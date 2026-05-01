<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_credit_rule_log_field extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_credit_rule_log_field';
		$this->_pk = '';

		parent::__construct();
	}

	public function delete_clid($val) {
		DB::delete($this->_table, DB::field('clid', $val));
	}

	public function delete_by_uid($uids) {
		return DB::delete($this->_table, DB::field('uid', $uids));
	}

	public function update($val, $data, $unbuffered = false, $low_priority = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::update($val, $data, $unbuffered, $low_priority);
		} else {
			return $this->update_field($val, $data, $unbuffered);
		}
	}

	public function fetch($id, $force_from_db = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::fetch($id, $force_from_db);
		} else {
			return $this->fetch_field($id, $force_from_db);
		}
	}

	public function update_field($uid, $clid, $data) {
		if(!empty($data) && is_array($data)) {
			return DB::update($this->_table, $data, ['uid' => $uid, 'clid' => $clid]);
		}
		return 0;
	}

	public function fetch_field($uid, $clid) {
		$logarr = [];
		if($uid && $clid) {
			$logarr = DB::fetch_first('SELECT * FROM %t WHERE uid=%d AND clid=%d', [$this->_table, $uid, $clid]);
		}
		return !empty($logarr) ? $logarr : [];
	}
}

