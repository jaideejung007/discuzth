<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_admincp_group extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_admincp_group';
		$this->_pk = 'cpgroupid';

		parent::__construct();
	}

	public function fetch_by_cpgroupname($name) {
		return $name ? DB::fetch_first('SELECT * FROM %t WHERE cpgroupname=%s', [$this->_table, $name]) : null;
	}
}

