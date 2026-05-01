<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_template extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_template';
		$this->_pk = 'templateid';

		parent::__construct();
	}

	public function fetch_all_data() {
		return DB::fetch_all('SELECT * FROM %t', [$this->_table]);
	}

	public function delete($val, $unbuffered = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::delete($val, $unbuffered);
		} else {
			return $this->delete_tpl($val);
		}
	}

	public function delete_tpl($val) {
		if(!$val) {
			return;
		}
		DB::query('DELETE FROM %t WHERE %i AND templateid<>1', [$this->_table, DB::field('templateid', $val)]);
	}

	public function get_templateid($name) {
		return DB::result_first('SELECT templateid FROM %t WHERE name=%s', [$this->_table, $name]);
	}

	public function get_templateid_by_directory($directory) {
		return DB::result_first('SELECT templateid FROM %t WHERE directory=%s', [$this->_table, $directory]);
	}

	public function fetch_by_templateid($templateid) {
		return DB::fetch_first('SELECT * FROM %t WHERE templateid=%s', [$this->_table, $templateid]);
	}

}

