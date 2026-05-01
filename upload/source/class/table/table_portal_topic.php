<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_portal_topic extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'portal_topic';
		$this->_pk = 'topicid';

		parent::__construct();
	}

	public function count_by_search_where($wherearr) {
		$wheresql = empty($wherearr) ? '' : implode(' AND ', $wherearr);
		return DB::result_first('SELECT COUNT(*) FROM '.DB::table($this->_table).($wheresql ? ' WHERE '.$wheresql : ''));
	}

	public function fetch_all_by_search_where($wherearr, $ordersql, $start, $limit) {
		$wheresql = empty($wherearr) ? '' : implode(' AND ', $wherearr);
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).($wheresql ? ' WHERE '.$wheresql : '').' '.$ordersql.DB::limit($start, $limit), null, 'topicid');
	}

	public function fetch_by_name($name) {
		return $name ? DB::fetch_first('SELECT * FROM %t WHERE name=%s LIMIT 1', [$this->_table, $name]) : false;
	}

	public function increase($ids, $data) {
		$ids = array_map('intval', (array)$ids);
		$sql = [];
		$allowkey = ['commentnum', 'viewnum'];
		foreach($data as $key => $value) {
			if(($value = intval($value)) && in_array($key, $allowkey)) {
				$sql[] = "`$key`=`$key`+'$value'";
			}
		}
		if(!empty($sql)) {
			DB::query('UPDATE '.DB::table($this->_table).' SET '.implode(',', $sql).' WHERE topicid IN ('.dimplode($ids).')', 'UNBUFFERED');
		}
	}

	public function fetch_all_by_title($idtype, $subject) {
		if(empty($idtype) || !is_string($idtype) || empty($subject)) {
			return [];
		}
		$parameter = [$this->_table];
		$or = $wheresql = '';
		$subject = explode(',', str_replace(' ', '', $subject));
		for($i = 0; $i < count($subject); $i++) {
			if(preg_match('/\{(\d+)\}/', $subject[$i])) {
				$subject[$i] = preg_replace("/\\\{(\d+)\\\}/", ".{0,\\1}", preg_quote($subject[$i], '/'));
				$wheresql .= " $or title REGEXP %s";
				$parameter[] = $subject[$i];
			} else {
				$wheresql .= " $or title LIKE %s";
				$parameter[] = '%'.$subject[$i].'%';
			}
			$or = 'OR';
		}
		return DB::fetch_all("SELECT $idtype FROM %t WHERE $wheresql", $parameter);
	}

	public function repair_htmlmade($ids) {
		if(($ids = dintval($ids, true))) {
			return DB::update($this->_table, ['htmlmade' => 0], DB::field($this->_pk, $ids));
		}
		return false;
	}

	public function fetch_all_topicid_by_dateline($dateline) {
		$data = [];
		$where = [];

		if($dateline) {
			$where[] = DB::field('dateline', intval($dateline), '>=');
		}
		$where[] = "closed='0'";
		if($where) {
			$data = DB::fetch_all('SELECT topicid FROM '.DB::table($this->_table).' WHERE '.implode(' AND ', $where).' LIMIT 20000', NULL, $this->_pk);
		}
		return $data;
	}
}

