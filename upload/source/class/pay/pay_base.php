<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class pay_base {

	var $settings;
	var $notify_url;

	public function __construct() {
	}

	protected function enable() {
		if($this->settings && $this->settings['on']) {
			return true;
		} else {
			return false;
		}
	}

	public function pay($order) {
	}

	public function status($out_biz_no) {
	}

	public function refund($refund_no, $trade_no, $total_amount, $refund_amount, $refund_desc) {
	}

	public function refund_status($refund_no, $trade_no) {
	}

	public function transfer($transfer_no, $amount, $realname, $account, $title = '', $desc = '') {
	}

	public function transfer_status($transfer_no) {
	}

}
