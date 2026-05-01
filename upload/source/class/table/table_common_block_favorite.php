<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_block_favorite extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_block_favorite';
		$this->_pk = 'favid';

		parent::__construct();
	}

	public function delete_by_uid_bid($uid, $bid) {
		return ($uid = dintval($uid)) && ($bid = dintval($bid)) ? DB::delete($this->_table, DB::field('uid', $uid).' AND '.DB::field('bid', $bid)) : false;
	}

	public function delete_by_bid($bid) {
		return ($bid = dintval($bid)) ? DB::delete($this->_table, DB::field('bid', $bid)) : false;
	}

	public function count_by_uid_bid($uid, $bid) {
		return ($uid = dintval($uid)) && ($bid = dintval($bid)) ? DB::result_first('SELECT count(*) FROM %t WHERE uid=%d AND bid=%d', [$this->_table, $uid, $bid]) : false;
	}

	public function fetch_all_by_uid($uid) {
		return ($uid = dintval($uid)) ? DB::fetch_all('SELECT * FROM %t WHERE uid=%d ORDER BY dateline DESC', [$this->_table, $uid], 'bid') : [];
	}
}

