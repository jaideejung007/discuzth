<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_portal_article_count extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'portal_article_count';
		$this->_pk = 'aid';

		parent::__construct();
	}

	public function increase($ids, $data) {
		$ids = array_map('intval', (array)$ids);
		$sql = [];
		$allowkey = ['commentnum', 'viewnum', 'favtimes', 'sharetimes'];
		foreach($data as $key => $value) {
			if(($value = intval($value)) && in_array($key, $allowkey)) {
				$sql[] = "`$key`=`$key`+'$value'";
			}
		}
		if(!empty($sql)) {
			DB::query('UPDATE '.DB::table($this->_table).' SET '.implode(',', $sql).' WHERE aid IN ('.dimplode($ids).')', 'UNBUFFERED');
		}
	}

	public function fetch_all_hotarticle($wheresql, $dateline) {
		if(!empty($wheresql) && ($wheresql = (string)$wheresql) && $dateline = dintval($dateline)) {
			return DB::fetch_all('SELECT at.* FROM '.DB::table($this->_table).' ac, '.DB::table('portal_article_title')." at WHERE $wheresql AND at.dateline>'$dateline' AND ac.aid=at.aid ORDER BY ac.viewnum DESC LIMIT 10");
		} else {
			return [];
		}
	}
}

