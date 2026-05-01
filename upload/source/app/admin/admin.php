<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('MITFRAME_APP')) {
	exit('Access Denied');
}

const IN_ADMINCP = true;
const NOROBOT = true;
const HOOKTYPE = 'hookscript';
const APPTYPEID = 0;


require './source/class/class_core.php';
$discuz = C::app();
$discuz->init_cron = false;
$discuz->cachelist = ['admin'];
$discuz->init();


const ADMINSCRIPT = '?app=admin&platform='.PLATFORM;

require_once './source/function/function_misc.php';
require_once './source/function/function_forum.php';
require_once './source/function/function_admincp.php';
require_once './source/function/function_cache.php';


$admincp = new admin\class_core();
$admincp->core = &$discuz;
$admincp->init();

$action = preg_replace('/[^\[A-Za-z0-9_\]]/', '', getgpc('action'));
$operation = preg_replace('/[^\[A-Za-z0-9_:\]]/', '', getgpc('operation'));
$do = preg_replace('/[^\[A-Za-z0-9_\]]/', '', getgpc('do'));
$frames = preg_replace('/[^\[A-Za-z0-9_\]]/', '', getgpc('frames'));
lang('admincp');
$lang = &$_G['lang']['admincp'];
$page = max(1, intval(getgpc('page')));
$isfounder = $admincp->isfounder;

if(empty($action) || $frames != null) {
	$admincp->show_admincp_main();
} elseif($action == 'logout') {
	$method = $_G['config']['admincp']['logout']['method'] ?? 'default';
	if($method != 'default' && ($f = childfile('adminlogout/'.$method, 'global'))) {
		require_once $f;
	} else {
		$admincp->do_admin_logout();
		dheader('Location: ./index.php');
	}
} elseif(($admincp->allow($action, $operation, $do) || $action == 'index') && ($f = $admincp->admincpfile($action))) {
	require_once $f;
} else {
	cpheader();
	if($action == 'cloudaddons') {
		cpmsg('cloudaddons_noaccess', '', 'error');
	} else {
		cpmsg('action_noaccess', '', 'error');
	}
}
