<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_credit_rule_log extends discuz_table {
	private $_rids = [];

	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_credit_rule_log';
		$this->_pk = 'clid';

		parent::__construct();
	}

	public function increase($clid, $logarr) {
		if($clid && !empty($logarr) && is_array($logarr)) {
			return DB::query('UPDATE %t SET %i WHERE clid=%d', [$this->_table, implode(',', $logarr), $clid]);
		}
		return 0;
	}

	public function fetch_ids_by_rid_fid($rid, $fid) {
		$lids = [];
		if($rid && $fid) {
			$query = DB::query('SELECT clid FROM %t WHERE rid=%d AND fid=%d', [$this->_table, $rid, $fid]);
			while($value = DB::fetch($query)) {
				$lids[$value['clid']] = $value['clid'];
			}
		}
		return $lids;
	}

	public function fetch_rule_log($rid, $uid, $fid = 0) {
		$log = [];
		if($rid && $uid) {
			$sql = '';
			$para = [$this->_table, $uid, $rid];
			if($fid) {
				$sql = ' AND fid=%d';
				$para[] = $fid;
			}
			$log = DB::fetch_first('SELECT * FROM %t WHERE uid=%d AND rid=%d'.$sql, $para);
		}
		return $log;
	}

	public function count_by_uid($uid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d', [$this->_table, $uid]);
	}

	public function fetch_all_by_uid($uid, $start = 0, $limit = 0) {
		$data = [];

		$query = DB::query('SELECT * FROM %t WHERE uid=%d ORDER BY dateline DESC '.DB::limit($start, $limit), [$this->_table, $uid]);
		while($value = DB::fetch($query)) {
			$data[$value['clid']] = $value;
			$this->_rids[$value['rid']] = $value['rid'];
		}
		return $data;
	}

	public function delete_by_uid($uids) {
		$uids = dintval((array)$uids, true);
		if(!empty($uids)) {
			return DB::delete($this->_table, DB::field('uid', $uids));
		}
		return 0;
	}

	public function get_rids() {
		return $this->_rids;
	}
}

