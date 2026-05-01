<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class payment_invite {

	public function callback($data, $order) {
		global $_G;
		table_forum_order::t()->insert([
			'orderid' => $order['out_biz_no'],
			'status' => '2',
			'buyer' => $order['id'],
			'uid' => 0,
			'admin' => 0,
			'amount' => $data['num'],
			'price' => $order['amount'] / 100,
			'submitdate' => $_G['timestamp'],
			'email' => $data['email'],
			'confirmdate' => $order['payment_time'],
			'ip' => $data['ip'],
			'port' => $data['port'],
		]);

		$codes = $codetext = [];
		$dateline = TIMESTAMP;
		for($i = 0; $i < $data['num']; $i++) {
			$code = strtolower(random(6));
			$codetext[] = $code;
			$codes[] = "('0', '$code', '$dateline', '".($_G['group']['maxinviteday'] ? ($_G['timestamp'] + $_G['group']['maxinviteday'] * 24 * 3600) : $_G['timestamp'] + 86400 * 10)."', '{$data['email']}', '{$data['ip']}', '{$order['out_biz_no']}')";
			$invitedata = [
				'uid' => 0,
				'code' => $code,
				'dateline' => $dateline,
				'endtime' => $_G['group']['maxinviteday'] ? ($_G['timestamp'] + $_G['group']['maxinviteday'] * 24 * 3600) : $_G['timestamp'] + 86400 * 10,
				'email' => $data['email'],
				'inviteip' => $data['ip'],
				'orderid' => $order['out_biz_no']
			];
			table_common_invite::t()->insert($invitedata);
		}

		if(!function_exists('sendmail')) {
			include libfile('function/mail');
		}
		$invite_payment_subject = [
			'tpl' => 'invite_payment',
			'var' => [
				'orderid' => $order['out_biz_no'],
				'codetext' => implode('<br />', $codetext),
				'siteurl' => $_G['siteurl'],
				'bbname' => $_G['setting']['bbname'],
			]
		];
		if(!sendmail($data['email'], $invite_payment_subject)) {
			runlog('sendmail', "{$data['email']} sendmail failed.");
		}
	}

}

