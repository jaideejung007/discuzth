<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_threadimage extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_threadimage';
		$this->_pk = '';

		parent::__construct();
	}

	public function delete($val, $unbuffered = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::delete($val, $unbuffered);
		} else {
			return $this->delete_threadimage($val);
		}
	}

	public function delete_threadimage($tid) {
		return ($tid = dintval($tid)) ? DB::delete('forum_threadimage', "tid='$tid'") : false;
	}

	public function delete_by_tid($tids) {
		return !empty($tids) ? DB::delete($this->_table, DB::field('tid', $tids)) : false;
	}

	public function fetch_all_order_by_tid($start = 0, $limit = 0) {
		return DB::fetch_all('SELECT * FROM %t ORDER BY tid DESC '.DB::limit($start, $limit), [$this->_table], 'tid');
	}

	public function fetch_all_order_by_tid_for_guide($start = 0, $limit = 0, $fids = 0) {
		$tidsql = '';
		$fids = dintval($fids, true);
		if($fids) {
			$tidsql = is_array($fids) && $fids ? ' AND t.fid IN('.dimplode($fids).')' : ' AND t.fid='.$fids;
		}
		return DB::fetch_all('SELECT i.* FROM %t i LEFT JOIN %t t ON i.tid = t.tid WHERE 1 %i ORDER BY i.tid DESC '.DB::limit($start, $limit), [$this->_table, 'forum_thread', $tidsql], 'tid');
	}
}

