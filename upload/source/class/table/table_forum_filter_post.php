<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_filter_post extends discuz_table {

	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {
		$this->_table = 'forum_filter_post';
		$this->_pk = '';

		parent::__construct();
	}

	public function fetch_all_by_tid_pids($tid, $pid) {
		return DB::fetch_all('SELECT * FROM %t WHERE tid=%d AND pid IN(%n)', [$this->_table, $tid, $pid], 'pid');
	}

	public function fetch_all_by_tid_postlength_limit($tid, $limit = 10) {
		if($limit <= 0) {
			return [];
		}
		return DB::fetch_all('SELECT * FROM %t WHERE tid=%d ORDER BY postlength DESC LIMIT %d', [$this->_table, $tid, $limit], 'pid');
	}

	public function delete_by_tid_pid($tid, $pid) {
		if(empty($tid) || empty($pid)) {
			return false;
		}
		return DB::query('DELETE FROM %t WHERE tid=%d AND pid=%d', [$this->_table, $tid, $pid]);
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

