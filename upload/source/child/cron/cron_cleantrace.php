<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$maxday = 90;
$deltime = $_G['timestamp'] - $maxday * 3600 * 24;

table_home_clickuser::t()->delete_by_dateline($deltime);

table_home_visitor::t()->delete_by_dateline($deltime);

