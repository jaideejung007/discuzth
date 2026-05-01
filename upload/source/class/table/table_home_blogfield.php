<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_blogfield extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'home_blogfield';
		$this->_pk = 'blogid';

		parent::__construct();
	}

	public function delete_by_uid($uids) {
		if(!$uids) {
			return null;
		}
		return DB::delete($this->_table, DB::field('uid', $uids));
	}

	public function fetch_targetids_by_blogid($blogid) {
		return DB::fetch_first('SELECT target_ids, hotuser FROM %t WHERE blogid = %d', [$this->_table, $blogid]);
	}

	public function fetch_tag_by_blogid($blogid) {
		return DB::result_first('SELECT tag FROM %t WHERE blogid = %d', [$this->_table, $blogid]);
	}

}

