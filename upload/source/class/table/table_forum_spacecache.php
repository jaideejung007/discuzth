<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_spacecache extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_spacecache';
		$this->_pk = '';

		parent::__construct();
	}

	public function fetch($id, $force_from_db = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::fetch($id, $force_from_db);
		} else {
			return $this->fetch_spacecache($id, $force_from_db);
		}
	}

	public function fetch_all($ids, $force_from_db = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::fetch_all($ids, $force_from_db);
		} else {
			return $this->fetch_all_spacecache($ids, $force_from_db);
		}
	}

	public function delete($val, $unbuffered = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::delete($val, $unbuffered);
		} else {
			return $this->delete_spacecache($val, $unbuffered);
		}
	}

	public function fetch_spacecache($uid, $variable) {
		return DB::fetch_first('SELECT * FROM %t WHERE uid=%d AND variable=%s', [$this->_table, $uid, $variable]);
	}

	public function fetch_all_spacecache($uids, $variables) {
		if(empty($uids) || empty($variables)) {
			return [];
		}
		return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('uid', $uids).' AND '.DB::field('variable', $variables), [$this->_table]);
	}

	public function delete_spacecache($uid, $variable) {
		return DB::query('DELETE FROM %t WHERE uid=%d AND variable=%s', [$this->_table, $uid, $variable]);
	}

}

