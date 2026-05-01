<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['friendstatus']) {
	showmessage('friend_status_off');
}
if(!$_G['setting']['ranklist']['membershow']) {
	exit('Access Denied');
}

$operation = $_GET['op'] == 'modify' ? trim($_GET['op']) : '';
if($_G['setting']['creditstransextra'][6]) {
	$key = 'extcredits'.intval($_G['setting']['creditstransextra'][6]);
} elseif($_G['setting']['creditstrans']) {
	$key = 'extcredits'.intval($_G['setting']['creditstrans']);
} else {
	showmessage('trade_credit_invalid', '', [], ['return' => 1]);
}
space_merge($space, 'count');

if(submitcheck('friendsubmit')) {

	$showcredit = intval($_POST['stakecredit']);
	if($showcredit > $space[$key]) $showcredit = $space[$key];
	if($showcredit < 1) {
		showmessage('showcredit_error');
	}

	$_POST['fusername'] = trim($_POST['fusername']);
	$friend = table_home_friend::t()->fetch_all_by_uid_username($space['uid'], $_POST['fusername'], 0, 1);
	$friend = $friend[0];
	$fuid = $friend['fuid'];
	if(empty($_POST['fusername']) || empty($fuid) || $fuid == $space['uid']) {
		showmessage('showcredit_fuid_error', '', [], ['return' => 1]);
	}

	$count = getcount('home_show', ['uid' => $fuid]);
	if($count) {
		table_home_show::t()->update_credit_by_uid($fuid, $showcredit, false);
	} else {
		table_home_show::t()->insert(['uid' => $fuid, 'username' => $_POST['fusername'], 'credit' => $showcredit], false, true);
	}

	updatemembercount($space['uid'], [$_G['setting']['creditstransextra'][6] => (0 - $showcredit)], true, 'RKC', $space['uid']);

	notification_add($fuid, 'credit', 'showcredit', ['credit' => $showcredit]);


	if(ckprivacy('show', 'feed')) {
		require_once libfile('function/feed');
		feed_add('show', 'feed_showcredit', [
			'fusername' => "<a href=\"home.php?mod=space&uid=$fuid\">{$friend['fusername']}</a>",
			'credit' => $showcredit]);
	}

	showmessage('showcredit_friend_do_success', 'misc.php?mod=ranklist&type=member');

} elseif(submitcheck('showsubmit')) {

	$showcredit = intval($_POST['showcredit']);
	$unitprice = intval($_POST['unitprice']);
	if($showcredit > $space[$key]) $showcredit = $space[$key];
	if($showcredit < 1 || $unitprice < 1) {
		showmessage('showcredit_error', '', [], ['return' => 1]);
	}
	$_POST['note'] = getstr($_POST['note'], 100);
	$_POST['note'] = censor($_POST['note']);
	$showarr = table_home_show::t()->fetch($_G['uid']);
	if($showarr) {
		$notesql = $_POST['note'] ? $_POST['note'] : false;
		$unitprice = $unitprice > $showarr['credit'] + $showcredit ? $showarr['credit'] + $showcredit : $unitprice;
		table_home_show::t()->update_credit_by_uid($_G['uid'], $showcredit, false, $unitprice, $notesql);
	} else {
		$unitprice = $unitprice > $showcredit ? $showcredit : $unitprice;
		table_home_show::t()->insert(['uid' => $_G['uid'], 'username' => $_G['username'], 'unitprice' => $unitprice, 'credit' => $showcredit, 'note' => $_POST['note']], false, true);
	}

	updatemembercount($space['uid'], [$_G['setting']['creditstransextra'][6] => (0 - $showcredit)], true, 'RKC', $space['uid']);

	if(ckprivacy('show', 'feed')) {
		require_once libfile('function/feed');
		feed_add('show', 'feed_showcredit_self', ['credit' => $showcredit], '', [], $_POST['note']);
	}

	showmessage('showcredit_do_success', dreferer());
}
