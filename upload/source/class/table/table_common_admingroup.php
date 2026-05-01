<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_admingroup extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_admingroup';
		$this->_pk = 'admingid';

		parent::__construct();
	}

	public function fetch_all_merge_usergroup($gids = []) {
		$admingroups = empty($gids) ? $this->range() : $this->fetch_all($gids);
		$data = [];
		foreach(table_common_usergroup::t()->fetch_all_usergroup(array_keys($admingroups)) as $gid => $value) {
			$data[$gid] = array_merge($admingroups[$gid], $value);
		}
		return $data;
	}

	public function fetch_all_order() {
		return DB::fetch_all('SELECT u.radminid, u.groupid, u.grouptitle FROM '.DB::table('common_admingroup').' a LEFT JOIN '.DB::table('common_usergroup').' u ON u.groupid=a.admingid ORDER BY u.radminid, a.admingid');
	}
}

