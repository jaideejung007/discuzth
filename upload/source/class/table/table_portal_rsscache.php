<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_portal_rsscache extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'portal_rsscache';
		$this->_pk = 'aid';

		parent::__construct();
	}

	public function fetch_all_by_catid($catid, $limit = 20) {
		return $catid ? DB::fetch_all('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field('catid', $catid).' ORDER BY dateline DESC LIMIT '.dintval($limit), null, 'aid') : [];
	}
}

