<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_word extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_word';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function fetch_by_find($find) {
		return DB::fetch_first('SELECT * FROM %t WHERE find=%s', [$this->_table, $find]);
	}

	public function fetch_all_order_type_find() {
		return DB::fetch_all('SELECT * FROM %t ORDER BY type ASC, find ASC', [$this->_table], $this->_pk);
	}

	public function fetch_all($ids = [], $force_from_db = false) {
		
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::fetch_all($ids, $force_from_db);
		} else {
			return $this->fetch_all_word();
		}
	}

	public function fetch_all_word() {
		return DB::fetch_all('SELECT * FROM %t', [$this->_table], $this->_pk);
	}

	public function fetch_all_by_type_find($type = null, $find = null, $start = 0, $limit = 0) {
		$parameter = [$this->_table];
		$wherearr = [];
		if($type !== null) {
			$parameter[] = $type;
			$wherearr[] = '`type`=%d';
		}
		if($find !== null) {
			$parameter[] = '%'.addslashes(stripsearchkey($find)).'%';
			$wherearr[] = '`find` LIKE %s';
		}
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		return DB::fetch_all("SELECT * FROM %t $wheresql ORDER BY find ASC".DB::limit($start, $limit), $parameter);
	}


	public function update_by_type($types, $data) {
		if(!empty($types) && !empty($data) && is_array($data)) {
			$types = array_map('intval', (array)$types);
			return DB::update($this->_table, $data, 'type IN ('.dimplode($types).')');
		}
		return 0;
	}

	public function update_by_find($find, $data) {
		if(!empty($find) && !empty($data) && is_array($data)) {
			return DB::update($this->_table, $data, DB::field('find', $find));
		}
		return 0;
	}

	public function count_by_type_find($type = null, $find = null) {
		$parameter = [$this->_table];
		$wherearr = [];
		if($type !== null) {
			$parameter[] = $type;
			$wherearr[] = '`type`=%d';
		}
		if($find !== null) {
			$parameter[] = '%'.addslashes(stripsearchkey($find)).'%';
			$wherearr[] = '`find` LIKE %s';
		}
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		return DB::result_first("SELECT COUNT(*) FROM %t $wheresql", $parameter);
	}

}

