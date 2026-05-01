<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_poll extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_poll';
		$this->_pk = 'tid';

		parent::__construct();
	}

	public function update_vote($tid, $num = 1) {
		DB::query('UPDATE %t SET voters=voters+\'%d\' WHERE tid=%d', [$this->_table, $num, $tid], false, true);
	}

	public function delete_by_tid($tids) {
		return DB::delete($this->_table, DB::field('tid', $tids));
	}
}

