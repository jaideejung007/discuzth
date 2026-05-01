<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!submitcheck('medalsubmit')) {
	showmessage('undefined_action');
}
$medalid = intval($_GET['medalid']);
$_G['forum_formulamessage'] = $_G['forum_usermsg'] = $medalnew = '';
$medal = table_forum_medal::t()->fetch($medalid);
if(!$medal['type']) {
	showmessage('medal_apply_invalid');
}

if(table_common_member_medal::t()->count_by_uid_medalid($_G['uid'], $medalid)) {
	showmessage('medal_apply_existence', 'home.php?mod=medal');
}

$applysucceed = FALSE;
$medalpermission = $medal['permission'] ? dunserialize($medal['permission']) : [];
if($medalpermission[0] || $medalpermission['usergroupallow']) {
	include libfile('function/forum');
	medalformulaperm(serialize(['medal' => $medalpermission]), $medal['type']);

	if($_G['forum_formulamessage']) {
		showmessage('medal_permforum_nopermission', 'home.php?mod=medal', ['formulamessage' => $_G['forum_formulamessage'], 'usermsg' => $_G['forum_usermsg']]);
	} else {
		$applysucceed = TRUE;
	}
} else {
	$applysucceed = TRUE;
}

if($applysucceed) {
	$expiration = empty($medal['expiration']) ? 0 : TIMESTAMP + $medal['expiration'] * 86400;
	if($medal['type'] == 1) {
		if($medal['price']) {
			$medal['credit'] = $medal['credit'] ? $medal['credit'] : $_G['setting']['creditstransextra'][3];
			if($medal['price'] > getuserprofile('extcredits'.$medal['credit'])) {
				showmessage('medal_not_get_credit', '', ['credit' => $_G['setting']['extcredits'][$medal['credit']]['title']]);
			}
			updatemembercount($_G['uid'], [$medal['credit'] => -$medal['price']], true, 'BME', $medal['medalid']);
		}

		$memberfieldforum = table_common_member_field_forum::t()->fetch($_G['uid']);
		$usermedal = $memberfieldforum;
		unset($memberfieldforum);
		$medal['medalid'] = $medal['medalid'].(empty($expiration) ? '' : '|'.$expiration);
		$medalnew = $usermedal['medals'] ? $usermedal['medals']."\t".$medal['medalid'] : $medal['medalid'];
		table_common_member_field_forum::t()->update($_G['uid'], ['medals' => $medalnew]);
		table_common_member_medal::t()->insert(['uid' => $_G['uid'], 'medalid' => $medal['medalid']], 0, 1);
		$medalmessage = 'medal_get_succeed';
	} else {
		if(table_forum_medallog::t()->count_by_verify_medalid($_G['uid'], $medal['medalid'])) {
			showmessage('medal_apply_existence', 'home.php?mod=medal');
		}
		$medalmessage = 'medal_apply_succeed';
		manage_addnotify('verifymedal');
	}

	table_forum_medallog::t()->insert([
		'uid' => $_G['uid'],
		'medalid' => $medalid,
		'type' => $medal['type'],
		'dateline' => TIMESTAMP,
		'expiration' => $expiration,
		'status' => ($expiration ? 1 : 0),
	]);
	showmessage($medalmessage, 'home.php?mod=medal', ['medalname' => $medal['name']]);
}
	