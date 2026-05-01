<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_member_account extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_member_account';
		$this->_pre_cache_key = 'common_member_account_';
		$this->_allowmem = true;

		parent::__construct();
	}

	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		$this->clear_cache($data['uid']);
		helper_forumperm::clear_cache($data['uid']);
		return parent::insert($data, $return_insert_id, $replace, $silent);
	}

	public function fetch_by_uid($uid, $atype) {
		return DB::fetch_first('SELECT * FROM %t WHERE uid=%s AND atype=%d', [$this->_table, $uid, $atype]);
	}

	public function fetch_all_atype_by_uid($uids) {
		$data = DB::fetch_all('SELECT uid,atype FROM %t WHERE uid IN('.dimplode($uids).')', [$this->_table]);
		$return = [];
		foreach($data as $uid => $member) {
			$return[$member['uid']][] = $member['atype'];
		}
		return $return;
	}

	public function fetch_by_account($account, $atype) {
		return DB::fetch_first('SELECT * FROM %t WHERE account=%s AND atype=%d', [$this->_table, $account, $atype]);
	}

	public function fetch_all_by_uid($uid, $nocache = true) {
		$data = $nocache ? false : $this->fetch_cache($uid);
		if(!$data) {
			$data = DB::fetch_all('SELECT * FROM %t WHERE uid=%s', [$this->_table, $uid]);
			!$nocache && $this->store_cache($uid, $data);
		}
		return $data;
	}

	public function update_by_uid_and_atype($uid, $atype, $data = []) {
		if($uid && $atype) {
			helper_forumperm::clear_cache($uid);
			DB::update($this->_table, $data, ['uid' => intval($uid), 'atype' => $atype], 'UNBUFFERED');
		}
	}

	public function delete_by_uid($uid, $atype = 0) {
		$append = '';
		if($atype) {
			$append = ' AND atype='.dintval($atype);
		}
		$uids = dimplode((array)$uid);
		$this->clear_cache($uid);
		helper_forumperm::clear_cache($uid);
		return DB::delete($this->_table, "uid IN ($uids) $append");
	}

	public function delete_by_account($account, $atype) {
		return DB::delete($this->_table, ['where' => 'account=%s AND atype=%d', 'arg' => [$account, $atype]]);
	}

	public function count_by_atype($atype) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE atype=%d', [$this->_table, $atype]);
	}

	public function delete_by_atype($atype) {
		return DB::delete($this->_table, ['where' => 'atype=%d', 'arg' => [$atype]]);
	}

	public function fetch_all_by_atype($atype, $start = 0, $limit = 0) {
		$data = DB::fetch_all('SELECT * FROM %t WHERE atype=%d '.DB::limit($start, $limit), [$this->_table, $atype]);
	}

}

