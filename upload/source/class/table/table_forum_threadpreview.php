<?php
/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_threadpreview extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_threadpreview';
		$this->_pk = 'tid';

		parent::__construct();
	}

	public function count_by_tid($tid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE tid=%d', [$this->_table, $tid]);
	}

	public function update_relay_by_tid($tid, $value) {
		return DB::query('UPDATE %t SET relay=relay+\'%d\' WHERE tid=%d', [$this->_table, $value, $tid]);
	}

	public function delete_by_tid($tids) {
		return DB::delete($this->_table, DB::field('tid', $tids));
	}
}

