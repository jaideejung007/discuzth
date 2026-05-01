<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/misc');
include template('common/header_ajax');
if($_G['cookie']['lip'] && $_G['cookie']['lip'] != ',' && $_G['uid'] && getglobal('setting/disableipnotice') != 1) {
	$status = table_common_member_status::t()->fetch($_G['uid']);
	$lip = explode(',', $_G['cookie']['lip']);
	$lastipConvert = convertip($lip[0]);
	$lastipDate = dgmdate($lip[1]);
	$nowipConvert = convertip($status['lastip']);

	$lastipConvert = process_ipnotice($lastipConvert);
	$nowipConvert = process_ipnotice($nowipConvert);

	if($lastipConvert != $nowipConvert && stripos($lastipConvert, $nowipConvert) === false && stripos($nowipConvert, $lastipConvert) === false) {
		$lang = lang('forum/misc');
		include template('common/ipnotice');
	}
}
include template('common/footer_ajax');
exit;
	