<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_credit_log_field extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_credit_log_field';
		$this->_pk = '';

		parent::__construct();
	}

	public function delete_by_removetime($removetime) {
		return DB::query('DELETE FROM %t WHERE dateline < %d', [$this->_table, $removetime]);
	}

	public function fetch_last_by_uid($uid) {
		return DB::fetch_first('SELECT * FROM %t WHERE uid=%d ORDER BY logid DESC LIMIT 1', [$this->_table, $uid]);
	}

	public function fetch_clear($uid, $cleardate) {
		return DB::fetch_first('SELECT * FROM %t WHERE uid=%d AND dateline < %d ORDER BY logid DESC LIMIT 1', [$this->_table, $uid, $cleardate]);
	}
}

