<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['blogstatus']) {
	showmessage('blog_status_off');
}

$classid = empty($_GET['classid']) ? 0 : intval($_GET['classid']);
$op = empty($_GET['op']) ? '' : $_GET['op'];

$class = [];
if($classid) {
	$class = table_home_class::t()->fetch($classid);
	if($class['uid'] != $_G['uid']) {
		$class = null;
	}
}
if(empty($class)) showmessage('did_not_specify_the_type_of_operation');

if($op == 'edit') {

	if(submitcheck('editsubmit')) {

		$_POST['classname'] = getstr($_POST['classname'], 40);
		$_POST['classname'] = censor($_POST['classname']);
		if(strlen($_POST['classname']) < 1) {
			showmessage('enter_the_correct_class_name');
		}
		table_home_class::t()->update($classid, ['classname' => $_POST['classname']]);
		showmessage('do_success', dreferer(), ['classid' => $classid, 'classname' => $_POST['classname']], ['showdialog' => 1, 'showmsg' => true, 'closetime' => true]);
	}

} elseif($op == 'delete') {
	if(submitcheck('deletesubmit')) {
		table_home_blog::t()->update_classid_by_classid($classid, 0);
		table_home_class::t()->delete($classid);

		showmessage('do_success', dreferer());
	}
}

include_once template('home/spacecp_class');

