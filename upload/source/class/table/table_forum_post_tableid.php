<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_post_tableid extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_post_tableid';
		$this->_pk = 'pid';

		parent::__construct();
	}

	public function alter_auto_increment($auto_increment) {
		return DB::query('ALTER TABLE %t AUTO_INCREMENT=%d', [$this->_table, $auto_increment]);
	}

	public function delete_by_lesspid($pid) {
		return DB::query('DELETE FROM %t WHERE pid<%d', [$this->_table, $pid]);
	}

	public function fetch_max_id() {
		return DB::result_first('SELECT MAX(pid) FROM '.DB::table($this->_table));
	}
}

