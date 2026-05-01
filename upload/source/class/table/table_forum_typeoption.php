<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_typeoption extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_typeoption';
		$this->_pk = 'optionid';

		parent::__construct();
	}

	public function fetch_all_by_classid($classid, $start = 0, $limit = 0) {
		return DB::fetch_all('SELECT * FROM %t WHERE classid=%d ORDER BY displayorder '.DB::limit($start, $limit), [$this->_table, $classid]);
	}

	public function fetch_all_by_identifier($identifier, $start = 0, $limit = 0, $not_optionid = null) {
		return DB::fetch_all('SELECT * FROM %t WHERE identifier=%s '.($not_optionid ? ' AND '.DB::field('optionid', $not_optionid, '<>').' ' : '').DB::limit($start, $limit), [$this->_table, $identifier]);
	}

	public function fetch_all_by_identifier_prefix($prefix) {
		return DB::fetch_all('SELECT optionid FROM %t WHERE %i', [$this->_table, DB::field('identifier', '%'.$prefix.'%', 'like')], 'optionid');
	}

}

