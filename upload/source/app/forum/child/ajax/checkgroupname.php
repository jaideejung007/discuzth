<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$groupname = trim($_GET['groupname']);
if(empty($groupname)) {
	showmessage('group_name_empty', '', [], ['msgtype' => 3]);
}
$tmpname = cutstr($groupname, 20, '');
if($tmpname != $groupname) {
	showmessage('group_name_oversize', '', [], ['msgtype' => 3]);
}
if(table_forum_forum::t()->fetch_fid_by_name($groupname)) {
	showmessage('group_name_exist', '', [], ['msgtype' => 3]);
}
showmessage('', '', [], ['msgtype' => 3]);
include template('common/header_ajax');
include template('common/footer_ajax');
dexit();
	