<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_notification extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'home_notification';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function delete_clear($new, $days) {
		$days = TIMESTAMP - intval($days) * 86400;
		DB::query('DELETE FROM %t WHERE new=%d AND dateline<%d', [$this->_table, $new, $days]);
	}

	public function delete_by_type($type, $uid = 0) {
		if(!$type) {
			return;
		}
		$uid = $uid ? ' AND '.DB::field('uid', $uid) : '';
		return DB::query('DELETE FROM %t WHERE type=%s %i', [$this->_table, $type, $uid]);
	}

	public function optimize() {
		DB::query('OPTIMIZE TABLE %t', [$this->_table], true);
	}

	public function fetch_by_fromid_uid($id, $idtype, $uid) {
		return DB::fetch_first('SELECT * FROM %t WHERE from_id=%d AND from_idtype=%s AND uid=%d', [$this->_table, $id, $idtype, $uid]);
	}

	public function delete_by_id_uid($id, $uid) {
		DB::query('DELETE FROM %t WHERE id=%d AND uid=%d', [$this->_table, $id, $uid]);
	}

	public function delete_by_uid($uid) {
		DB::query('DELETE FROM %t WHERE uid IN (%n) OR authorid IN (%n)', [$this->_table, $uid, $uid]);
	}

	public function delete_by_uid_type_authorid($uid, $type, $authorid) {
		return DB::query('DELETE FROM %t WHERE uid=%d AND type=%s AND authorid=%d', [$this->_table, $uid, $type, $authorid]);
	}

	public function fetch_all_by_authorid_fromid($authorid, $fromid, $type) {
		return DB::fetch_all('SELECT * FROM %t WHERE authorid=%d AND from_id=%d AND type=%s', [$this->_table, $authorid, $fromid, $type]);
	}

	public function ignore($uid, $type = '', $category = '', $new = true, $from_num = true) {
		$uid = intval($uid);
		$update = [];
		if($new) {
			$update['new'] = 0;
		}
		if($from_num) {
			$update['from_num'] = 0;
		}
		$where = ['uid' => $uid, 'new' => 1];
		if($type) {
			$where['type'] = $type;
		}
		if($category !== '') {
			$category = match ($category) {
				'mypost' => 1,
				'interactive' => 2,
				'system' => 3,
				'manage' => 4,
				default => 0,
			};
			$where['category'] = $category;
		}
		if($update) {
			DB::update($this->_table, $update, $where);
		}
	}

	public function count_by_uid($uid, $new, $type = '', $category = '') {
		$new = intval($new);
		$type = $type ? ' AND '.DB::field('type', $type) : '';
		if($category !== '') {
			$category = match ($category) {
				'mypost' => 1,
				'interactive' => 2,
				'system' => 3,
				'manage' => 4,
				default => 0,
			};
			$category = ' AND '.DB::field('category', $category);
		}
		$new = $new != '-1' ? ' AND '.DB::field('new', $new) : '';
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d %i %i %i', [$this->_table, $uid, $new, $category, $type]);
	}

	public function fetch_all_by_uid($uid, $new, $type = 0, $start = 0, $perpage = 0, $category = '') {
		$new = intval($new);
		$type = $type ? ' AND '.DB::field('type', $type) : '';
		if($category !== '') {
			$category = match ($category) {
				'mypost' => 1,
				'interactive' => 2,
				'system' => 3,
				'manage' => 4,
				'follow' => 5,
				'follower' => 6,
				default => 0,
			};
			$category = ' AND '.DB::field('category', $category);
		}
		$new = $new != '-1' ? ' AND '.DB::field('new', $new) : '';
		return DB::fetch_all('SELECT * FROM %t WHERE uid=%d %i %i %i ORDER BY dateline DESC %i', [$this->_table, $uid, $new, $category, $type, DB::limit($start, $perpage)]);
	}
}

