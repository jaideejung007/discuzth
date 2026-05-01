<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_failedlogin extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_failedlogin';
		$this->_pk = '';

		parent::__construct();
	}

	public function fetch_username($ip, $username) {
		return DB::fetch_first('SELECT * FROM %t WHERE ip=%s AND username=%s', [$this->_table, $ip, $username]);
	}

	public function fetch_ip($ip) {
		return DB::fetch_first('SELECT * FROM %t WHERE ip=%s', [$this->_table, $ip]);
	}

	public function delete_old($time) {
		DB::query('DELETE FROM %t WHERE lastupdate<%d', [$this->_table, TIMESTAMP - intval($time)], 'UNBUFFERED');
	}

	public function update_failed($ip) {
		DB::query('UPDATE %t SET count=count+1, lastupdate=%d WHERE ip=%s', [$this->_table, TIMESTAMP, $ip]);
	}

}

