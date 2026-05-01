<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_attachtype extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_attachtype';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function fetch_all_data() {
		return DB::fetch_all('SELECT * FROM %t', [$this->_table], $this->_pk);
	}

	public function fetch_all_by_fid($fid) {
		return DB::fetch_all('SELECT * FROM %t WHERE fid=%d', [$this->_table, $fid], $this->_pk);
	}

	public function delete_by_id_fid($id, $fid) {
		$id = dintval($id, is_array($id));
		$fid = dintval($fid, is_array($fid));
		if(is_array($id) && empty($id) || is_array($fid) && empty($fid)) {
			return 0;
		}
		return DB::delete($this->_table, DB::field('id', $id).' AND '.DB::field('fid', $fid));
	}

	public function count_by_extension_fid($extension, $fid = null) {
		$parameter = [$this->_table];
		$wherearr = [];
		if($fid !== null) {
			$wherearr[] = 'fid=%d';
			$parameter[] = $fid;
		}
		$parameter[] = $extension;
		$wherearr[] = 'extension=%s';
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		return DB::result_first('SELECT COUNT(*) FROM %t'.$wheresql, $parameter);
	}

}

