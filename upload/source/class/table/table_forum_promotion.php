<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_promotion extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_promotion';
		$this->_pk = 'ip';

		parent::__construct();
	}

	public function count_by_uid($uid) {
		$uid = dintval($uid, is_array($uid));
		if(!empty($uid)) {
			$parameter = [$this->_table, $uid];
			$where = is_array($uid) ? 'uid IN(%n)' : 'uid=%d';
			return DB::result_first("SELECT COUNT(*) FROM %t WHERE $where", $parameter);
		}
		return 0;
	}

	public function delete_by_uid($uid) {
		return $uid ? DB::delete($this->_table, DB::field('uid', $uid)) : false;
	}

	public function delete_all() {
		return DB::query('DELETE FROM %t', [$this->_table]);
	}

}

