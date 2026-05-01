<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_threadprofile extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_threadprofile';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function fetch_all($ids = null, $force_from_db = false) {
		
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::fetch_all($ids, $force_from_db);
		} else {
			return $this->fetch_all_threadprofile();
		}
	}

	public function fetch_all_threadprofile() {
		return DB::fetch_all('SELECT * FROM %t', [$this->table], $this->_pk);
	}

	public function reset_default($tpid) {
		DB::query('UPDATE %t SET `global`=0', [$this->table]);
		DB::query('UPDATE %t SET `global`=1 WHERE id=%d', [$this->table, $tpid]);
	}

}

