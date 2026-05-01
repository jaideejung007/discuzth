<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_grouplevel extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_grouplevel';
		$this->_pk = 'levelid';

		parent::__construct();
	}

	public function fetch_all_creditslower_order() {
		return DB::fetch_all('SELECT * FROM '.DB::table('forum_grouplevel').' WHERE 1 ORDER BY creditslower');
	}

	public function fetch_count() {
		return DB::result_first('SELECT count(*) FROM '.DB::table('forum_grouplevel'));
	}

	public function fetch_by_credits($credits = 0) {
		return DB::fetch_first('SELECT * FROM %t WHERE creditshigher<=%d AND %d<creditslower LIMIT 1', [$this->_table, $credits, $credits]);
	}
}

