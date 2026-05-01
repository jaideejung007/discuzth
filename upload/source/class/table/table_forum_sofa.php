<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_sofa extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_sofa';
		$this->_pk = 'tid';

		parent::__construct();
	}

	public function range($start = 0, $limit = 0, $sort = '') {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::range($start, $limit, $sort);
		} else {
			return $this->range_sofa($start, $limit);
		}
	}

	public function range_sofa($start = 0, $limit = 20) {
		return DB::fetch_all('SELECT * FROM %t ORDER BY tid DESC %i', [$this->_table, DB::limit($start, $limit)], $this->_pk);
	}

	public function fetch_all_by_fid($fid, $start = 0, $limit = 20) {
		return DB::fetch_all('SELECT * FROM %t WHERE fid IN(%n) ORDER BY tid DESC %i', [$this->_table, $fid, DB::limit($start, $limit)], $this->_pk);
	}

}

