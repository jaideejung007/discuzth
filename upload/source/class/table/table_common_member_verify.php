<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_member_verify extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_member_verify';
		$this->_pk = 'uid';
		$this->_pre_cache_key = 'common_member_verify_';

		parent::__construct();
	}

	public function fetch_all_by_vid($vid, $flag, $uids = []) {
		$parameter = [$this->_table];
		if($vid > 0 && $vid < 8) {
			$wherearr = [];
			if($uids) {
				$wherearr[] = is_array($uids) ? 'uid IN(%n)' : 'uid=%d';
				$parameter[] = $uids;
			}
			$parameter[] = $flag;
			$wherearr[] = "verify{$vid}=%d";
			return DB::fetch_all('SELECT * FROM %t WHERE '.implode(' AND ', $wherearr), $parameter, $this->_pk);
		} else {
			return [];
		}
	}

	public function fetch_all_search($uid, $vid, $username = '', $order = 'dateline', $start = 0, $limit = 0, $sort = 'DESC') {
		$condition = $this->search_condition($uid, $vid, $username);
		$ordersql = !empty($order) ? ' ORDER BY '.$order.' '.$sort : '';
		return DB::fetch_all("SELECT * FROM %t v, %t m  $condition[0] $ordersql ".DB::limit($start, $limit), $condition[1], $this->_pk);

	}

	public function count_by_uid($uid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d', [$this->_table, $uid]);
	}

	public function count_by_search($uid, $vid, $username = '') {
		$condition = $this->search_condition($uid, $vid, $username);
		return DB::result_first('SELECT COUNT(*) FROM %t v, %t m '.$condition[0], $condition[1]);
	}

	public function search_condition($uid, $vid, $username) {
		$parameter = [$this->_table, 'common_member'];
		$wherearr = [];
		if($uid) {
			$parameter[] = $uid;
			$wherearr[] = 'v.uid=%d';
		}
		if($vid > 0 && $vid < 8) {
			$parameter[] = $vid;
			$wherearr[] = 'v.verify%d=1';
		}
		if(!empty($username)) {
			$parameter[] = '%'.$username.'%';
			$wherearr[] = 'm.username LIKE %s';
		}
		$wherearr[] = 'v.uid=m.uid';
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		return [$wheresql, $parameter];

	}

}

