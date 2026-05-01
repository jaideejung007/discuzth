<?php
/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_threadcalendar extends discuz_table {

	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_threadcalendar';
		$this->_pk = 'cid';

		parent::__construct();
	}

	public function fetch_by_fid_dateline($fid, $dateline = 0, $order = 'dateline', $sort = 'DESC') {
		$parameter = [$this->_table];
		$wherearr = [];
		$wheresql = '';
		if($fid) {
			$wherearr[] = 'fid=%d';
			$parameter[] = $fid;
		}
		if($dateline) {
			$wherearr[] = 'dateline=%d';
			$parameter[] = $dateline;
		}
		if($wherearr) {
			$wheresql = ' WHERE '.implode(' AND ', $wherearr);
		}
		return DB::fetch_first('SELECT * FROM %t '.$wheresql.' ORDER BY '.DB::order($order, $sort), $parameter, $this->_pk);
	}

	public function fetch_all_by_dateline($dateline) {
		$dateline = dintval($dateline);
		if($dateline) {
			return DB::fetch_all('SELECT * FROM %t WHERE dateline=%d', [$this->_table, $dateline], 'fid');
		} else {
			return [];
		}
	}

	public function fetch_all_by_fid_dateline($fids, $dateline = 0) {
		$parameter = [$this->_table];
		$wherearr = [];
		$wheresql = '';
		$fids = dintval($fids, true);
		if($fids) {
			$wherearr[] = is_array($fids) ? 'fid IN(%n)' : 'fid=%d';
			$parameter[] = $fids;
		}
		$dateline = dintval($dateline);
		if($dateline) {
			$wherearr[] = 'dateline=%d';
			$parameter[] = $dateline;
		}
		if($wherearr) {
			$wheresql = ' WHERE '.implode(' AND ', $wherearr);
		}
		return DB::fetch_all('SELECT * FROM %t '.$wheresql, $parameter, 'fid');
	}

	public function insert_multiterm($dataarr) {
		$allkey = ['fid', 'dateline', 'hotnum'];
		$sql = [];
		foreach($dataarr as $key => $value) {
			if(is_array($value)) {
				$fid = dintval($value['fid']);
				$dateline = dintval($value['dateline']);
				$hotnum = dintval($value['hotnum']);
				$sql[] = "($fid, $dateline, $hotnum)";
			}
		}
		if($sql) {
			return DB::query('INSERT INTO '.DB::table($this->_table).' (`fid`, `dateline`, `hotnum`) VALUES '.implode(',', $sql), true);
		}
		return false;
	}
}

