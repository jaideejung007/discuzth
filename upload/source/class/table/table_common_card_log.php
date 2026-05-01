<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_card_log extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_card_log';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function fetch_by_operation($operation) {
		return DB::fetch_first('SELECT * FROM %t WHERE operation=%d ORDER BY dateline DESC LIMIT 1', [$this->_table, $operation]);
	}

	public function fetch_all_by_operation($operation, $start = 0, $limit = 0) {
		return DB::fetch_all('SELECT * FROM %t WHERE operation=%d ORDER BY dateline DESC '.DB::limit($start, $limit), [$this->_table, $operation]);
	}

	public function count_by_operation($operation) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE operation=%d', [$this->_table, $operation]);
	}
}

