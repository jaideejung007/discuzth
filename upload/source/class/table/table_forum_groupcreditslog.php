<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_groupcreditslog extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_groupcreditslog';
		$this->_pk = '';

		parent::__construct();
	}

	public function check_logdate($fid, $uid, $logdate) {
		return DB::result_first('SELECT logdate FROM %t WHERE fid=%d AND uid=%d AND logdate=%s', [$this->_table, $fid, $uid, $logdate]);
	}

	public function delete_by_fid($fid) {
		if(empty($fid)) {
			return false;
		}
		DB::query('DELETE FROM '.DB::table('forum_groupcreditslog').' WHERE '.DB::field('fid', $fid));
	}
}

