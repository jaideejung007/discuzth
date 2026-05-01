<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_memberrecommend extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_memberrecommend';
		$this->_pk = '';

		parent::__construct();
	}

	public function fetch_by_recommenduid_tid($uid, $tid) {
		return DB::fetch_first('SELECT * FROM %t WHERE recommenduid=%d AND tid=%d', [$this->_table, $uid, $tid]);
	}

	public function count_by_recommenduid_dateline($uid, $dateline) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE recommenduid=%d AND dateline>%d', [$this->_table, $uid, $dateline]);
	}

}

