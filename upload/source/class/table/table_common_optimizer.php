<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_optimizer extends discuz_table {

	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_optimizer';
		$this->_pk = 'k';

		parent::__construct();
	}

	public function fetch($id, $force_from_db = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::fetch($id, $force_from_db);
		} else {
			return $this->fetch_optimizer($id, $force_from_db);
		}
	}

	public function update($val, $data, $unbuffered = false, $low_priority = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::update($val, $data, $unbuffered, $low_priority);
		} else {
			return $this->update_optimizer($val, $data);
		}
	}

	public function fetch_optimizer($skey, $auto_unserialize = false) {
		$data = DB::result_first('SELECT v FROM '.DB::table($this->_table).' WHERE '.DB::field($this->_pk, $skey));
		return $auto_unserialize ? (array)dunserialize($data) : $data;
	}

	public function update_optimizer($skey, $svalue) {
		return DB::insert($this->_table, [$this->_pk => $skey, 'v' => is_array($svalue) ? serialize($svalue) : $svalue], false, true);
	}

}

