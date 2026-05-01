<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

const IN_API = true;
const CURSCRIPT = 'api';
const DISABLEXSSCHECK = true;

require '../../../source/class/class_core.php';

$discuz = C::app();
$discuz->init();

if(!$_POST['sign'] || !$_POST['sign_type']) {
	exit('fail');
}
$sign = $_POST['sign'];
unset($_POST['sign']);

$payment = new pay_alipay();
$isright = $payment->alipay_sign_verify($sign, $_POST);
if(!$isright) {
	$_POST['sign'] = $sign;
	payment::paymentlog('alipay', 0, 0, 0, 50001, $_POST);
	exit('fail');
}

if($_POST['trade_status'] == 'TRADE_SUCCESS') {
	$out_biz_no = $_POST['out_trade_no'];
	$payment_time = strtotime($_POST['gmt_payment']);

	$is_success = payment::finish_order('alipay', $out_biz_no, $_POST['trade_no'], $payment_time);
	if($is_success) {
		exit('success');
	}
} else {
	payment::paymentlog('alipay', 0, 0, 0, 50001, $_POST);
}

exit('fail');

