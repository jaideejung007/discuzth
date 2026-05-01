<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

const IN_API = true;
const CURSCRIPT = 'api';
const DISABLEXSSCHECK = true;

require '../../source/class/class_core.php';
require '../../source/function/function_forum.php';

$discuz = C::app();
$discuz->init();

$apitype = empty($_GET['attach']) || !preg_match('/^[a-z0-9]+$/i', $_GET['attach']) ? 'alipay' : $_GET['attach'];
require_once DISCUZ_ROOT.'./api/trade/api_'.$apitype.'.php';

$PHP_SELF = $_SERVER['PHP_SELF'];
$_G['siteurl'] = dhtmlspecialchars($_G['scheme'].'://'.$_SERVER['HTTP_HOST'].preg_replace('/\/+(api\/trade)?\/*$/i', '', substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'))).'/');

$notifydata = trade_notifycheck('trade');

if($notifydata['validator']) {

	$orderid = $notifydata['order_no'];

	if($orderid) {

		$tradelog = table_forum_tradelog::t()->fetch($orderid);

		if($tradelog && $tradelog['status'] != STATUS_TRADE_SUCCESS && $tradelog['status'] != STATUS_REFUND_CLOSE && ($apitype == 'tenpay' || $tradelog['selleraccount'] == $_REQUEST['seller_email'])) {
			$status = $notifydata['status'];
			table_forum_tradelog::t()->update($orderid, [
				'status' => $status,
				'lastupdate' => $_G['timestamp'],
				'tradeno' => $notifydata['trade_no']
			]);
			if($status != $tradelog['status']) {

				if($status == STATUS_SELLER_SEND) {

					notification_add($tradelog['sellerid'], 'goods', 'trade_seller_send', [
						'buyerid' => $tradelog['buyerid'],
						'buyer' => $tradelog['buyer'],
						'orderid' => $orderid,
						'subject' => $tradelog['subject']
					]);

				} elseif($status == STATUS_WAIT_BUYER) {

					notification_add($tradelog['buyerid'], 'goods', 'trade_buyer_confirm', [
						'sellerid' => $tradelog['sellerid'],
						'seller' => $tradelog['seller'],
						'orderid' => $orderid,
						'subject' => $tradelog['subject']
					]);

				} elseif($status == STATUS_TRADE_SUCCESS) {

					if($_G['setting']['creditstransextra'][5] != -1 && $tradelog['basecredit']) {
						$netcredit = round($tradelog['number'] * $tradelog['basecredit'] * (1 - $_G['setting']['creditstax']));
						updatemembercount($tradelog['sellerid'], [$_G['setting']['creditstransextra'][5] => $netcredit]);
					} else {
						$netcredit = 0;
					}
					table_forum_trade::t()->update_trade($tradelog['tid'], $tradelog['pid'], ['lastbuyer' => $tradelog['buyer'], 'lastupdate' => $_G['timestamp']]);
					table_forum_trade::t()->update_counter($tradelog['tid'], $tradelog['pid'], $tradelog['number'], $tradelog['price'], $netcredit);

					updatecreditbyaction('tradefinished', $tradelog['sellerid']);
					updatecreditbyaction('tradefinished', $tradelog['buyerid']);

					notification_add($tradelog['sellerid'], 'goods', 'trade_success', [
						'orderid' => $orderid,
						'subject' => $tradelog['subject']
					]);
					notification_add($tradelog['buyerid'], 'goods', 'trade_success', [
						'orderid' => $orderid,
						'subject' => $tradelog['subject']
					]);

				} elseif($status == STATUS_REFUND_CLOSE) {

					table_forum_trade::t()->update_counter($tradelog['tid'], $tradelog['pid'], 0, 0, 0, $tradelog['number']);
					notification_add($tradelog['sellerid'], 'goods', 'trade_fefund_success', [
						'orderid' => $orderid,
						'subject' => $tradelog['subject']
					]);
					notification_add($tradelog['buyerid'], 'goods', 'trade_fefund_success', [
						'orderid' => $orderid,
						'subject' => $tradelog['subject']
					]);
					if($_G['setting']['creditstrans'] && $tradelog['buyerid']) {
						updatemembercount($tradelog['buyerid'], [$_G['setting']['creditstrans'] => $tradelog['buyercredits']]);
					}
					if($_G['setting']['creditstransextra'][5] != -1 && $tradelog['basecredit'] && $tradelog['buyerid']) {
						$credit = $tradelog['number'] * $tradelog['basecredit'];
						updatemembercount($tradelog['buyerid'], [$_G['setting']['creditstransextra'][5] => $credit]);
					}

				}
			}
		}
	}

}

if($notifydata['location']) {
	dheader('location: '.$_G['siteurl'].'forum.php?mod=misc&action=paysucceed&orderid='.$orderid);
} else {
	exit($notifydata['notify']);
}

