<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_faq extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_faq';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function fetch_all_by_fpid($fpid = '', $srchkw = '') {
		$sql = [];
		if($fpid !== '' && $fpid) {
			$sql[] = DB::field('fpid', $fpid);
		}
		if($srchkw) {
			$sql[] = DB::field('title', '%'.$srchkw.'%', 'like').' OR '.DB::field('message', '%'.$srchkw.'%', 'like');
		}
		$sql = implode(' AND ', $sql);
		if($sql) {
			$sql = 'WHERE '.$sql;
		}
		return DB::fetch_all('SELECT *  FROM %t  %i ORDER BY displayorder', [$this->_table, $sql]);
	}

	public function check_identifier($identifier, $id) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE identifier=%s AND id!=%s', [$this->_table, $identifier, $id]);
	}

}

