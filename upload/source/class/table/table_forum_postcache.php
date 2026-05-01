<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_postcache extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_postcache';
		$this->_pk = 'pid';
		$this->_pre_cache_key = 'forum_postcache_';

		parent::__construct();
	}

	public function delete_by_dateline($dateline) {
		return DB::delete($this->_table, DB::field('dateline', $dateline, '<'));
	}
}

