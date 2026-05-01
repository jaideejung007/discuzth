<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_hotreply_number extends discuz_table {

	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {
		$this->_table = 'forum_hotreply_number';
		$this->_pk = 'pid';

		parent::__construct();
	}

	public function fetch_all_by_pids($pids) {
		return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('pid', $pids), [$this->_table], 'pid');
	}

	public function fetch_all_by_tid_total($tid, $limit = 5) {
		return DB::fetch_all('SELECT * FROM %t WHERE tid=%d ORDER BY total DESC LIMIT %d', [$this->_table, $tid, $limit], 'pid');
	}

	public function fetch_by_pid($pid) {
		return DB::fetch_first('SELECT * FROM %t WHERE pid=%d', [$this->_table, $pid]);
	}

	public function update_num($pid, $typeid) {
		$typename = $typeid == 1 ? 'support' : 'against';
		return DB::query('UPDATE %t SET '.$typename.'='.$typename.'+1, total=total+1 WHERE pid=%d', [$this->_table, $pid]);
	}

	public function delete_by_tid($tid) {
		if(empty($tid)) {
			return false;
		}
		return DB::query('DELETE FROM %t WHERE tid IN (%n)', [$this->_table, $tid]);
	}

	public function delete_by_pid($pids) {
		if(empty($pids)) {
			return false;
		}
		return DB::query('DELETE FROM %t WHERE '.DB::field('pid', $pids), [$this->_table]);
	}
}

