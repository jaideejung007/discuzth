<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_imagetype extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_imagetype';
		$this->_pk = 'typeid';

		parent::__construct();
	}

	public function fetch_all_by_type($type, $available = null) {
		$available = $available !== null ? ($available ? ' AND available=1' : ' AND available=0') : '';
		return DB::fetch_all('SELECT * FROM %t WHERE type=%s %i ORDER BY displayorder', [$this->_table, $type, $available]);
	}

	public function fetch_all_available() {
		return DB::fetch_all('SELECT * FROM %t WHERE available=1', [$this->_table]);
	}

	public function count_by_name($type, $name) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE type=%s AND name=%s', [$this->_table, $type, $name]);
	}

}

