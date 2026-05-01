<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_doing_attachment extends discuz_table
{
	public static function t()
	{
		static $_instance;
		if (!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct()
	{

		$this->_table = 'home_doing_attachment';
		$this->_pk = 'aid';

		parent::__construct();
	}

	private function _check_id($idtype, $ids)
	{
		if ($idtype == 'pid' && $this->_table == 'forum_attachment_unused') {
			return false;
		}
		if (in_array($idtype, ['aid', 'tid', 'pid', 'uid', 'doid']) && !empty($ids)) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($val, $unbuffered = false)
	{
		if (defined('DISCUZ_DEPRECATED')) {
			throw new Exception('UnsupportedOperationException');
		} else {
			return $this->delete_attachment($val, $unbuffered);
		}
	}

	public function update($val, $data, $unbuffered = false, $low_priority = false)
	{
		if (defined('DISCUZ_DEPRECATED')) {
			throw new Exception('UnsupportedOperationException');
		} else {
			return $this->update_attachment($val, $data, $unbuffered);
		}
	}

	public function insert($data, $return_insert_id = false, $replace = false, $silent = false, $null = false)
	{
		
		if (defined('DISCUZ_DEPRECATED')) {
			throw new Exception('UnsupportedOperationException');
		} else {
			return $this->insert_attachment($data, $return_insert_id, $replace, $silent, $null);
		}
	}

	public function fetch($id, $force_from_db = false, $null = false)
	{
		
		if (defined('DISCUZ_DEPRECATED')) {
			throw new Exception('UnsupportedOperationException');
		} else {
			return $this->fetch_attachment($id, $force_from_db, $null);
		}
	}

	public function fetch_all($ids, $force_from_db = false, $null1 = false, $null2 = false)
	{
		
		if (defined('DISCUZ_DEPRECATED')) {
			throw new Exception('UnsupportedOperationException');
		} else {
			return $this->fetch_all_attachment($ids, $force_from_db, $null1, $null2);
		}
	}

	public function delete_attachment($val)
	{
		return DB::delete($this->_table, DB::field($this->_pk, $val));
	}

	public function delete_by_id($idtype, $id)
	{
		return $this->_check_id($idtype, $id) ? DB::delete($this->_table, DB::field($idtype, $id)) : false;
	}

	public function delete_by_uid($uid)
	{
		if (!$uid) {
			return null;
		}
		return DB::delete($this->_table, DB::field('uid', $uid));
	}

	public function update_attachment($val, $data)
	{
		if (!$data) {
			return;
		}
		return DB::update($this->_table, $data, DB::field($this->_pk, $val));
	}

	public function insert_attachment($data, $return_insert_id = false, $replace = false, $silent = false)
	{
		if (!$data) {
			return;
		}
		return DB::insert($this->_table, $data, $return_insert_id, $replace, $silent);
	}

	public function fetch_attachment($tableid, $aid, $isimage = false)
	{
		$isimage = $isimage === false ? '' : ' AND ' . DB::field('isimage', $isimage);
		return !empty($aid) ? DB::fetch_first('SELECT * FROM %t WHERE %i %i', [$this->_table, DB::field($this->_pk, $aid), $isimage]) : [];
	}

	public function fetch_max_image($tableid, $idtype, $id)
	{
		return $this->_check_id($idtype, $id) ? DB::fetch_first('SELECT * FROM %t WHERE %i AND isimage IN (1, -1) ORDER BY width DESC LIMIT 1', [$this->_table, DB::field($idtype, $id)]) : [];
	}

	public function count_by_id($tableid, $idtype, $id)
	{
		return $this->_check_id($idtype, $id) ? DB::result_first('SELECT COUNT(*) FROM %t WHERE %i', [$this->_table, DB::field($idtype, $id)]) : 0;
	}

	public function count_image_by_id($tableid, $idtype, $id)
	{
		return $this->_check_id($idtype, $id) ? DB::result_first('SELECT COUNT(*) FROM %t WHERE %i AND isimage IN (1, -1)', [$this->_table, DB::field($idtype, $id)]) : 0;
	}

	public function fetch_all_attachment($tableid, $aids, $remote = false, $isimage = false)
	{
		$remote = $remote === false ? '' : ' AND ' . DB::field('remote', $remote);
		$isimage = $isimage === false ? '' : ' AND ' . DB::field('isimage', $isimage);
		return !empty($aids) ? DB::fetch_all('SELECT * FROM %t WHERE %i %i %i', [$this->_table, DB::field($this->_pk, $aids), $remote, $isimage]) : [];
	}

	public function fetch_all_by_id($tableid, $idtype, $ids, $orderby = '', $isimage = false, $isprice = false, $remote = false, $limit = false)
	{
		if ($this->_check_id($idtype, $ids)) {
			$attachments = [];
			if ($orderby) {
				$orderby = 'ORDER BY ' . $orderby;
			} else {
				
				$orderby = 'ORDER BY displayorder ASC, dateline ASC';
			}
			$isimage = $isimage === false ? '' : ' AND ' . DB::field('isimage', $isimage);
			$isprice = $isprice === false ? '' : ' AND ' . DB::field('price', 0, '>');
			$remote = $remote === false ? '' : ' AND ' . DB::field('remote', $remote);
			$limit = $limit < 1 ? '' : DB::limit(0, $limit);
			$query = DB::query('SELECT * FROM %t WHERE %i %i %i %i %i %i', [$this->_table, DB::field($idtype, $ids), $isimage, $isprice, $remote, $orderby, $limit]);
			while ($value = DB::fetch($query)) {
				$attachments[] = $value;
			}
			return $attachments;
		} else {
			return [];
		}
	}

	public function reset_picid($tableid, $newids)
	{
		if ($newids) {
			DB::query("UPDATE %t SET picid='0' WHERE picid IN (%n)", [$this->_table, (array)$newids], false, true);
		}
	}

	public function fetch_by_aid_uid($tableid, $aid, $uid)
	{
		$query = DB::query('SELECT * FROM %t WHERE aid=%d AND uid=%d', [$this->_table, $aid, $uid]);
		return DB::fetch($query);
	}

	public function fetch_all_by_pid_width($tableid, $pids, $width)
	{
		return DB::fetch_all("SELECT * FROM %t WHERE %i AND isimage IN ('1', '-1') AND width>=%d", [$this->_table, DB::field('pid', $pids), $width]);
	}

	public function get_total_filesize()
	{
		$attachsize = 0;
		for ($i = 0; $i < 10; $i++) {
			$attachsize += DB::result_first('SELECT SUM(filesize) FROM ' . DB::table('forum_attachment_' . $i));
		}
		return $attachsize;
	}
	public function update_by_aid($aids, $data) 
	{
		if (empty($aids) || empty($data)) {
			return false;
		}
		
		return DB::update($this->_table, $data, DB::field($this->_pk, $aids));
	}
}
