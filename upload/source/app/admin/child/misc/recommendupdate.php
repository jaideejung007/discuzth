<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

ob_clean();
define('FOOTERDISABLED', 1);

require_once libfile('function/cloudaddons');

$recommendaddon = json_decode(cloudaddons_recommendaddon($addonids), true);
if(empty($recommendaddon) || !is_array($recommendaddon)) {
	$recommendaddon = [];
}
$recommendaddon['updatetime'] = $_G['timestamp'];
table_common_setting::t()->update('cloudaddons_recommendaddon', $recommendaddon);
updatecache('setting');