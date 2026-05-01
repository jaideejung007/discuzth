<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_docomment_recomend_log extends discuz_table {
	public static function t() {
		static $instance;
		if(!isset($instance)) {
			$instance = new self();
		}
		return $instance;
	}

	public function __construct() {

		$this->_table = 'home_docomment_recomend_log';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function delete_by_docid_uid($docid, $uid) {
		return DB::delete($this->_table, DB::field('doid', $doid).' AND '.DB::field('uid', $uid));
	}

	public function fetch_by_docid_uid($docid, $uid) {
		return DB::fetch_first('SELECT * FROM %t WHERE docid=%d AND uid=%d', array($this->_table, $docid, $uid));
	}

	public function fetch_all_by_docid($docid) {
		return DB::fetch_all('SELECT * FROM %t WHERE docid=%d', array($this->_table, $docid));
	}

	public function count_by_docid($docid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE docid=%d', array($this->_table, $docid));
	}

	public function delete_by_uid($uid) {
		return DB::delete($this->_table, DB::field('uid', $uid));
	}

	public function delete_by_docid($docid) {
		return DB::delete($this->_table, DB::field('docid', $docid));
	}

	public function fetch_all_by_docids_uid($docids, $uid) {
		if (empty($docids) || !$uid) {
			return array();
		}
		$result = DB::fetch_all('SELECT docid FROM %t WHERE docid IN (%n) AND uid=%d', array($this->_table, $docids, $uid));
		$recommend_status = array();
		foreach ($result as $item) {
			$recommend_status[$item['docid']] = 1;
		}
		return $recommend_status;
	}
}