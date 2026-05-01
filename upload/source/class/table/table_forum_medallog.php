<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_medallog extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_medallog';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function count_by_type($type) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE type=%d', [$this->_table, $type]);
	}

	public function count_by_uid($uid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d', [$this->_table, $uid]);
	}

	public function fetch_all_by_type($type) {
		return DB::fetch_all('SELECT * FROM %t WHERE type=%d ORDER BY dateline', [$this->_table, $type], $this->_pk);
	}

	public function fetch_all_lastmedal($limit) {
		return DB::fetch_all("SELECT * FROM %t WHERE type<'2' ORDER BY dateline DESC LIMIT %d", [$this->_table, $limit], $this->_pk);
	}

	public function fetch_all_by_expiration($expiration) {
		return DB::fetch_all('SELECT * FROM %t WHERE status=1 AND expiration>0 AND expiration<%d', [$this->_table, $expiration]);
	}

	public function fetch_all_by_uid($uid, $start, $limit) {
		return DB::fetch_all('SELECT * FROM %t WHERE uid=%d ORDER BY dateline DESC LIMIT %d,%d', [$this->_table, $uid, $start, $limit]);
	}

	public function update_type_by_uid_medalid($type, $uid, $medalid) {
		$type = intval($type);
		if(!$uid || !$medalid) {
			return;
		}
		DB::update($this->_table, ['type' => $type], DB::field('uid', $uid).' AND '.DB::field('medalid', $medalid));
	}

	public function fetch_all_by_type_medalid($type, $medalid, $start_limit, $lpp) {
		$where = [];
		if($type !== '') {
			$where[] = DB::field('type', $type);
		}
		if($medalid !== '') {
			$where[] = DB::field('medalid', $medalid);
		}
		$where = $where ? 'WHERE '.implode(' AND ', $where) : '';
		$start_limit = intval($start_limit);
		$lpp = intval($lpp);

		return DB::fetch_all('SELECT * FROM '.DB::table('forum_medallog')." $where ORDER BY dateline DESC LIMIT $start_limit, $lpp");
	}

	public function count_by_type_medalid($type, $medalid) {
		$where = [];
		if($type !== '') {
			$where[] = DB::field('type', $type);
		}
		if($medalid !== '') {
			$where[] = DB::field('medalid', $medalid);
		}
		$where = $where ? 'WHERE '.implode(' AND ', $where) : '';

		return DB::result_first('SELECT COUNT(*) FROM '.DB::table('forum_medallog')." $where");
	}

	public function count_by_verify_medalid($uid, $medalid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d AND medalid=%d AND type=2', [$this->_table, $uid, $medalid]);
	}

	public function delete_by_removetime($removetime) {
		return DB::query('DELETE FROM %t WHERE dateline < %d', [$this->_table, $removetime]);
	}
}

