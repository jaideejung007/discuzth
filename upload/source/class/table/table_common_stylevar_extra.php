<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_stylevar_extra extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_stylevar_extra';
		$this->_pk = 'stylevarid';

		parent::__construct();
	}

	public function fetch_all_by_styleid($styleid) {
		return DB::fetch_all('SELECT * FROM %t WHERE styleid=%d ORDER BY displayorder', [$this->_table, $styleid]);
	}

	public function fetch_all_visible_by_styleid($styleid) {
		return DB::fetch_all('SELECT * FROM %t WHERE styleid=%d AND displayorder>=0 ORDER BY displayorder', [$this->_table, $styleid]);
	}

	public function count_by_styleid($styleid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE styleid=%d', [$this->_table, $styleid]);
	}

	public function count_by_styleid_page($styleid) {
		return DB::result_first("SELECT COUNT(*) FROM %t WHERE styleid=%d AND type='stylePage'", [$this->_table, $styleid]);
	}

	public function fetch_first_by_styleid($styleid) {
		return DB::fetch_first('SELECT * FROM %t WHERE styleid=%d ORDER BY displayorder LIMIT 1', [$this->_table, $styleid]);
	}

	public function update_by_variable($styleid, $variable, $data) {
		if(!$styleid || !$variable || !$data || !is_array($data)) {
			return;
		}
		DB::update($this->_table, $data, DB::field('styleid', $styleid).' AND '.DB::field('variable', $variable));
	}

	public function update_by_stylevarid($styleid, $stylevarid, $data) {
		if(!$styleid || !$stylevarid || !$data || !is_array($data)) {
			return;
		}
		DB::update($this->_table, $data, DB::field('styleid', $styleid).' AND '.DB::field('stylevarid', $stylevarid));
	}

	public function check_variable($styleid, $variable) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE styleid=%d AND variable=%s', [$this->_table, $styleid, $variable]);
	}


	public function delete_by_styleid($styleid) {
		if(!$styleid) {
			return;
		}
		DB::delete($this->_table, DB::field('styleid', $styleid));
	}

	public function delete_by_variable($styleid, $variable) {
		if(!$styleid || !$variable) {
			return;
		}
		DB::delete($this->_table, DB::field('styleid', $styleid).' AND '.DB::field('variable', $variable));
	}

}

