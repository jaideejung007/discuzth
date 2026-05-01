<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_member_field_home extends discuz_table_archive {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_member_field_home';
		$this->_pk = 'uid';
		$this->_pre_cache_key = 'common_member_field_home_';

		parent::__construct();
	}

	public function increase($uids, $creditarr) {
		$uids = array_map('intval', (array)$uids);
		$sql = [];
		$allowkey = ['addsize', 'addfriend'];
		foreach($creditarr as $key => $value) {
			if(($value = intval($value)) && in_array($key, $allowkey)) {
				$sql[] = "`$key`=`$key`+'$value'";
			}
		}
		if(!empty($sql)) {
			DB::query('UPDATE ' .DB::table($this->_table). ' SET ' .implode(',', $sql). ' WHERE uid IN (' .dimplode($uids). ')', 'UNBUFFERED');
			$this->increase_cache($uids, $creditarr);
		}
	}
}

