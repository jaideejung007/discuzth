<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_member_status extends discuz_table_archive {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_member_status';
		$this->_pk = 'uid';
		$this->_pre_cache_key = 'common_member_status_';

		parent::__construct();
	}

	public function increase($uids, $setarr) {
		$uids = array_map('intval', (array)$uids);
		$sql = [];
		$allowkey = ['buyercredit', 'sellercredit', 'favtimes', 'sharetimes'];
		foreach($setarr as $key => $value) {
			if(($value = intval($value)) && in_array($key, $allowkey)) {
				$sql[] = "`$key`=`$key`+'$value'";
			}
		}
		if(!empty($sql)) {
			DB::query('UPDATE '.DB::table($this->_table).' SET '.implode(',', $sql).' WHERE uid IN ('.dimplode($uids).')', 'UNBUFFERED');
			$this->increase_cache($uids, $setarr);
		}
	}

	public function count_by_ip($ips) {
		return !empty($ips) ? DB::result_first('SELECT COUNT(*) FROM %t WHERE regip IN(%n) OR lastip IN (%n)', [$this->_table, $ips, $ips]) : 0;
	}

	public function fetch_all_by_ip($ips, $start, $limit) {
		$data = [];
		if(!empty($ips) && $limit) {
			$data = DB::fetch_all('SELECT * FROM %t WHERE regip IN(%n) OR lastip IN (%n) LIMIT %d, %d', [$this->_table, $ips, $ips, $start, $limit], 'uid');
		}
		return $data;
	}

	public function fetch_all_orderby_lastpost($uids, $start, $limit) {
		$uids = dintval($uids, true);
		if($uids) {
			return DB::fetch_all('SELECT * FROM %t WHERE uid IN(%n) ORDER BY lastpost DESC '.DB::limit($start, $limit), [$this->_table, $uids], $this->_pk);
		}
		return [];
	}

	public function count_by_lastactivity_invisible($timestamp, $invisible = 0) {
		$addsql = '';
		if($invisible === 1) {
			$addsql = ' AND invisible = 1';
		} elseif($invisible === 2) {
			$addsql = ' AND invisible = 0';
		}
		return $timestamp ? DB::result_first('SELECT COUNT(*) FROM %t WHERE lastactivity >= %d'.$addsql, [$this->_table, $timestamp]) : 0;
	}


	public function fetch_all_by_lastactivity_invisible($timestamp, $invisible = 0, $start = 0, $limit = 0) {
		$data = [];
		if($timestamp) {
			$addsql = '';
			if($invisible === 1) {
				$addsql = ' AND invisible = 1';
			} elseif($invisible === 2) {
				$addsql = ' AND invisible = 0';
			}
			$data = DB::fetch_all('SELECT * FROM %t WHERE lastactivity >= %d'.$addsql.' ORDER BY lastactivity DESC'.DB::limit($start, $limit), [$this->_table, $timestamp], $this->_pk);
		}
		return $data;
	}

	public function fetch_all_onlines($uids, $lastactivity, $start = 0, $limit = 0) {
		$data = [];
		$uids = dintval($uids, true);
		if(!empty($uids)) {
			$ppp = ($ppp = getglobal('ppp')) ? $ppp + 30 : 100;
			if(count($uids) > $ppp) {
				$uids = array_slice($uids, 0, $ppp);
			}
			$length = $limit ? $limit : $start;
			$i = 0;
			foreach($this->fetch_all($uids) as $uid => $member) {
				if($member['lastactivity'] >= $lastactivity) {
					$data[$uid] = $member;
					if($length && $i >= $length) {
						break;
					}
					$i++;
				}
			}
		}
		return $data;
	}
}

