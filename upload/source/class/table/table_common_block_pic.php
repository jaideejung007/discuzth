<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_block_pic extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_block_pic';
		$this->_pk = 'picid';

		parent::__construct();
	}

	public function fetch_all_by_bid_itemid($bid, $itemid = []) {
		return $bid ? DB::fetch_all('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field('bid', $bid).($itemid ? ' AND '.DB::field('itemid', $itemid) : '')) : [];
	}

	public function insert_by_bid($bid, $data) {
		if($bid && $data && is_array($data)) {
			$data = daddslashes($data);
			$str = [];
			foreach($data as $value) {
				$str[] = "('{$value['bid']}', '{$value['pic']}', '{$value['picflag']}', '{$value['type']}')";
			}
			if($str) {
				DB::query('INSERT INTO '.DB::table($this->_table).' (bid, pic, picflag, `type`) VALUES '.implode(',', $str));
			}
		}
	}

	public function count_by_bid_pic($bid, $pic) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE bid=%d AND pic=%s', [$this->_table, $bid, $pic]);
	}
}

