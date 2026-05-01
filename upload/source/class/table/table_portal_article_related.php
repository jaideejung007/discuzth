<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_portal_article_related extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'portal_article_related';
		$this->_pk = 'aid';

		parent::__construct();
	}

	public function delete_by_aid_raid($aid, $raid = null) {
		return ($aid = dintval($aid, true)) ? DB::delete($this->_table, DB::field('aid', $aid).($raid = dintval($raid) ? ' OR '.DB::field('raid', $raid) : '')) : false;
	}

	public function insert_batch($aid, $list) {
		$replaces = [];
		if(($aid = dintval($aid))) {
			$displayorder = 0;
			unset($list[$aid]);
			foreach($list as $value) {
				if(($value['aid'] = dintval($value['aid']))) {
					$replaces[] = "('$aid', '{$value['aid']}', '$displayorder')";
					$replaces[] = "('{$value['aid']}', '$aid', '0')";
					$displayorder++;
				}
			}
		}
		if($replaces) {
			DB::query('REPLACE INTO '.DB::table($this->_table).' (aid,raid,displayorder) VALUES '.implode(',', $replaces));
		}
	}

	public function fetch_all_by_aid($aid) {
		return ($aid = dintval($aid)) ? DB::fetch_all('SELECT * FROM %t WHERE aid=%d ORDER BY displayorder', [$this->_table, $aid], 'raid') : [];
	}
}

