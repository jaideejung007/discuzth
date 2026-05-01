<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($_G['adminid'] != 1) {
	cpmsg('undefined_action', '', 'error');
}

if(submitcheck('resetsubmit')) {
	admin\widget_setting::reset();
	cpmsg('widget_reset_succeed', 'action=index', 'succeed');
}

if(!submitcheck('submit') || empty($_GET['pos']) || !isset($_GET['pos']['left']) || !isset($_GET['pos']['right'])) {
	cpmsg('undefined_action', '', 'error');
}

admin\widget_setting::set();

cpmsg('widget_update_succeed', 'action=index', 'succeed');