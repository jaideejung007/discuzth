<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_style extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_style';
		$this->_pk = 'styleid';

		parent::__construct();
	}

	public function fetch_all_data($withtemplate = false, $available = false) {
		if($withtemplate) {
			$available = $available !== false ? 'WHERE s.available='.intval($available) : '';
			return DB::fetch_all('SELECT s.*, t.name AS tplname, t.directory, t.copyright FROM %t s LEFT JOIN %t t ON t.templateid=s.templateid %i ORDER BY s.styleid ASC', [$this->_table, 'common_template', $available]);
		} else {
			$available = $available !== false ? 'WHERE available='.intval($available) : '';
			return DB::fetch_all('SELECT * FROM %t %i', [$this->_table, $available]);
		}
	}

	public function fetch_by_styleid($styleid) {
		return DB::fetch_first('SELECT s.*, t.name AS tplname, t.directory, t.copyright FROM %t s LEFT JOIN %t t ON s.templateid=t.templateid WHERE s.styleid=%d', [$this->_table, 'common_template', $styleid]);
	}

	public function check_stylename($stylename) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE name=%s', [$this->_table, $stylename]);
	}

	public function fetch_by_stylename_templateid($stylename, $templateid = 0) {
		if($templateid) {
			return DB::fetch_first('SELECT * FROM %t WHERE name=%s AND templateid=%d ORDER BY styleid ASC LIMIT 1', [$this->_table, $stylename, $templateid]);
		} else {
			return DB::fetch_first('SELECT * FROM %t WHERE name=%s ORDER BY styleid ASC LIMIT 1', [$this->_table, $stylename]);
		}
	}

}

