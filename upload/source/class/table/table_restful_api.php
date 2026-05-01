<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_restful_api extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'restful_api';

		parent::__construct();
	}

	public function fetch_all_data($haveData = false) {
		$field = $haveData ? '*' : 'baseuri, ver, name, copyright, status, dateline';
		return DB::fetch_all('SELECT %i FROM %t ORDER BY ver ASC', [$field, $this->_table]);
	}

	public function fetch_by_baseuri_ver($baseuri, $ver) {
		return DB::fetch_first('SELECT * FROM %t WHERE baseuri=%s AND ver=%d', [
			$this->_table, $baseuri, $ver
		]);
	}

	public function delete_by_baseuri_ver($baseuri, $ver) {
		return DB::delete($this->_table, DB::field('baseuri', $baseuri).' AND '.DB::field('ver', $ver));
	}

	public function update_by_baseuri_ver($data, $baseuri, $ver) {
		return DB::update($this->_table, $data, DB::field('baseuri', $baseuri).' AND '.DB::field('ver', $ver));
	}
}
