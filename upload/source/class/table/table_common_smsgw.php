<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_smsgw extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_smsgw';
		$this->_pk = 'smsgwid';

		parent::__construct();
	}

	public function fetch_all_gw_order_id() {
		return DB::fetch_all("SELECT * FROM %t ORDER BY `order`, $this->_pk", [$this->_table]);
	}

	public function fetch_all_gw_avaliable() {
		return DB::fetch_all("SELECT * FROM %t WHERE available > %d ORDER BY `order`, $this->_pk", [$this->_table, 0]);
	}

}

