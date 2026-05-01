<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_friend_request extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'home_friend_request';
		$this->_pk = '';

		parent::__construct();
	}

	public function fetch_by_uid($uid) {
		return DB::fetch_first('SELECT * FROM %t WHERE uid=%d LIMIT 0,1', [$this->_table, $uid]);
	}

	public function fetch_by_uid_fuid($uid, $fuid) {
		return DB::fetch_first('SELECT * FROM %t WHERE uid=%d AND fuid=%d', [$this->_table, $uid, $fuid]);
	}

	public function fetch_all_by_uid($uid, $start = 0, $limit = 0) {
		return DB::fetch_all('SELECT * FROM %t WHERE uid=%d ORDER BY dateline DESC '.DB::limit($start, $limit), [$this->_table, $uid]);
	}

	public function delete_by_uid_or_fuid($uids) {
		$uids = dintval($uids, true);
		if($uids) {
			return DB::delete($this->_table, DB::field('uid', $uids).' OR '.DB::field('fuid', $uids));
		}
		return 0;
	}

	public function delete_by_uid($uids) {
		$uids = dintval($uids, true);
		if($uids) {
			return DB::delete($this->_table, DB::field('uid', $uids));
		}
		return 0;
	}

	public function delete_by_uid_fuid($uid, $fuid) {
		$uid = dintval($uid, true);
		$fuid = dintval($fuid, true);
		if($uid) {
			return DB::delete($this->_table, DB::field('uid', $uid).' AND '.DB::field('fuid', $fuid));
		}
		return 0;
	}

	public function count_by_uid_fuid($uid, $fuid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d AND fuid=%d', [$this->_table, $uid, $fuid]);
	}

	public function count_by_uid($uid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d', [$this->_table, $uid]);
	}

}

