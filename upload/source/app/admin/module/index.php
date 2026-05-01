<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

@include_once DISCUZ_ROOT.'./source/discuz_version.php';

if(isset($_GET['blank'])) {
	define('FOOTERDISABLED', true);
	cpheader();
	require_once template('admin/blank');
	exit;
}

if(!empty($_GET['chart'])) {
	define('FOOTERDISABLED', true);
	require_once childfile('index/chart');
}

require_once childfile('index/function');

$sensitivedirs = ['./', './uc_server/', './ucenter/'];

foreach($sensitivedirs as $sdir) {
	if(@file_exists(DISCUZ_ROOT.$sdir.'install/index.php') && !DISCUZ_DEBUG) {
		@unlink(DISCUZ_ROOT.$sdir.'install/index.php');
		if(@file_exists(DISCUZ_ROOT.$sdir.'install/index.php')) {
			dexit('Please delete '.$sdir.'install/index.php via FTP!');
		}
	}
}

require_once libfile('function/attachment');
require_once libfile('function/discuzcode');

if(submitcheck('notesubmit', 1)) {
	if(!empty($_GET['noteid']) && is_numeric($_GET['noteid'])) {
		table_common_adminnote::t()->delete_note($_GET['noteid'], (isfounder() ? '' : $_G['username']));
	}
	if(!empty($_GET['newmessage'])) {
		$newaccess = 0;
		$_GET['newexpiration'] = TIMESTAMP + (intval($_GET['newexpiration']) > 0 ? intval($_GET['newexpiration']) : 30) * 86400;
		$_GET['newmessage'] = nl2br(dhtmlspecialchars($_GET['newmessage']));
		$data = [
			'admin' => $_G['username'],
			'access' => 0,
			'adminid' => $_G['adminid'],
			'dateline' => $_G['timestamp'],
			'expiration' => $_GET['newexpiration'],
			'message' => $_GET['newmessage'],
		];
		table_common_adminnote::t()->insert($data);
	}
}

require_once libfile('function/cloudaddons');
$newversion = (CHARSET == 'utf-8') ? dunserialize($_G['setting']['cloudaddons_newversion']) : json_decode($_G['setting']['cloudaddons_newversion'], true);
if(empty($newversion['newversion']) || !is_array($newversion['newversion']) || abs($_G['timestamp'] - $newversion['updatetime']) > 86400 || (isset($_GET['checknewversion']) && $_G['formhash'] == $_GET['formhash'])) {
	$newversion = json_decode(cloudaddons_open('&mod=app&ac=upgrade'), true);
	if(!empty($newversion['newversion'])) {
		$newversion['updatetime'] = $_G['timestamp'];
		table_common_setting::t()->update_setting('cloudaddons_newversion', ((CHARSET == 'utf-8') ? $newversion : json_encode($newversion)));
		updatecache('setting');
	} else {
		$newversion = [];
	}
}

$reldisp = is_numeric(DISCUZ_RELEASE) ? ('Release '.DISCUZ_RELEASE) : DISCUZ_RELEASE;
$now = date('Y');

cpheader();
shownav();

show_user_bar();

require_once template('admin/index');
