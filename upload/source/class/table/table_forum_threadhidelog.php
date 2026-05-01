<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_threadhidelog extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_threadhidelog';
		$this->_pk = '';

		parent::__construct();
	}

	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::insert($data, $return_insert_id, $replace, $silent);
		} else {
			return $this->insert_hidelog($data, $return_insert_id);
		}
	}

	public function insert_hidelog($tid, $uid) {
		if(!DB::fetch_first('SELECT * FROM %t WHERE tid=%d AND uid=%d', [$this->_table, $tid, $uid])) {
			DB::insert($this->_table, ['tid' => $tid, 'uid' => $uid]);
			DB::query('UPDATE %t SET hidden=hidden+1 WHERE tid=%d', ['forum_thread', $tid]);
		}
	}

	public function resetshow($tid) {
		$this->delete_by_tid($tid);
		DB::update('forum_thread', ['hidden' => 0], DB::field('tid', $tid));
	}


	public function delete_by_uid($uid) {
		return $uid ? DB::delete($this->_table, DB::field('uid', $uid)) : false;
	}

	public function delete_by_tid($tid) {
		DB::query('UPDATE %t SET hidden=0 WHERE tid IN (%n)', ['forum_thread', $tid]);
		return $tid ? DB::delete($this->_table, DB::field('tid', $tid)) : false;
	}

}

