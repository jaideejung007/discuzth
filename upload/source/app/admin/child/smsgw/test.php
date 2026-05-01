<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('testsubmit')) {

	shownav('extended', 'smsgw_admin');
	showsubmenu('smsgw_admin', [
		['smsgw_admin_setting', 'smsgw&operation=setting', 0],
		['smsgw_admin_list', 'smsgw&operation=list', 0],
		['smsgw_admin_test', 'smsgw&operation=test', 1]
	]);
	showformheader("smsgw&operation=$operation", 'enctype');
	showtableheader(cplang('smsgw_send_test'), 'fixpadding');

	showsetting('smsgw_send_test_secmobicc', 'secmobicc', '86', 'text');
	showsetting('smsgw_send_test_secmobile', 'secmobile', '', 'text');

	showsubmit('testsubmit');
	showtablefooter();
	showformfooter();

} else {

	$secmobicc = $_GET['secmobicc'];
	$secmobile = $_GET['secmobile'];
	
	if(empty($secmobicc) || !preg_match('#^(\d){1,3}$#', $secmobicc)) {
		cpmsg_error('smsgw_send_test_secmobicc_error');
	} else if(empty($secmobile) || !preg_match('#^(\d){1,12}$#', $secmobile)) {
		cpmsg_error('smsgw_send_test_secmobile_error');
	}

	$result = sms::send($_G['uid'], 0, 1, $secmobicc, $secmobile, random(6, 1), 0);

	
	
	if(is_string($result)) {
		cpmsg('smsgw_send_test_failure', '', [$result]);
	} else if($result >= 0) {
		cpmsg('smsgw_send_test_success', '', [], ['alert' => 'right']);
	} else {
		if($result <= -1 && $result >= -9) {
			cpmsg('smsgw_send_test_err_'.abs($result));
		} else {
			cpmsg('smsgw_send_test_failure');
		}
	}

}
	