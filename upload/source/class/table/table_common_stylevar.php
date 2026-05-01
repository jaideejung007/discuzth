<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_stylevar extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_stylevar';
		$this->_pk = 'stylevarid';

		parent::__construct();
	}

	public function fetch_all_by_styleid($styleid, $available = false) {
		if($available !== false) {
			return DB::fetch_all('SELECT sv.* FROM %t sv INNER JOIN %t s ON s.styleid = sv.styleid AND (s.available=%d OR s.styleid=%d)', [$this->_table, 'common_style', $available, $styleid]);
		} else {
			return DB::fetch_all('SELECT * FROM %t WHERE styleid=%d', [$this->_table, $styleid]);
		}
	}

	public function check_duplicate($styleid, $variable) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE styleid=%d AND variable=%s', [$this->_table, $styleid, $variable]);
	}

	public function update_substitute_by_styleid($substitute, $id, $stylevarids = []) {
		if(!is_string($substitute) || !$id) {
			return;
		}
		DB::update($this->_table, ['substitute' => $substitute], ($stylevarids ? DB::field('stylevarid', $stylevarids).' AND ' : '').DB::field('styleid', $id));
	}

	public function delete_by_styleid($id, $stylevarids = []) {
		if(!$id) {
			return;
		}
		DB::delete($this->_table, ($stylevarids ? DB::field('stylevarid', $stylevarids).' AND ' : '').DB::field('styleid', $id));
	}

	public function delete_by_variable($styleid, $variable) {
		if(!$styleid || !$variable) {
			return;
		}
		DB::delete($this->_table, DB::field('styleid', $styleid).' AND '.DB::field('variable', $variable));
	}

}

