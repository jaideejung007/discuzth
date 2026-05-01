<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_follow_feed extends discuz_table {
	private $_ids = [];
	private $_cids = [];
	private $_tids = [];
	private $_archiver_table = 'home_follow_feed_archiver';

	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'home_follow_feed';
		$this->_pk = 'feedid';

		parent::__construct();
	}

	public function fetch_all_by_uid($uids = 0, $archiver = false, $start = 0, $limit = 0) {

		$data = [];
		$parameter = [$archiver ? $this->_archiver_table : $this->_table];
		$wherearr = [];
		if(!empty($uids)) {
			$uids = dintval($uids, true);
			$wherearr[] = is_array($uids) && $uids ? 'uid IN(%n)' : 'uid=%d';
			$parameter[] = $uids;
		}
		$wheresql = !empty($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		$query = DB::query("SELECT * FROM %t $wheresql ORDER BY dateline DESC ".DB::limit($start, $limit), $parameter);
		while($row = DB::fetch($query)) {
			$data[$row['feedid']] = $row;
			$this->_tids[$row['tid']] = $row['tid'];
		}

		return $data;
	}

	public function fetch_all_by_dateline($dateline, $glue = '>=') {
		$glue = helper_util::check_glue($glue);
		return DB::fetch_all("SELECT * FROM %t WHERE dateline{$glue}%d ORDER BY dateline", [$this->_table, $dateline], $this->_pk);
	}

	public function fetch_by_feedid($feedid, $archiver = false) {
		return DB::fetch_first('SELECT * FROM %t WHERE feedid=%d', [$archiver ? $this->_archiver_table : $this->_table, $feedid]);
	}

	public function count_by_uid_tid($uid, $tid, $archiver = false) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d AND tid=%d', [$archiver ? $this->_archiver_table : $this->_table, $uid, $tid]);
	}

	public function count_by_uid_dateline($uids = [], $dateline = 0, $archiver = 0) {
		$count = 0;
		$parameter = [$archiver ? $this->_archiver_table : $this->_table];
		$wherearr = [];
		if(!empty($uids)) {
			$uids = dintval($uids, true);
			$wherearr[] = is_array($uids) && $uids ? 'uid IN(%n)' : 'uid=%d';
			$parameter[] = $uids;
		}
		if($dateline) {
			$wherearr[] = 'dateline>%d';
			$parameter[] = $dateline;
		}
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		$count = DB::result_first("SELECT COUNT(*) FROM %t $wheresql", $parameter);
		return $count;
	}

	public function insert_archiver($data) {
		if(!empty($data) && is_array($data)) {
			return DB::insert($this->_archiver_table, $data, false, true);
		}
		return 0;
	}

	public function delete_by_feedid($feedid, $archiver = false) {
		$feedid = dintval($feedid, true);
		if($feedid) {
			return DB::delete($archiver ? $this->_archiver_table : $this->_table, DB::field('feedid', $feedid));
		}
		return 0;
	}

	public function delete_by_uid($uids) {
		$uids = dintval($uids, true);
		$delnum = 0;
		if($uids) {
			$delnum = DB::delete($this->_table, DB::field('uid', $uids));
			$delnum_archiver = DB::delete($this->_archiver_table, DB::field('uid', $uids));
			if(is_int($delnum_archiver)) {
				$delnum += $delnum_archiver;
			}
		}
		return $delnum;
	}

	public function get_ids() {
		return $this->_ids;
	}

	public function get_tids() {
		return $this->_tids;
	}

	public function get_cids() {
		return $this->_cids;
	}

}

