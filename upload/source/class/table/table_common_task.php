<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_task extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_task';
		$this->_pk = 'taskid';

		parent::__construct();
	}

	public function fetch_all_by_available($available) {
		return DB::fetch_all('SELECT * FROM %t WHERE available=%d', [$this->_table, $available], $this->_pk);
	}

	public function fetch_all_data() {
		return DB::fetch_all('SELECT * FROM %t ORDER BY displayorder, taskid DESC', [$this->_table]);
	}

	public function count_by_scriptname($scriptname) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE scriptname=%s', [$this->_table, $scriptname]);
	}

	public function fetch_all_by_scriptname($scriptname) {
		return DB::fetch_all('SELECT * FROM %t WHERE scriptname=%s', [$this->_table, $scriptname]);
	}

	public function update_by_scriptname($scriptname, $data) {
		if(!$data || !is_array($data)) {
			return;
		}
		DB::update($this->_table, $data, DB::field('scriptname', $scriptname));
	}

	public function update_applicants($taskid, $v) {
		DB::query('UPDATE %t SET applicants=applicants+%s WHERE taskid=%d', [$this->_table, $v, $taskid]);
	}

	public function update_achievers($taskid, $v) {
		return DB::query('UPDATE %t SET achievers=achievers+%s WHERE taskid=%d', [$this->_table, $v, $taskid]);
	}

	public function update_available($available = 2) {
		if($available == 2) {
			
			DB::query("UPDATE %t SET available='2' WHERE available='1' AND starttime<=%d AND (endtime='0' OR endtime>%d)", [$this->_table, TIMESTAMP, TIMESTAMP], false, true);
		} else {
			
			DB::query("UPDATE %t SET available='1' WHERE available='2' AND (starttime>%d || (endtime<=%d && endtime>'0'))", [$this->_table, TIMESTAMP, TIMESTAMP], false, true);
		}
	}

	public function fetch_next_starttime() {
		
		return DB::result_first("SELECT starttime FROM %t WHERE available='1' AND starttime>'0' AND (endtime='0' OR endtime>%d) ORDER BY starttime ASC", [$this->_table, TIMESTAMP, TIMESTAMP]);
	}

	public function fetch_next_endtime() {
		
		return DB::result_first("SELECT endtime FROM %t WHERE available='2' AND endtime>'0' ORDER BY endtime ASC", [$this->_table]);
	}

	public function fetch_all_by_status($uid, $status) {
		$status = match ($status) {
			'doing' => "mt.status='0'",
			'done' => "mt.status='1'",
			'failed' => "mt.status='-1'",
			default => "'".TIMESTAMP."' > starttime AND (endtime=0 OR endtime>'".TIMESTAMP."') AND (mt.taskid IS NULL OR (ABS(mt.status)='1' AND t.period>0))",
		};
		return DB::fetch_all("SELECT t.*, mt.csc, mt.dateline FROM %t t
			LEFT JOIN %t mt ON mt.taskid=t.taskid AND mt.uid=%d
			WHERE %i AND t.available='2' ORDER BY t.displayorder, t.taskid DESC", [$this->_table, 'common_mytask', $uid, $status]);
	}

	public function fetch_by_uid($uid, $taskid) {
		return DB::fetch_first("SELECT t.*, mt.dateline, mt.dateline AS applytime, mt.status, mt.csc FROM %t t LEFT JOIN %t mt ON mt.uid=%d AND mt.taskid=t.taskid
			WHERE t.taskid=%d AND t.available='2'", [$this->_table, 'common_mytask', $uid, $taskid]);
	}

}

