<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_threadtype extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_threadtype';
		$this->_pk = 'typeid';

		parent::__construct();
	}

	public function fetch_all_for_cache() {
		return DB::fetch_all('SELECT t.typeid AS sortid, t.super, tt.optionid, tt.title, tt.type, tt.unit, tt.rules, tt.identifier, tt.description, tt.permprompt, tv.required, tv.unchangeable, tv.search, tv.subjectshow, tt.expiration, tt.protect
			FROM '.DB::table('forum_threadtype').' t
			LEFT JOIN '.DB::table('forum_typevar').' tv ON t.typeid=tv.sortid
			LEFT JOIN '.DB::table('forum_typeoption')." tt ON tv.optionid=tt.optionid
			WHERE t.special='1' AND tv.available='1'
			ORDER BY tv.displayorder");
	}

	public function fetch_all_for_order($typeid = []) {
		if(!empty($typeid)) {
			$where = ' WHERE '.DB::field('typeid', $typeid);
		}
		return DB::fetch_all('SELECT * FROM '.DB::table('forum_threadtype')." $where ORDER BY displayorder");
	}

	public function checkname($name) {
		return DB::result_first('SELECT typeid FROM %t WHERE name=%s', [$this->_table, $name]);
	}
}

