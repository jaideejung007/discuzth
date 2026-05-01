<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_class extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'home_class';
		$this->_pk = 'classid';

		parent::__construct();
	}

	public function fetch_all_by_uid($uid) {
		return DB::fetch_all('SELECT * FROM %t WHERE uid = %d', [$this->_table, $uid], $this->_pk);
	}

	public function fetch_classid_by_uid_classname($uid, $classname) {
		return DB::result_first('SELECT classid FROM %t WHERE uid = %d AND classname = %s', [$this->_table, $uid, $classname]);
	}

	public function delete_by_uid($uids) {
		if(!$uids) {
			return null;
		}
		return DB::delete($this->_table, DB::field('uid', $uids));
	}
}

