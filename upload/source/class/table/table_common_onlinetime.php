<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_onlinetime extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_onlinetime';
		$this->_pk = 'uid';

		parent::__construct();
	}

	public function update_onlinetime($uid, $total, $thismonth, $lastupdate) {
		if(($uid = intval($uid))) {
			DB::query('UPDATE '.DB::table('common_onlinetime')."
			SET total=total+'$total', thismonth=thismonth+'$thismonth', lastupdate='".$lastupdate."' WHERE ".DB::field($this->_pk, $uid));
			return DB::affected_rows();
		}
		return false;
	}

	public function range_by_field($start = 0, $limit = 0, $orderby = '', $sort = '') {
		$orderby = in_array($orderby, ['thismonth', 'total', 'lastupdate'], true) ? $orderby : '';
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).($orderby ? ' WHERE '.$orderby.' >0 ORDER BY '.DB::order($orderby, $sort) : '').' '.DB::limit($start, $limit), null, $this->_pk);
	}

	public function update_thismonth() {
		return DB::update($this->_table, ['thismonth' => 0]);
	}

}

