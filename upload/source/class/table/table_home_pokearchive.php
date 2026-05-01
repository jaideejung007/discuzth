<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_pokearchive extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'home_pokearchive';
		$this->_pk = 'pid';

		parent::__construct();
	}

	public function delete_by_uid_or_fromuid($uids) {
		$uids = dintval($uids, is_array($uids));
		if($uids) {
			return DB::delete($this->_table, DB::field('uid', $uids).' OR '.DB::field('fromuid', $uids));
		}
		return 0;
	}

	public function fetch_all_by_pokeuid($pokeuid) {
		return DB::fetch_all('SELECT * FROM %t WHERE pokeuid=%d ORDER BY dateline', [$this->_table, $pokeuid]);
	}

	public function delete_by_dateline($dateline) {
		DB::query('DELETE FROM %t WHERE dateline<%d', [$this->_table, $dateline]);
	}

}

