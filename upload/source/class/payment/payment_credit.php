<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class payment_credit {

	public function callback($data, $order) {

		global $_G;
		table_forum_order::t()->insert([
			'orderid' => $order['out_biz_no'],
			'status' => '2',
			'buyer' => $order['id'],
			'admin' => 0,
			'uid' => $order['uid'],
			'amount' => $data['value'],
			'price' => $order['amount'] / 100,
			'submitdate' => $_G['timestamp'],
			'email' => $_G['member']['email'],
			'confirmdate' => $order['payment_time'],
			'ip' => $data['ip'],
			'port' => $data['port']
		]);
		updatemembercount($order['uid'], ['extcredits'.$data['index'] => $data['value']], 1, 'AFD', $order['uid']);
		table_forum_order::t()->delete_by_submitdate($_G['timestamp'] - 60 * 86400);

		$extcredits = $_G['setting']['extcredits'][$data['index']];
		notification_add($order['uid'], 'credit', 'addfunds', ['orderid' => $order['out_biz_no'], 'price' => $order['amount'] / 100, 'value' => trim($extcredits['title'].' '.$data['value'].' '.$extcredits['unit'])], 1);
	}

}

