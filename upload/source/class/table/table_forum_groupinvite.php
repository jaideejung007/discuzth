<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_groupinvite extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_groupinvite';
		$this->_pk = 'fid';

		parent::__construct();
	}

	public function fetch_uid_by_inviteuid($fid, $inviteuid) {
		return DB::result_first('SELECT uid FROM %t WHERE fid=%d AND inviteuid=%d', [$this->_table, $fid, $inviteuid]);
	}

	public function fetch_all_inviteuid($fid, $inviteuids, $uid) {
		if(empty($fid) || empty($uid) || empty($inviteuids)) {
			return [];
		}
		return DB::fetch_all('SELECT inviteuid FROM %t WHERE fid=%d AND '.DB::field('inviteuid', $inviteuids).' AND uid=%d', [$this->_table, $fid, $uid]);
	}

	public function delete_by_inviteuid($fid, $inviteuid) {
		DB::query('DELETE FROM %t WHERE fid=%d AND inviteuid=%d', [$this->_table, $fid, $inviteuid]);
	}

	public function affected_rows() {
		return DB::affected_rows();
	}
}

