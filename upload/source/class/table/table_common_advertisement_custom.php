<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_advertisement_custom extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_advertisement_custom';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function fetch_all_data() {
		return DB::fetch_all('SELECT * FROM %t ORDER BY id', [$this->_table]);
	}

	public function fetch_by_name($name) {
		return DB::fetch_first('SELECT * FROM %t WHERE name=%s', [$this->_table, $name]);
	}

	public function get_id_by_name($name) {
		$result = $this->fetch_by_name($name);
		return $result['id'];
	}
}

