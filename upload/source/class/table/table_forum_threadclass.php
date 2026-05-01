<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_threadclass extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_threadclass';
		$this->_pk = 'typeid';

		parent::__construct();
	}

	public function fetch_by_fid_name($fid, $name) {
		return DB::fetch_first('SELECT * FROM %t WHERE fid=%d AND name=%s', [$this->_table, $fid, $name]);
	}

	public function fetch_all_by_typeid($typeids) {
		$typeids = dintval($typeids, is_array($typeids));
		if($typeids) {
			return DB::fetch_all('SELECT * FROM %t WHERE typeid IN(%n) ORDER BY displayorder', [$this->_table, $typeids], $this->_pk);
		}
		return [];
	}

	public function fetch_all_by_fid($fid) {
		return DB::fetch_all('SELECT * FROM %t WHERE fid=%d ORDER BY displayorder', [$this->_table, $fid], $this->_pk);
	}

	public function fetch_all_by_typeid_fid($typeid, $fid) {
		$typeid = dintval($typeid, is_array($typeid));
		$fid = dintval($fid, is_array($fid));
		$parameter = [$this->_table, $typeid, $fid];
		$wheresql = is_array($typeid) && $typeid ? 'typeid IN(%n)' : 'typeid=%d';
		$wheresql .= ' AND '.(is_array($fid) && $fid ? 'fid IN(%n)' : 'fid=%d');
		return DB::fetch_all('SELECT * FROM %t WHERE '.$wheresql, $parameter, $this->_pk);
	}

	public function update_by_fid($fid, $data) {
		$fid = dintval($fid, is_array($fid));
		if(is_array($fid) && empty($fid)) {
			return 0;
		}
		if(!empty($data) && is_array($data)) {
			return DB::update($this->_table, $data, DB::field('fid', $fid));
		}
		return 0;
	}

	public function update_by_typeid($typeid, $data) {
		$typeid = dintval($typeid, is_array($typeid));
		if(is_array($typeid) && empty($typeid)) {
			return 0;
		}
		if(!empty($data) && is_array($data)) {
			return DB::update($this->_table, $data, DB::field('typeid', $typeid));
		}
		return 0;
	}

	public function update_by_typeid_fid($typeid, $fid, $data) {
		$typeid = dintval($typeid, is_array($typeid));
		$fid = dintval($fid, is_array($fid));
		if(empty($typeid) || empty($fid)) {
			return 0;
		}
		if(!empty($data) && is_array($data)) {
			return DB::update($this->_table, $data, DB::field('typeid', $typeid).' AND '.DB::field('fid', $fid));
		}
		return 0;
	}

	public function delete_by_typeid($typeid) {
		$typeid = dintval($typeid, is_array($typeid));
		if(empty($typeid)) {
			return 0;
		}
		return DB::delete($this->_table, DB::field('typeid', $typeid));
	}

	public function delete_by_typeid_fid($typeid, $fid) {
		$typeid = dintval($typeid, is_array($typeid));
		$fid = dintval($fid, is_array($fid));
		if(empty($typeid) || empty($fid)) {
			return 0;
		}
		return DB::delete($this->_table, DB::field('typeid', $typeid).' AND '.DB::field('fid', $fid));
	}

	public function count_by_fid($fid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE fid=%d', [$this->_table, $fid]);
	}

}

