<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_member_profile_setting extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_member_profile_setting';
		$this->_pk = 'fieldid';

		parent::__construct();
	}

	public function range($start = 0, $limit = 0, $sort = '') {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::range($start, $limit, $sort);
		} else {
			return $this->range_setting($start, $limit);
		}
	}

	public function range_setting($start = 0, $limit = 0) {
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).' ORDER BY available DESC, displayorder'.DB::limit($start, $limit), null, $this->_pk);
	}

	public function fetch_all_by_available_unchangeable($available, $unchangeable) {
		return DB::fetch_all('SELECT * FROM %t WHERE available=%d AND unchangeable=%d ORDER BY displayorder', [$this->_table, $available, $unchangeable], $this->_pk);
	}

	public function fetch_all_by_available($available) {
		return DB::fetch_all('SELECT * FROM %t WHERE available=%d ORDER BY displayorder', [$this->_table, $available], $this->_pk);
	}

	public function fetch_all_by_available_formtype($available, $formtype) {
		return DB::fetch_all('SELECT * FROM %t WHERE available=%d AND formtype=%s', [$this->_table, $available, $formtype], $this->_pk);
	}

	public function fetch_all_by_available_required($available, $required) {
		return DB::fetch_all('SELECT * FROM %t WHERE available=%d AND required=%d', [$this->_table, $available, $required], $this->_pk);
	}

	public function fetch_all_by_available_showinregister($available, $showinregister) {
		return DB::fetch_all('SELECT * FROM %t WHERE available=%d AND showinregister=%d', [$this->_table, $available, $showinregister], $this->_pk);
	}

	public function fetch_all_by_available_showinthread($available, $showinthread) {
		return DB::fetch_all('SELECT * FROM %t WHERE available=%d AND showinthread=%d', [$this->_table, $available, $showinthread], $this->_pk);
	}

	public function clear_showinthread() {
		DB::update($this->_table, ['showinthread' => 0]);
	}
}

