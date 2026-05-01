<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_restful_permission extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'restful_permission';

		parent::__construct();
	}

	public function delete_by_baseuri_ver($baseuri, $ver) {
		return DB::delete($this->_table, DB::field('uri', $baseuri.'%', 'like').' AND '.DB::field('ver', $ver));
	}

	public function fetch_all_by_appid($appid) {
		return DB::fetch_all('SELECT * FROM %t WHERE appid=%d', [$this->_table, $appid]);
	}

	public function delete_by_appid($appid) {
		return DB::delete($this->_table, DB::field('appid', $appid));
	}
}
