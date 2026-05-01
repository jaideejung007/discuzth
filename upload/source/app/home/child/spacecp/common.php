<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$op = empty($_GET['op']) ? '' : trim($_GET['op']);

if($op == 'ignore') {

	$type = empty($_GET['type']) ? '' : preg_replace('/[^0-9a-zA-Z\_\-\.]/', '', $_GET['type']);
	$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
	if(submitcheck('ignoresubmit')) {
		$authorid = empty($_POST['authorid']) ? 0 : intval($_POST['authorid']);
		if($type) {
			$type_uid = $type.'|'.$authorid;
			if(empty($space['privacy']['filter_note']) || !is_array($space['privacy']['filter_note'])) {
				$space['privacy']['filter_note'] = [];
			}
			$space['privacy']['filter_note'][$type_uid] = $type_uid;
			privacy_update();
		}
		showmessage('do_success', dreferer(), ['id' => $id, 'type' => $type, 'uid' => $authorid], ['showdialog' => 1, 'showmsg' => true, 'closetime' => true]);
	}
	$formid = random(8);

} elseif($op == 'closefeedbox') {

	dsetcookie('closefeedbox', 1);

} elseif($op == 'modifyunitprice') {
	if(!$_G['setting']['ranklist']['membershow']) {
		exit('Access Denied');
	}
	$showinfo = table_home_show::t()->fetch($_G['uid']); 
	if(submitcheck('modifysubmit')) {
		$unitprice = intval($_POST['unitprice']);
		if($unitprice < 1) {
			showmessage('showcredit_error', '', [], ['return' => 1]);
		}
		$unitprice = $unitprice > $showinfo['credit'] ? $showinfo['credit'] : $unitprice;
		table_home_show::t()->update($_G['uid'], ['unitprice' => $unitprice]);

		showmessage('do_success', dreferer(), ['unitprice' => $unitprice], ['showdialog' => 1, 'showmsg' => true, 'closetime' => true]);
	}
}

include template('home/spacecp_common');

