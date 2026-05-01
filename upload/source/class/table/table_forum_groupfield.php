<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_groupfield extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_groupfield';
		$this->_pk = 'fid';

		parent::__construct();
	}

	public function truncate() {
		DB::query('TRUNCATE '.DB::table('forum_groupfield'));
	}

	public function delete_by_type($types, $fid = 0) {
		if(empty($types)) {
			return false;
		}
		$addfid = $fid ? " AND fid='".intval($fid)."'" : '';
		DB::query('DELETE FROM '.DB::table('forum_groupfield').' WHERE '.DB::field('type', $types).$addfid);
	}

	public function fetch_all_group_cache($fid, $types = [], $privacy = 0) {
		$typeadd = $types && is_array($types) ? 'AND '.DB::field('type', $types) : '';
		return DB::fetch_all('SELECT fid, dateline, type, data FROM '.DB::table('forum_groupfield')." WHERE fid=%d AND privacy=%d $typeadd", [$fid, $privacy]);
	}
}

