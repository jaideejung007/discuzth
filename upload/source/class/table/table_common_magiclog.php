<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_magiclog extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_magiclog';
		$this->_pk = '';

		parent::__construct();
	}

	public function fetch_all_by_magicid_action($magicid, $action, $start = 0, $limit = 0) {
		$sql = [];
		if($magicid) {
			$magicid = dintval($magicid, true);
			$sql[] = DB::field('magicid', $magicid);
		}
		if($action) {
			$sql[] = DB::field('action', $action);
		}
		$wheresql = ($sql ? 'WHERE ' : '').implode(' AND ', $sql);
		return DB::fetch_all('SELECT * FROM %t %i ORDER BY dateline DESC '.DB::limit($start, $limit), [$this->_table, $wheresql]);
	}

	public function fetch_all_by_magicid_action_uid($mid, $action, $uid, $start = 0, $limit = 0) {
		return DB::fetch_all('SELECT * FROM %t WHERE magicid=%d AND action=%d AND uid!=%d ORDER BY dateline DESC '.DB::limit($start, $limit), [$this->_table, $mid, $action, $uid]);
	}

	public function fetch_all_by_uid_action($uid, $action, $start, $limit, $order = 'DESC') {
		return DB::fetch_all('SELECT * FROM %t WHERE uid=%d AND action=%d '.($order ? 'ORDER BY '.DB::order('dateline', $order) : '').' '.($limit ? DB::limit($start, $limit) : ''), [$this->_table, $uid, $action]);
	}

	public function fetch_all_by_targetuid_action($uid, $action, $start, $limit, $order = 'DESC') {
		return DB::fetch_all('SELECT * FROM %t WHERE targetuid=%d AND action=%d '.($order ? 'ORDER BY '.DB::order('dateline', $order) : '').' '.($limit ? DB::limit($start, $limit) : ''), [$this->_table, $uid, $action]);
	}

	public function count_by_targetuid_action($uid, $action) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE targetuid=%d AND action=%d', [$this->_table, $uid, $action]);
	}

	public function count_by_magicid_action($magicid, $action) {
		$sql = [];
		if($magicid) {
			$magicid = dintval($magicid, true);
			$sql[] = DB::field('magicid', $magicid);
		}
		if($action) {
			$sql[] = DB::field('action', $action);
		}
		$wheresql = ($sql ? 'WHERE ' : '').implode(' AND ', $sql);
		return DB::result_first('SELECT COUNT(*) FROM %t %i', [$this->_table, $wheresql]);
	}

	public function count_by_action_uid_targetid_idtype_magicid($action, $uid, $targetid, $idtype, $magicid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE action=%d AND uid=%d AND targetid=%d AND idtype=%d AND magicid=%d', [$this->_table, $action, $uid, $targetid, $idtype, $magicid]);
	}

	public function count_by_uid_magicid_action_dateline($uid, $magicid, $action, $dateline) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d AND magicid=%d AND action=%d AND dateline>%d', [$this->_table, $uid, $magicid, $action, $dateline]);
	}

	public function count_by_uid_action($uid, $action) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d AND action=%d', [$this->_table, $uid, $action]);
	}

	public function count_by_action_uid_dateline($action, $uid, $dateline, $maxdateline = 0) {
		$wherearr = [];
		$wherearr[] = 'action=%d';
		$wherearr[] = 'uid=%d';
		$wherearr[] = $maxdateline ? 'dateline BETWEEN %d AND %d' : 'dateline>%d';
		$parameter = [$this->_table, $action, $uid, $dateline];
		if($maxdateline) {
			$parameter[] = $maxdateline;
		}
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE '.implode(' AND ', $wherearr), $parameter);
	}

	public function delete_by_magicid($ids) {
		$ids = dintval($ids, true);
		if($ids) {
			return DB::delete($this->_table, DB::field('magicid', $ids));
		}
		return 0;
	}

	public function delete_by_removetime($removetime) {
		return DB::query('DELETE FROM %t WHERE dateline < %d', [$this->_table, $removetime]);
	}
}

