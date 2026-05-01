<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


class table_common_tag extends discuz_table {
	
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	
	public function __construct() {
		$this->_table = 'common_tag';
		$this->_pk = 'tagid';
		$this->_pre_cache_key = 'common_tag_';
		parent::__construct();
	}

	
	public function fetch_by_tagid($tagid) {
		return DB::fetch_first('SELECT * FROM %t WHERE tagid=%d', [$this->_table, $tagid]);
	}

	
	public function fetch_all_by_status($status = NULL, $tagname = '', $startlimit = 0, $count = 0, $returncount = 0, $order = '') {
		if($status === NULL) {
			$statussql = 'status<>3';
		} else {
			$statussql = 'status='.intval($status);
		}
		$data = [$this->_table];
		if($tagname) {
			$namesql = ' AND tagname LIKE %s';
			$data[] = '%'.$tagname.'%';
		}
		if($returncount) {
			return DB::result_first("SELECT count(*) FROM %t WHERE $statussql $namesql", $data);
		}
		return DB::fetch_all("SELECT * FROM %t WHERE $statussql $namesql ORDER BY ".DB::order('tagid', $order). ' ' .DB::limit($startlimit, $count), $data);
	}

	
	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::insert($data, $return_insert_id, $replace, $silent);
		} else {
			$return_insert_id = $return_insert_id === false ? 0 : $return_insert_id;
			return $this->insert_tag($data, $return_insert_id);
		}
	}

	
	public function insert_tag($tagname, $status = 0) {
		DB::query('INSERT INTO %t (tagname, status, related_count, hot_score, created_at) VALUES (%s, %d, 0, 0, %d)', [$this->_table, $tagname, $status, TIMESTAMP]);
		return DB::insert_id();
	}

	
	public function get_byids($ids) {
		if(empty($ids)) {
			return [];
		}
		if(!is_array($ids)) {
			$ids = [$ids];
		}
		return DB::fetch_all('SELECT * FROM %t WHERE tagid IN (%n)', [$this->_table, $ids], 'tagid');
	}

	
	public function get_bytagname($tagname, $type) {
		if(empty($tagname)) {
			return [];
		}
		$statussql = $type != 'uid' ? ' AND status<\'3\'' : ' AND status=\'3\'';
		return DB::fetch_first('SELECT * FROM %t WHERE tagname=%s '.$statussql, [$this->_table, $tagname]);
	}

	
	public function fetch_info($tagid, $tagname = '') {
		if(empty($tagid) && empty($tagname)) {
			return [];
		}
		$addsql = $sqlglue = '';
		if($tagid) {
			$addsql = ' tagid=' .intval($tagid);
			$sqlglue = ' AND ';
		}
		if($tagname) {
			$addsql .= $sqlglue.' '.DB::field('tagname', $tagname);
		}
		return DB::fetch_first('SELECT * FROM ' .DB::table('common_tag')." WHERE $addsql");
	}

	
	public function delete_byids($ids) {
		if(empty($ids)) {
			return false;
		}
		if(!is_array($ids)) {
			$ids = [$ids];
		}
		return DB::query('DELETE FROM %t WHERE tagid IN (%n)', [$this->_table, $ids]);
	}

	
	public function fetch_all_by_hot($status = NULL, $startlimit = 0, $count = 0, $order = 'DESC', $order_by = 'hot_score') {
		if($status === NULL) {
			$statussql = 'status<>3';
		} else {
			$statussql = 'status='.intval($status);
		}
		if($order_by == 'rand'){
			$ordersql = " ORDER BY rand()";
		}else{
			$ordersql = " ORDER BY ".DB::order($order_by, $order);
		}
		return DB::fetch_all("SELECT * FROM %t WHERE $statussql $ordersql" .DB::limit($startlimit, $count), [$this->_table]);
	}

	
	public function fetch_hot_by_tagids($tagids) {
		if(empty($tagids)) {
			return [];
		}
		return DB::fetch_all('SELECT tagid, hot_score FROM %t WHERE tagid IN (%n)', [$this->_table, $tagids], 'tagid');
	}

	
	public function increase($tagids, $setarr) {
		$tagids = array_map('intval', (array)$tagids);
		$sql = [];
		$allowkey = ['related_count'];
		foreach($setarr as $key => $value) {
			if(($value = intval($value)) && in_array($key, $allowkey)) {
				$sql[] = "`$key`=`$key`+'$value'";
			}
		}
		if(!empty($sql)) {
			DB::query('UPDATE ' .DB::table($this->_table). ' SET ' .implode(',', $sql). ' WHERE tagid IN (' .dimplode($tagids). ')', 'UNBUFFERED');
			$this->increase_cache($tagids, $setarr);
		}
	}
}