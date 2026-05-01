<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_G['setting']['feedday'] < 3) $_G['setting']['feedday'] = 3;
$deltime = $_G['timestamp'] - $_G['setting']['feedday'] * 3600 * 24;
$f_deltime = $_G['timestamp'] - $_G['setting']['feedday'] * 3600 * 24;

table_home_feed::t()->delete_by_dateline($deltime);
table_home_feed::t()->optimize_table();

