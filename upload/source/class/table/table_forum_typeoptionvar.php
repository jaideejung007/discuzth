<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_typeoptionvar extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_typeoptionvar';
		$this->_pk = '';

		parent::__construct();
	}

	public function fetch_all_by_tid_optionid($tids, $optionids = null) {
		if(empty($tids)) {
			return [];
		}
		return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('tid', $tids).($optionids ? ' AND '.DB::field('optionid', $optionids) : ''), [$this->_table]);
	}

	public function fetch_all_by_search($sortids = null, $fids = null, $tids = null, $optionids = null) {
		$sql = [];
		$sortids && $sql[] = DB::field('sortid', $sortids);
		$fids && $sql[] = DB::field('fid', $fids);
		$tids && $sql[] = DB::field('tid', $tids);
		$optionids && $sql[] = DB::field('optionid', $optionids);
		if($sql) {
			return DB::fetch_all('SELECT * FROM %t WHERE %i', [$this->_table, implode(' AND ', $sql)]);
		} else {
			return [];
		}
	}

	public function update_by_tid($tid, $data, $unbuffered = false, $low_priority = false, $optionid = null, $sortid = null) {
		if(empty($data)) {
			return false;
		}
		$where = [];
		$where[] = DB::field('tid', $tid);
		if($optionid !== null) {
			$where[] = DB::field('optionid', $optionid);
		}
		if($sortid !== null) {
			$where[] = DB::field('sortid', $sortid);
		}
		return DB::update($this->_table, $data, implode(' AND ', $where), $unbuffered, $low_priority);
	}

	public function delete_by_sortid($sortids) {
		if(empty($sortids)) {
			return false;
		}
		return DB::query('DELETE FROM %t WHERE '.DB::field('sortid', $sortids), [$this->_table]);
	}

	public function delete_by_tid($tids) {
		if(empty($tids)) {
			return false;
		}
		return DB::query('DELETE FROM %t WHERE '.DB::field('tid', $tids), [$this->_table], false, true);
	}

}

