<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_admincp_session extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {
		$this->_table = 'common_admincp_session';
		$this->_pk = 'uid';

		parent::__construct();
	}

	public function fetch($id, $force_from_db = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::fetch($id, $force_from_db);
		} else {
			return $this->fetch_session($id, $force_from_db);
		}
	}

	public function delete($val, $unbuffered = false, $null = 3600) {
		
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::delete($val, $unbuffered);
		} else {
			return $this->delete_session($val, $unbuffered, $null);
		}
	}

	public function update($val, $data, $unbuffered = false, $low_priority = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::update($val, $data, $unbuffered, $low_priority);
		} else {
			return $this->update_session($val, $data, $unbuffered);
		}
	}

	public function fetch_session($uid, $panel) {
		$sql = 'SELECT * FROM %t WHERE uid=%d AND panel=%d';
		return DB::fetch_first($sql, [$this->_table, $uid, $panel]);
	}

	public function fetch_all_by_panel($panel) {
		return DB::fetch_all('SELECT * FROM %t WHERE panel=%d', [$this->_table, $panel], 'uid');
	}

	public function delete_session($uid, $panel, $ttl = 3600) {
		$sql = 'DELETE FROM %t WHERE (uid=%d AND panel=%d) OR dateline<%d';
		DB::query($sql, [$this->_table, $uid, $panel, TIMESTAMP - intval($ttl)]);
	}

	public function update_session($uid, $panel, $data) {
		if(!empty($data) && is_array($data)) {
			DB::update($this->_table, $data, ['uid' => $uid, 'panel' => $panel]);
		}
	}

}

