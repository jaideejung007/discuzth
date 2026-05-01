<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_doing_recomend_log extends discuz_table {
	public static function t() {
		static $instance;
		if(!isset($instance)) {
			$instance = new self();
		}
		return $instance;
	}

	public function __construct() {

		$this->_table = 'home_doing_recomend_log';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function delete_by_doid_uid($doid, $uid) {
		return DB::delete($this->_table, DB::field('doid', $doid).' AND '.DB::field('uid', $uid));
	}

	public function fetch_by_doid_uid($doid, $uid) {
		return DB::fetch_first('SELECT * FROM %t WHERE doid=%d AND uid=%d', array($this->_table, $doid, $uid));
	}

	public function fetch_all_by_doid($doid) {
		return DB::fetch_all('SELECT * FROM %t WHERE doid=%d', array($this->_table, $doid));
	}

	public function count_by_doid($doid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE doid=%d', array($this->_table, $doid));
	}

	public function delete_by_uid($uid) {
		return DB::delete($this->_table, DB::field('uid', $uid));
	}

	public function delete_by_doid($doid) {
		return DB::delete($this->_table, DB::field('doid', $doid));
	}

	public function fetch_all_by_doids_uid($doids, $uid) {
		if (empty($doids) || !$uid) {
			return array();
		}
		$result = DB::fetch_all('SELECT doid FROM %t WHERE doid IN (%n) AND uid=%d', array($this->_table, $doids, $uid));
		$recommend_status = array();
		foreach ($result as $item) {
			$recommend_status[$item['doid']] = 1;
		}
		return $recommend_status;
	}
}
