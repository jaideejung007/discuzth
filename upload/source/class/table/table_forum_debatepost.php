<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_debatepost extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_debatepost';
		$this->_pk = 'pid';

		parent::__construct();
	}

	public function update_voters($pid, $uid) {
		DB::query("UPDATE %t SET voters=voters+1, voterids=CONCAT(voterids, '%d\t') WHERE pid=%d", [$this->_table, $uid, $pid]);
	}

	public function fetch_all_voters($tid, $number) {
		return DB::fetch_all("SELECT SUM(voters) as voters, stand, uid FROM %t WHERE tid=%d AND stand>'0' GROUP BY uid ORDER BY voters DESC LIMIT %d", [$this->_table, $tid, $number]);
	}

	public function get_stand_by_bestuid($tid, $bestuid, $excludeuids) {
		if(!$excludeuids) {
			return;
		}
		return DB::result_first("SELECT stand FROM %t WHERE tid=%d AND uid=%d AND stand>'0' AND %i LIMIT 1", [$this->_table, $tid, $bestuid, DB::field('uid', $excludeuids, 'notin')]);
	}

	public function get_numbers_by_bestuid($tid, $bestuid) {
		$return = DB::fetch_first('SELECT SUM(voters) AS voters, COUNT(*) AS replies FROM %t WHERE tid=%d AND uid=%d', [$this->_table, $tid, $bestuid]);
		return [$return['voters'], $return['replies']];
	}

	public function count_by_tid_stand($tid, $stand) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE tid=%d AND stand=%d', [$this->_table, $tid, $stand]);
	}

	public function get_firststand($tid, $uid) {
		return DB::result_first("SELECT stand FROM %t WHERE tid=%d AND uid=%d AND stand>'0' ORDER BY dateline LIMIT 1", [$this->_table, $tid, $uid]);
	}

	public function delete_by_tid($tids) {
		if(!$tids) {
			return;
		}
		return DB::delete($this->_table, DB::field('tid', $tids));
	}

}

