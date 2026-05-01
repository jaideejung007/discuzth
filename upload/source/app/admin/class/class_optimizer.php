<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace admin;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class class_optimizer {

	private $optimizer = [];

	public function __construct($type) {
		$c = 'admin\\'.$type;
		$this->optimizer = new $c();
	}


	public function check() {
		return $this->optimizer->check();
	}

	public function optimizer() {
		return $this->optimizer->optimizer();
	}

	public function option_optimizer($options) {
		return $this->optimizer->option_optimizer($options);
	}

	public function get_option() {
		return $this->optimizer->get_option();
	}
}

