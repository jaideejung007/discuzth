<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_word_type extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_word_type';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function fetch_by_typename($typename) {
		$data = [];
		if(!empty($typename)) {
			$data = DB::fetch_first('SELECT * FROM %t WHERE typename=%s', [$this->_table, $typename], $this->_pk);
		}
		return $data;
	}

	public function fetch_all($ids = [], $force_from_db = false) {
		
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::fetch_all($ids, $force_from_db);
		} else {
			return $this->fetch_all_word_type();
		}
	}

	public function fetch_all_word_type() {
		return DB::fetch_all('SELECT * FROM %t', [$this->_table], $this->_pk);
	}

}

