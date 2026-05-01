<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_stat extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_stat';
		$this->_pk = 'daytime';

		parent::__construct();
	}

	public function updatestat($uid, $type, $primary = 0, $num = 1) {
		$nowdaytime = dgmdate(TIMESTAMP, 'Ymd');
		$type = addslashes($type);
		if($primary) {
			$setarr = [
				'uid' => intval($uid),
				'daytime' => $nowdaytime,
				'type' => $type
			];
			if(table_common_statuser::t()->check_exists($uid, $nowdaytime, $type)) {
				return false;
			} else {
				table_common_statuser::t()->insert($setarr);
			}
		}
		$num = abs(intval($num));
		if(DB::result_first('SELECT COUNT(*) FROM '.DB::table($this->_table)." WHERE `daytime` = '$nowdaytime'")) {
			DB::query('UPDATE '.DB::table($this->_table)." SET `$type`=`$type`+$num WHERE `daytime` = '$nowdaytime'");
		} else {
			DB::query('INSERT INTO '.DB::table($this->_table)." (`daytime`, `$type`) VALUES ('$nowdaytime', '$num') ON DUPLICATE KEY UPDATE `$type` = `$type` + '$num'");
			table_common_statuser::t()->clear_by_daytime($nowdaytime);
		}
	}

	public function fetch_post_avg() {
		return DB::result_first('SELECT AVG(post) FROM '.DB::table($this->_table));
	}

	public function fetch_all($ids, $force_from_db = false, $null = '*') {
		
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::fetch_all($ids, $force_from_db);
		} else {
			return $this->fetch_all_stat($ids, $force_from_db, $null);
		}
	}

	public function fetch_all_stat($begin, $end, $field = '*') {
		$data = [];
		$query = DB::query('SELECT %i FROM %t WHERE daytime>=%d AND daytime<=%d ORDER BY daytime', [$field, $this->_table, $begin, $end]);
		while($value = DB::fetch($query)) {
			$data[$value['daytime']] = $value;
		}
		return $data;
	}

	public function fetch_all_by_daytime($daytime, $start = 0, $limit = 0, $sort = 'ASC') {
		$wheresql = '';
		$parameter = [$this->_table];
		if($daytime) {
			$wheresql = 'WHERE daytime>=%d';
			$parameter[] = $daytime;
		}
		return DB::fetch_all("SELECT * FROM %t $wheresql ORDER BY daytime $sort".DB::limit($start, $limit), $parameter);
	}
}

