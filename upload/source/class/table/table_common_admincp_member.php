<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_admincp_member extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_admincp_member';
		$this->_pk = 'uid';

		parent::__construct();
	}

	public function update_cpgroupid_by_cpgroupid($val, $data) {
		if(!is_array($data)) {
			return null;
		}
		return DB::update('common_admincp_member', $data, DB::field('cpgroupid', $val));
	}

	public function count_by_uid($uid) {
		return DB::result_first('SELECT count(*) FROM %t WHERE uid=%d', [$this->_table, $uid]);
	}

	public function fetch_all_uid_by_gid_perm($gid, $perm) {
		return DB::fetch_all('SELECT uid FROM %t am LEFT JOIN %t ap ON am.cpgroupid=ap.cpgroupid WHERE am.cpgroupid=%d OR ap.perm=%s', [$this->_table, 'common_admincp_perm', $gid, $perm]);
	}

	public function fetch_perm_by_uid_perm($uid, $perm) {
		return DB::result_first('SELECT ap.perm FROM %t am LEFT JOIN %t ap ON ap.cpgroupid=am.cpgroupid WHERE am.uid=%d AND ap.perm=%s', [$this->_table, 'common_admincp_perm', $uid, $perm]);
	}
}

