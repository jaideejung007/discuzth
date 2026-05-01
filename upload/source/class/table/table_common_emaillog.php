<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_emaillog extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_emaillog';
		$this->_pk = 'logid';

		parent::__construct();
	}

	public function get_lastemail_by_uese($uid, $emailtype = 0, $svctype = 1, $email) {
		return DB::fetch_first('SELECT * FROM %t WHERE uid = %d AND emailtype = %d AND svctype = %d AND email = %s  AND status >= 0 ORDER BY dateline DESC', [$this->_table, $uid, $emailtype, $svctype, $email]);
	}

	public function get_email_by_ut($uid, $time) {
		$dateline = time() - $time;
		return DB::fetch_all('SELECT dateline FROM %t WHERE uid = %d AND dateline > %d AND status >= 0', [$this->_table, $uid, $dateline]);
	}

	public function get_email_by_et($email, $time) {
		$dateline = time() - $time;
		return DB::fetch_all('SELECT dateline FROM %t WHERE email = %s AND dateline > %d AND status >= 0', [$this->_table, $email, $dateline]);
	}

	public function count_email_by_time($time) {
		$dateline = time() - $time;
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE dateline > %d', [$this->_table, $dateline]);
	}

	public function fetch_all_by_dateline($dateline, $glue = '>=') {
		$glue = helper_util::check_glue($glue);
		return DB::fetch_all("SELECT * FROM %t WHERE dateline{$glue}%d ORDER BY dateline", [$this->_table, $dateline], $this->_pk);
	}

}

