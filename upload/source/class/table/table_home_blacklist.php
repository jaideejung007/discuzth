<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_blacklist extends discuz_table {
	private $_buids = [];

	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'home_blacklist';
		$this->_pk = '';

		parent::__construct();
	}

	public function count_by_uid_buid($uid, $buid = 0) {
		$parameter = [$this->_table];
		$wherearr = [];
		if($uid) {
			$parameter[] = $uid;
			$wherearr[] = 'uid=%d';
		}
		if($buid) {
			$parameter[] = $buid;
			$wherearr[] = 'buid=%d';
		}
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		return DB::result_first("SELECT COUNT(*) FROM %t $wheresql", $parameter);
	}

	public function fetch_all_by_uid($uid, $start = 0, $limit = 0) {
		$data = [];
		$query = DB::query('SELECT * FROM %t WHERE uid=%d ORDER BY dateline DESC '.DB::limit($start, $limit), [$this->_table, $uid]);
		while($value = DB::fetch($query)) {
			$data[$value['buid']] = $value;
		}
		return $data;
	}

	public function fetch_all_by_uid_buid($uid, $buids) {
		return DB::fetch_all('SELECT * FROM %t WHERE uid=%d AND buid IN(%n)', [$this->_table, $uid, $buids], 'buid');
	}

	public function delete_by_uid_buid($uid, $buid) {
		return DB::query('DELETE FROM %t WHERE uid=%d AND buid=%d', [$this->_table, $uid, $buid]);
	}
}

