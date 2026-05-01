<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_favorite extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'home_favorite';
		$this->_pk = 'favid';

		parent::__construct();
	}

	public function fetch_all_by_uid_idtype($uid, $idtype, $favid = 0, $start = 0, $limit = 0) {
		$parameter = [$this->_table];
		$wherearr = [];
		if($favid) {
			$parameter[] = dintval($favid, is_array($favid));
			$wherearr[] = is_array($favid) ? 'favid IN(%n)' : 'favid=%d';
		}
		$parameter[] = $uid;
		$wherearr[] = 'uid=%d';
		if(!empty($idtype)) {
			$parameter[] = $idtype;
			$wherearr[] = 'idtype=%s';
		}
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';

		return DB::fetch_all("SELECT * FROM %t $wheresql ORDER BY dateline DESC ".DB::limit($start, $limit), $parameter, $this->_pk);
	}

	public function count_by_uid_idtype($uid, $idtype, $favid = 0) {
		$parameter = [$this->_table];
		$wherearr = [];
		if($favid) {
			$parameter[] = dintval($favid, is_array($favid));
			$wherearr[] = is_array($favid) ? 'favid IN(%n)' : 'favid=%d';
		}
		$parameter[] = $uid;
		$wherearr[] = 'uid=%d';
		if(!empty($idtype)) {
			$parameter[] = $idtype;
			$wherearr[] = 'idtype=%s';
		}
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		return DB::result_first("SELECT COUNT(*) FROM %t $wheresql ", $parameter);
	}

	public function fetch_by_id_idtype($id, $idtype, $uid = 0) {
		if($uid) {
			$uidsql = ' AND '.DB::field('uid', $uid);
		}
		return DB::fetch_first("SELECT * FROM %t WHERE id=%d AND idtype=%s $uidsql", [$this->_table, $id, $idtype]);
	}

	public function count_by_id_idtype($id, $idtype) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE id=%d AND idtype=%s', [$this->_table, $id, $idtype]);
	}

	public function delete_by_id_idtype($id, $idtype) {
		return DB::delete($this->_table, DB::field('id', $id).' AND '.DB::field('idtype', $idtype));
	}

	public function delete($val, $unbuffered = false, $uid = 0) {
		$val = dintval($val, is_array($val));
		if($val) {
			if($uid) {
				$uid = dintval($uid, is_array($uid));
			}
			return DB::delete($this->_table, DB::field($this->_pk, $val).($uid ? ' AND '.DB::field('uid', $uid) : ''), null, $unbuffered);
		}
		return !$unbuffered ? 0 : false;
	}

}

