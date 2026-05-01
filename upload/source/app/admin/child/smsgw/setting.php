<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('smsgwsubmit')) {
	// 是否开启 SMS
	$smsstatus = (int)$_GET['smsstatusnew'];
	// 默认国际电话区号, 默认 86
	$smsdefaultcc = (int)$_GET['smsdefaultccnew'];
	$smsdefaultcc = $smsdefaultcc > 0 ? $smsdefaultcc : 86;
	// 支持的国际区号, 每行一项
	$smssupportedcc = $_GET['smssupportedccnew'];
	// 默认短信验证码长度, 默认 4
	$smsdefaultlength = (int)$_GET['smsdefaultlengthnew'];
	$smsdefaultlength = $smsdefaultlength > 0 ? $smsdefaultlength : 4;
	// 限制时间区间, 默认 86400 秒
	$smstimelimit = (int)$_GET['smstimelimitnew'];
	$smstimelimit = $smstimelimit > 0 ? $smstimelimit : 86400;
	// 单用户/单号码短信限制时间区间内总量, 默认 5 条
	$smsnumlimit = (int)$_GET['smsnumlimitnew'];
	$smsnumlimit = $smsnumlimit > 0 ? $smsnumlimit : 5;
	// 单用户/单号码短信时间间隔, 默认 300 秒
	$smsinterval = (int)$_GET['smsintervalnew'];
	$smsinterval = $smsinterval > 0 ? $smsinterval : 300;
	// 万号段短信限制时间区间内总量, 默认 20 条
	$smsmillimit = (int)$_GET['smsmillimitnew'];
	$smsmillimit = $smsmillimit > 0 ? $smsmillimit : 20;
	// 全局短信限制时间区间内总量, 默认 1000 条
	$smsglblimit = (int)$_GET['smsglblimitnew'];
	$smsglblimit = $smsglblimit > 0 ? $smsglblimit : 1000;
	// 短信验证码有效验证次数, 默认 5 次
	$smsverifylimit = (int)$_GET['smsverifylimitnew'];
	$smsverifylimit = $smsverifylimit > 0 ? $smsverifylimit : 5;
	// 手机号段黑名单, 英文逗号分隔
	$smsmobileblacklist = $_GET['smsmobileblacklist'];
	// 手机号段黑名单, 英文逗号分隔
	$smsmobilesegmentblacklist = $_GET['smsmobilesegmentblacklist'];
	// 手机号段白名单, 英文逗号分隔
	$smsmobilesegmentwhitelist = $_GET['smsmobilesegmentwhitelist'];

	table_common_setting::t()->update_setting('smsstatus', $smsstatus);
	table_common_setting::t()->update_setting('smsdefaultcc', $smsdefaultcc);
	table_common_setting::t()->update_setting('smssupportedcc', $smssupportedcc);
	table_common_setting::t()->update_setting('smsdefaultlength', $smsdefaultlength);
	table_common_setting::t()->update_setting('smstimelimit', $smstimelimit);
	table_common_setting::t()->update_setting('smsnumlimit', $smsnumlimit);
	table_common_setting::t()->update_setting('smsinterval', $smsinterval);
	table_common_setting::t()->update_setting('smsmillimit', $smsmillimit);
	table_common_setting::t()->update_setting('smsglblimit', $smsglblimit);
	table_common_setting::t()->update_setting('smsverifylimit', $smsverifylimit);
	table_common_setting::t()->update_setting('smsmobileblacklist', $smsmobileblacklist);
	table_common_setting::t()->update_setting('smsmobilesegmentblacklist', $smsmobilesegmentblacklist);
	C::t('common_setting')->update_setting('smsmobilesegmentwhitelist', $smsmobilesegmentwhitelist);

	updatecache('setting');

	cpmsg('setting_update_succeed', 'action=smsgw&operation=setting', 'succeed');
} else {
	shownav('extended', 'smsgw_admin');
	showsubmenu('smsgw_admin', [
		['smsgw_admin_setting', 'smsgw&operation=setting', 1],
		['smsgw_admin_list', 'smsgw&operation=list', 0],
		['smsgw_admin_test', 'smsgw&operation=test', 0]
	]);
	// 是否开启 SMS
	$smsstatus = table_common_setting::t()->fetch_setting('smsstatus');
	// 默认国际区号, 默认 86
	$smsdefaultcc = table_common_setting::t()->fetch_setting('smsdefaultcc');
	// 支持的国际区号, 每行一项
	$smssupportedcc = table_common_setting::t()->fetch_setting('smssupportedcc');
	// 默认短信验证码长度, 默认 4
	$smsdefaultlength = table_common_setting::t()->fetch_setting('smsdefaultlength');
	// 限制时间区间, 默认 86400 秒
	$smstimelimit = table_common_setting::t()->fetch_setting('smstimelimit');
	// 单用户/单号码短信限制时间区间内总量, 默认 5 条
	$smsnumlimit = table_common_setting::t()->fetch_setting('smsnumlimit');
	// 单用户/单号码短信时间间隔, 默认 300 秒
	$smsinterval = table_common_setting::t()->fetch_setting('smsinterval');
	// 万号段短信限制时间区间内总量, 默认 20 条
	$smsmillimit = table_common_setting::t()->fetch_setting('smsmillimit');
	// 全局短信限制时间区间内总量, 默认 1000 条
	$smsglblimit = table_common_setting::t()->fetch_setting('smsglblimit');
	// 短信验证码有效验证次数, 默认 5 次
	$smsverifylimit = table_common_setting::t()->fetch_setting('smsverifylimit');
	// 手机号黑名单
	$smsmobileblacklist = table_common_setting::t()->fetch_setting('smsmobileblacklist');
	// 手机号段黑名单
	$smsmobilesegmentblacklist = table_common_setting::t()->fetch_setting('smsmobilesegmentblacklist');
	// 手机号段白名单
	$smsmobilesegmentwhitelist = C::t('common_setting')->fetch_setting('smsmobilesegmentwhitelist');

	showformheader("smsgw&operation=$operation");
	showtableheader();
	showsetting('smsgw_setting_smsstatus', 'smsstatusnew', $smsstatus, 'radio', 0, 1);
	showsetting('smsgw_setting_smsdefaultcc', 'smsdefaultccnew', $smsdefaultcc, 'text');
	showsetting('smsgw_setting_smssupportedcc', 'smssupportedccnew', $smssupportedcc, 'textarea');
	showsetting('smsgw_setting_smsdefaultlength', 'smsdefaultlengthnew', $smsdefaultlength, 'text');
	showsetting('smsgw_setting_smstimelimit', 'smstimelimitnew', $smstimelimit, 'text');
	showsetting('smsgw_setting_smsnumlimit', 'smsnumlimitnew', $smsnumlimit, 'text');
	showsetting('smsgw_setting_smsinterval', 'smsintervalnew', $smsinterval, 'text');
	showsetting('smsgw_setting_smsmillimit', 'smsmillimitnew', $smsmillimit, 'text');
	showsetting('smsgw_setting_smsglblimit', 'smsglblimitnew', $smsglblimit, 'text');
	showsetting('smsgw_setting_smsverifylimit', 'smsverifylimitnew', $smsverifylimit, 'text');
	showsetting('smsgw_setting_smsmobileblacklist', 'smsmobileblacklist', $smsmobileblacklist, 'textarea');
	showsetting('smsgw_setting_smsmobilesegmentblacklist', 'smsmobilesegmentblacklist', $smsmobilesegmentblacklist, 'textarea');
	showsetting('smsgw_setting_smsmobilesegmentwhitelist', 'smsmobilesegmentwhitelist', $smsmobilesegmentwhitelist, 'textarea');
	showtagfooter('tbody');
	showsubmit('smsgwsubmit');
	showtablefooter();
	showformfooter();
}
	