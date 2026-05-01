<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$_POST['grid']['fids'] = in_array(0, $_POST['grid']['fids']) ? [0] : $_POST['grid']['fids'];
table_common_setting::t()->update_setting('grid', $_POST['grid']);
updatecache('setting');
table_common_syscache::t()->delete_syscache('grids');
cpmsg('setting_update_succeed', 'action=grid', 'succeed');
	