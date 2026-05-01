<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_warning extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_warning';
		$this->_pk = 'wid';

		parent::__construct();
	}

	public function count_by_author($authors = null) {
		return DB::result_first('SELECT COUNT(*) FROM %t '.($authors ? 'WHERE '.DB::field('author', $authors) : ''), [$this->_table]);
	}

	public function count_by_authorid_dateline($authorid, $dateline = null) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE authorid=%d '.($dateline ? ' AND '.DB::field('dateline', dintval($dateline), '>=') : ''), [$this->_table, $authorid]);
	}

	public function fetch_all_by_author($authors, $start, $limit) {
		return DB::fetch_all('SELECT * FROM %t '.($authors ? 'WHERE '.DB::field('author', $authors) : '').' ORDER BY wid DESC '.DB::limit($start, $limit), [$this->_table]);
	}

	public function fetch_all_by_authorid($authorid) {
		return DB::fetch_all('SELECT * FROM %t WHERE authorid=%d', [$this->_table, $authorid]);
	}

	public function delete_by_pid($pids) {
		if(empty($pids)) {
			return false;
		}
		return DB::query('DELETE FROM %t WHERE '.DB::field('pid', $pids), [$this->_table], false, true);
	}

	public function delete_by_removetime($removetime) {
		return DB::query('DELETE FROM %t WHERE dateline < %d', [$this->_table, $removetime]);
	}
}

