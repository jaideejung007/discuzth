<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['magicstatus']) {
	showmessage('magics_close');
}

$space['credit'] = $space['credits'];

$op = empty($_GET['op']) ? 'view' : $_GET['op'];
$mid = empty($_GET['mid']) ? '' : trim($_GET['mid']);

if(!checkperm('allowmagics')) {
	showmessage('magic_groupid_not_allowed');
}

if($op == 'cancelflicker') {

	$mid = 'flicker';
	$_GET['idtype'] = 'cid';
	$_GET['id'] = intval($_GET['id']);
	$value = table_home_comment::t()->fetch_comment($_GET['id'], $_G['uid']);
	if(!$value || !$value['magicflicker']) {
		showmessage('no_flicker_yet');
	}

	if(submitcheck('cancelsubmit')) {
		table_home_comment::t()->update_comment('', ['magicflicker' => 0], $_G['uid']);
		showmessage('do_success', dreferer(), [], ['showdialog' => 1, 'closetime' => true]);
	}

} elseif($op == 'cancelcolor') {

	$mid = 'color';
	$_GET['id'] = intval($_GET['id']);
	$mapping = ['blogid' => 'blogfield', 'tid' => 'thread'];
	$tablename = $mapping[$_GET['idtype']];
	if(empty($tablename)) {
		showmessage('no_color_yet');
	}
	$value = C::t($tablename)->fetch($_GET['id']);
	if(!$value || $value['uid'] != $_G['uid'] || !$value['magiccolor']) {
		showmessage('no_color_yet');
	}

	if(submitcheck('cancelsubmit')) {
		DB::update($tablename, ['magiccolor' => 0], [$_GET['idtype'] => $_GET['id']]);
		$feed = table_home_feed::t()->fetch_feed($_GET['id'], $_GET['idtype']);
		if($feed) {
			$feed['body_data'] = dunserialize($feed['body_data']);
			if($feed['body_data']['magic_color']) {
				unset($feed['body_data']['magic_color']);
			}
			$feed['body_data'] = serialize($feed['body_data']);
			table_home_feed::t()->update_feed('', ['body_data' => $feed['body_data']], '', '', $feed['feedid']);
		}
		showmessage('do_success', dreferer(), 0);
	}

} elseif($op == 'receivegift') {

	$uid = intval($_GET['uid']);
	$mid = 'gift';
	$memberfieldhome = table_common_member_field_home::t()->fetch($uid);
	$info = $memberfieldhome['magicgift'] ? dunserialize($memberfieldhome['magicgift']) : [];
	unset($memberfieldhome);
	if(!empty($info['left'])) {
		$info['receiver'] = is_array($info['receiver']) ? $info['receiver'] : [];
		if(in_array($_G['uid'], $info['receiver'])) {
			showmessage('haved_red_bag');
		}
		$percredit = min($info['left'], $info['percredit']);
		$info['receiver'][] = $_G['uid'];
		$info['left'] = $info['left'] - $percredit;
		table_common_member_field_home::t()->update($uid, ['magicgift' => ($info['left'] > 0 ? serialize($info) : '')]);
		$credittype = '';
		if(preg_match('/^extcredits[1-8]$/', $info['credittype'])) {
			$extcredits = str_replace('extcredits', '', $info['credittype']);
			updatemembercount($_G['uid'], [$extcredits => $percredit], 1, 'AGC', $info['magicid']);
			$credittype = $_G['setting']['extcredits'][$extcredits]['title'];
		}
		showmessage('haved_red_bag_gain', dreferer(), ['percredit' => $percredit, 'credittype' => $credittype], ['showdialog' => 1, 'locationtime' => true]);
	}
	showmessage('space_no_red_bag', dreferer(), [], ['showdialog' => 1, 'locationtime' => true]);

} elseif($op == 'retiregift') {

	$mid = 'gift';
	$memberfieldhome = table_common_member_field_home::t()->fetch($_G['uid']);
	$info = $memberfieldhome['magicgift'] ? dunserialize($memberfieldhome['magicgift']) : [];
	unset($memberfieldhome);
	$leftcredit = intval($info['left']);
	if($leftcredit <= 0) {
		table_common_member_field_home::t()->update($_G['uid'], ['magicgift' => '']);
		showmessage('red_bag_no_credits');
	}

	$extcredits = str_replace('extcredits', '', $info['credittype']);
	$credittype = $_G['setting']['extcredits'][$extcredits]['title'];

	if(submitcheck('cancelsubmit')) {
		table_common_member_field_home::t()->update($_G['uid'], ['magicgift' => '']);
		if(preg_match('/^extcredits[1-8]$/', $info['credittype'])) {
			updatemembercount($_G['uid'], [$extcredits => $leftcredit], 1, 'RGC', $info['magicid']);
		}
		showmessage('return_red_bag', dreferer(), ['leftcredit' => $leftcredit, 'credittype' => $credittype], ['showdialog' => 1, 'locationtime' => true]);
	}
}

include_once template('home/spacecp_magic');

