<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_member_crime extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_member_crime';
		$this->_pk = 'cid';

		parent::__construct();
	}

	public function fetch_all_by_uid($uid) {
		return $uid ? DB::fetch_all('SELECT * FROM %t WHERE uid=%d ORDER BY dateline DESC', [$this->_table, $uid], $this->_pk) : [];
	}

	public function count_by_uid_action($uid, $action) {
		return $uid ? DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d AND action=%d', [$this->_table, $uid, $action]) : 0;
	}

	public function count_by_where($where) {
		return $where ? DB::result_first('SELECT COUNT(*) FROM %t %i ', [$this->_table, $where]) : $this->count();
	}

	public function fetch_all_by_where($where, $start = 0, $limit = 0) {
		return DB::fetch_all('SELECT * FROM %t %i ORDER BY dateline DESC '.DB::limit($start, $limit), [$this->_table, $where]);
	}

	public function fetch_all_by_uid_action($uid, $action) {
		return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('uid', $uid).' AND '.DB::field('action', $action).' ORDER BY dateline', [$this->_table]);
	}

	public function fetch_all_by_cid($cid, $action, $limit) {
		if(!$cid) {
			return DB::fetch_all('SELECT * FROM %t '.($action ? 'WHERE '.DB::field('action', $action) : '').' ORDER BY cid DESC '.DB::limit($limit), [$this->_table], $this->_pk);
		} else {
			return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('cid', $cid, '<').($action ? ' AND '.DB::field('action', $action) : '').' ORDER BY cid DESC '.DB::limit($limit), [$this->_table], $this->_pk);
		}
	}

	public function delete_by_uid($uids) {
		if(!$uids) {
			return null;
		}
		DB::delete($this->_table, DB::field('uid', $uids));
	}

	public function delete_by_removetime($removetime) {
		return DB::query('DELETE FROM %t WHERE dateline < %d', [$this->_table, $removetime]);
	}
}

