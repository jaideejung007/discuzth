<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_friendlink extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_friendlink';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function fetch_all_by_displayorder($type = '') {
		$args = [$this->_table];
		if($type) {
			$sql = 'WHERE (`type` & %s > 0)';
			$args[] = $type;
		}
		return DB::fetch_all("SELECT * FROM %t $sql ORDER BY displayorder", $args, $this->_pk);
	}

}

