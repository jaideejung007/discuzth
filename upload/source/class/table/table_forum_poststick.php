<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_poststick extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_poststick';
		$this->_pk = '';

		parent::__construct();
	}

	public function fetch_all_by_tid($tid) {
		return DB::fetch_all('SELECT * FROM %t WHERE tid=%d ORDER BY dateline DESC', [$this->_table, $tid], 'pid');
	}


	public function count_by_pid($pid) {
		return DB::result_first('SELECT count(*) FROM %t WHERE pid=%d ', [$this->_table, $pid]);
	}

	public function delete_by_pid($pids) {
		if(empty($pids)) {
			return false;
		}
		return DB::query('DELETE FROM %t WHERE '.DB::field('pid', $pids), [$this->_table]);
	}

	public function delete_by_tid($tids) {
		if(empty($tids)) {
			return false;
		}
		return DB::query('DELETE FROM %t WHERE '.DB::field('tid', $tids), [$this->_table]);
	}

	public function delete($val, $unbuffered = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::delete($val, $unbuffered);
		} else {
			return $this->delete_stick($val, $unbuffered);
		}
	}

	public function delete_stick($tid, $pid) {
		return DB::query('DELETE FROM %t WHERE tid=%d AND pid=%d', [$this->_table, $tid, $pid]);
	}

	public function count_by_tid($tid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE tid=%d', [$this->_table, $tid]);
	}
}

