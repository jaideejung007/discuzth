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
	// 短信发送前先校验安全手机号是否正确, 避免错误安全手机号送往短信网关
	if(empty($secmobicc) || !preg_match('#^(\d){1,3}$#', $secmobicc)) {
		cpmsg_error('smsgw_send_test_secmobicc_error');
	} else if(empty($secmobile) || !preg_match('#^(\d){1,12}$#', $secmobile)) {
		cpmsg_error('smsgw_send_test_secmobile_error');
	}

	$result = sms::send($_G['uid'], 0, 1, $secmobicc, $secmobile, random(6, 1), 0);

	// 发送时间短于设置返回 -1, 单号码发送次数风控规则不通过返回 -2, 万号段风控规则不通过返回 -3, 全局风控规则不通过返回 -4, 无可用网关返回 -5, 网关接口文件不存在返回 -6,
	// 网关接口类不存在返回 -7, 短信功能已被关闭返回 -8, 短信网关私有异常返回 -9
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
	