<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

const NOROBOT = TRUE;

if(!in_array($_GET['action'], ['login', 'logout', 'login_mobile'])) {
	showmessage('undefined_action');
}

$ctl_obj = new logging_ctl();
$ctl_obj->setting = $_G['setting'];
$method = 'on_'.$_GET['action'];
$ctl_obj->template = in_array($_GET['action'], ['login', 'logout']) ? 'member/login' : 'member/login_mobile';
$ctl_obj->$method();

