<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_polloption extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_polloption';
		$this->_pk = 'polloptionid';

		parent::__construct();
	}

	public function update_vote($polloptionids, $voterids, $num = 1) {
		DB::query('UPDATE %t SET votes=votes+\'%d\', voterids=CONCAT(voterids,%s) WHERE polloptionid IN (%n)', [$this->_table, $num, $voterids, $polloptionids], false, true);
	}

	public function fetch_all_by_tid($tids, $displayorder = 0, $limit = 0) {
		$sqladd = '';
		if($displayorder) {
			$sqladd = ' ORDER BY displayorder';
		}
		if($limit) {
			$sqladd .= ' LIMIT '.intval($limit);
		}
		return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('tid', $tids).$sqladd, [$this->_table]);
	}

	public function delete_safe_tid($tid, $polloptionid = 0) {
		$sqladd = '';
		if($polloptionid) {
			$sqladd = DB::field('polloptionid', intval($polloptionid)).' AND ';
		}
		DB::query("DELETE FROM %t WHERE $sqladd tid=%d", [$this->_table, $tid]);
	}

	public function delete_by_tid($tids) {
		return DB::delete($this->_table, DB::field('tid', $tids));
	}

	public function update_safe_tid($polloptionid, $tid, $displayorder, $polloption = '') {
		$param = [$this->_table, $displayorder];
		if($polloption) {
			$sqladd = ', polloption=%s';
			$param[] = $polloption;
		}
		$param[] = $polloptionid;
		$param[] = $tid;
		DB::query('UPDATE %t SET displayorder=%d'.$sqladd.' WHERE polloptionid=%d AND tid=%d', $param);
	}

	public function fetch_count_by_tid($tid) {
		return DB::fetch_first('SELECT MAX(votes) AS max, SUM(votes) AS total FROM %t WHERE tid=%d', [$this->_table, $tid]);
	}
}

