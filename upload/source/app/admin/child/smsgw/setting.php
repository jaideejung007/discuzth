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
	
	$smsstatus = (int)$_GET['smsstatusnew'];
	
	$smsdefaultcc = (int)$_GET['smsdefaultccnew'];
	$smsdefaultcc = $smsdefaultcc > 0 ? $smsdefaultcc : 86;
	
	$smssupportedcc = $_GET['smssupportedccnew'];
	
	$smsdefaultlength = (int)$_GET['smsdefaultlengthnew'];
	$smsdefaultlength = $smsdefaultlength > 0 ? $smsdefaultlength : 4;
	
	$smstimelimit = (int)$_GET['smstimelimitnew'];
	$smstimelimit = $smstimelimit > 0 ? $smstimelimit : 86400;
	
	$smsnumlimit = (int)$_GET['smsnumlimitnew'];
	$smsnumlimit = $smsnumlimit > 0 ? $smsnumlimit : 5;
	
	$smsinterval = (int)$_GET['smsintervalnew'];
	$smsinterval = $smsinterval > 0 ? $smsinterval : 300;
	
	$smsmillimit = (int)$_GET['smsmillimitnew'];
	$smsmillimit = $smsmillimit > 0 ? $smsmillimit : 20;
	
	$smsglblimit = (int)$_GET['smsglblimitnew'];
	$smsglblimit = $smsglblimit > 0 ? $smsglblimit : 1000;
	
	$smsverifylimit = (int)$_GET['smsverifylimitnew'];
	$smsverifylimit = $smsverifylimit > 0 ? $smsverifylimit : 5;
	
	$smsmobileblacklist = $_GET['smsmobileblacklist'];
	
	$smsmobilesegmentblacklist = $_GET['smsmobilesegmentblacklist'];
	
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
	
	$smsstatus = table_common_setting::t()->fetch_setting('smsstatus');
	
	$smsdefaultcc = table_common_setting::t()->fetch_setting('smsdefaultcc');
	
	$smssupportedcc = table_common_setting::t()->fetch_setting('smssupportedcc');
	
	$smsdefaultlength = table_common_setting::t()->fetch_setting('smsdefaultlength');
	
	$smstimelimit = table_common_setting::t()->fetch_setting('smstimelimit');
	
	$smsnumlimit = table_common_setting::t()->fetch_setting('smsnumlimit');
	
	$smsinterval = table_common_setting::t()->fetch_setting('smsinterval');
	
	$smsmillimit = table_common_setting::t()->fetch_setting('smsmillimit');
	
	$smsglblimit = table_common_setting::t()->fetch_setting('smsglblimit');
	
	$smsverifylimit = table_common_setting::t()->fetch_setting('smsverifylimit');
	
	$smsmobileblacklist = table_common_setting::t()->fetch_setting('smsmobileblacklist');
	
	$smsmobilesegmentblacklist = table_common_setting::t()->fetch_setting('smsmobilesegmentblacklist');
	
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
	