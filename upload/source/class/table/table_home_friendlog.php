<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_friendlog extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'home_friendlog';
		$this->_pk = '';

		parent::__construct();
	}

	public function fetch_all_order_by_dateline($start = 0, $limit = 0) {
		return DB::fetch_all('SELECT * FROM %t ORDER BY dateline'.DB::limit($start, $limit), [$this->_table]);
	}

	public function delete_by_uid_fuid($uid, $fuid) {
		return DB::delete($this->_table, DB::field('uid', $uid).' AND '.DB::field('fuid', $fuid));
	}

}

