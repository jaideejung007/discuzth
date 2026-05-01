<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_adminnote extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_adminnote';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function delete($val, $unbuffered = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::delete($val, $unbuffered);
		} else {
			$unbuffered = $unbuffered === false ? '' : $unbuffered;
			return $this->delete_note($val, $unbuffered);
		}
	}

	public function delete_note($id, $admin = '') {
		if(empty($id)) {
			return false;
		}
		return DB::query('DELETE FROM %t WHERE '.DB::field('id', $id).' %i', [$this->_table, ($admin ? ' AND '.DB::field('admin', $admin) : '')]);
	}

	public function fetch_all_by_access($access) {
		if(!is_numeric($access) && !is_array($access)) {
			return [];
		}
		return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('access', $access).' ORDER BY dateline DESC', [$this->_table]);
	}

	public function count_by_access($access) {
		if(!is_numeric($access) && !is_array($access)) {
			return 0;
		}
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE '.DB::field('access', $access), [$this->_table]);
	}

}

