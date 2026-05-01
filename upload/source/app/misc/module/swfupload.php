<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$_G['uid'] = intval($_POST['uid']);

if(defined('IN_RESTFUL') && empty($_FILES)) {
	echo getHash();
	exit;
}

if((empty($_G['uid']) && $_GET['operation'] != 'upload') || $_POST['hash'] != getHash()) {
	exit();
} else {
	if($_G['uid']) {
		$_G['member'] = getuserbyuid($_G['uid']);
	}
	$_G['groupid'] = $_G['member']['groupid'];
	loadcache('usergroup_'.$_G['member']['groupid']);
	$_G['group'] = $_G['cache']['usergroup_'.$_G['member']['groupid']];
}

$file = childfile($_GET['operation']);
if(file_exists($file)) {
	require_once $file;
}

function getHash() {
	global $_G;
	return md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid']);
}