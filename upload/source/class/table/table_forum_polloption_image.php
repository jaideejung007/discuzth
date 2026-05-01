<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_polloption_image extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_polloption_image';
		$this->_pk = 'aid';
		parent::__construct();
	}

	public function fetch_all_by_tid($tids) {
		return DB::fetch_all('SELECT * FROM %t WHERE tid'.(is_array($tids) ? ' IN(%n)' : '=%d'), [$this->_table, $tids], 'poid');
	}

	public function count_by_aid_uid($aid, $uid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE aid=%d AND uid=%d', [$this->_table, $aid, $uid]);
	}

	public function delete_by_tid($tids) {
		return DB::delete($this->_table, DB::field('tid', $tids));
	}

	public function clear() {
		require_once libfile('function/forum');
		$deltids = [];
		$query = DB::query('SELECT tid, attachment, thumb FROM %t WHERE tid=0 AND dateline<=%d', [$this->_table, TIMESTAMP - 86400]);
		while($attach = DB::fetch($query)) {
			dunlink($attach);
			$deltids[] = $attach['tid'];
		}
		if($deltids) {
			$this->delete_by_tid($deltids);
		}
	}
}

