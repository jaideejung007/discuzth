<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_editorblock extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_editorblock';
		$this->_pk = 'blockid';

		parent::__construct();
	}

	public function fetch_by_block_class($class) {
		return DB::fetch_first('SELECT * FROM %t WHERE class = %s', [$this->_table, $class]);
	}

	public function fetch_all_block_sort_id($order = 'DESC') {
		return DB::fetch_all("SELECT * FROM %t ORDER BY ".DB::order('sort', $order).", $this->_pk", [$this->_table]);
	}

	public function fetch_all_block_avaliable($fields = []) {
		$field = '*';
		if(!empty($fields)) {
			$field = implode(',', $fields);
		}
		return DB::fetch_all('SELECT '.$field." FROM %t WHERE available > %d ORDER BY `sort`, $this->_pk", [$this->_table, 0]);
	}

	public function count_all_blocks($order = 'DESC') {
		return DB::result_first('SELECT COUNT(*) FROM %t ORDER BY available DESC, '.DB::order('sort', $order).' ', [$this->_table]);
	}

	public function fetch_all_blocks($start = 0, $limit = 0, $order = 'DESC') {
		$blocks = [];
		$blocks = DB::fetch_all('SELECT * FROM '.DB::table($this->_table).'   ORDER BY available DESC, '.DB::order('sort', $order).' '.DB::limit($start, $limit));
		return $blocks;
	}

	public function fetch_all_block_by_type($type = [], $fields = ['identifier']) {
		$field = '*';
		if(!empty($fields)) {
			$field = implode(',', $fields);
		}
		return DB::fetch_all('SELECT '.$field." FROM %t WHERE available > %d AND type IN (%n) ORDER BY `sort`, $this->_pk", [$this->_table, 0, $type]);
	}
}

