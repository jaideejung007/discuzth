<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_typevar extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_typevar';
		$this->_pk = '';

		parent::__construct();
	}

	public function count_by_search($search) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE search>%d', [$this->_table, $search]);
	}

	public function fetch_all_by_search_optiontype($search, $optiontypes) {
		if(empty($optiontypes)) {
			return [];
		}
		return DB::fetch_all('SELECT p.*, v.* FROM %t v LEFT JOIN %t p ON p.optionid=v.optionid WHERE search=%d OR p.'.DB::field('type', $optiontypes),
			[$this->_table, 'forum_typeoption', $search]);
	}

	public function fetch_all_by_sortid($sortid, $order = '') {
		return DB::fetch_all('SELECT * FROM %t WHERE sortid=%d '.($order ? 'ORDER BY '.DB::order('displayorder', $order) : ''), [$this->_table, $sortid], 'optionid');
	}

	public function update($val, $data, $unbuffered = false, $low_priority = false, $null = false) {
		
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::update($val, $data, $unbuffered, $low_priority);
		} else {
			return $this->update_typevar($val, $data, $unbuffered, $low_priority, $null);
		}
	}

	public function update_typevar($sortid, $optionid, $data, $unbuffered = false, $low_priority = false) {
		if(empty($data)) {
			return false;
		}
		return DB::update($this->_table, $data, ['sortid' => $sortid, 'optionid' => $optionid], $unbuffered, $low_priority);
	}

	public function update_by_search($search, $data, $unbuffered = false, $low_priority = false) {
		if(empty($data)) {
			return false;
		}
		return DB::update($this->_table, $data, ['search' => $search], $unbuffered, $low_priority);
	}

	public function delete($val = null, $unbuffered = false) {
		
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::delete($val, $unbuffered);
		} else {
			$unbuffered = $unbuffered === false ? null : $unbuffered;
			return $this->delete_typevar($val, $unbuffered);
		}
	}

	public function delete_typevar($sortids = null, $optionids = null) {
		$where = [];
		$sortids && $where[] = DB::field('sortid', $sortids);
		$optionids && $where[] = DB::field('optionid', $optionids);
		if($where) {
			return DB::query('DELETE FROM %t WHERE '.implode(' AND ', $where), [$this->_table]);
		} else {
			return false;
		}
	}

	public function fetch_all_by_optionid($optionids) {
		return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('optionid', $optionids), [$this->_table]);
	}
}

