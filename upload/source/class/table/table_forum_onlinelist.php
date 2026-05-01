<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_onlinelist extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_onlinelist';
		$this->_pk = '';

		parent::__construct();
	}

	public function fetch_all_order_by_displayorder() {
		return DB::fetch_all('SELECT * FROM %t ORDER BY displayorder', [$this->_table]);
	}

	public function delete_all() {
		DB::query('DELETE FROM %t', [$this->_table]);
	}

	public function delete_by_groupid($groupid) {
		$groupid = is_array($groupid) ? array_map('intval', (array)$groupid) : dintval($groupid);
		if($groupid) {
			return DB::delete($this->_table, DB::field('groupid', $groupid));
		}
		return 0;
	}

	public function update_by_groupid($groupid, $data) {
		$groupid = is_array($groupid) ? array_map('intval', (array)$groupid) : dintval($groupid);
		if($groupid && $data && is_array($data)) {
			return DB::update($this->_table, $data, DB::field('groupid', $groupid));
		}
		return 0;
	}
}

