<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_collectionrelated extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_collectionrelated';
		$this->_pk = 'tid';
		$this->_pre_cache_key = 'forum_collectionrelated_';

		parent::__construct();
	}

	public function update_collection_by_ctid_tid($ctid, $tid, $replace = false) {
		if($replace === false) {
			$ctid .= "\t";
			$collection = 'CONCAT(collection, %s)';
		} else {
			$collection = '%s';
		}

		$result = DB::query('UPDATE %t SET collection='.$collection.' WHERE tid=%d', [$this->_table, $ctid, $tid]);
		if($this->_allowmem) {
			$this->clear_cache($tid);
			$this->clear_cache($tid, 'forum_collection_tid_');
		}
		return $result;
	}
}

