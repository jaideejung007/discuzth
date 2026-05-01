<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_statuser extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_statuser';
		$this->_pk = '';

		parent::__construct();
	}

	public function check_exists($uid, $daytime, $type) {

		$setarr = [
			'uid' => intval($uid),
			'daytime' => intval($daytime),
			'type' => $type
		];
		if(DB::result_first('SELECT COUNT(*) FROM '.DB::table($this->_table).' WHERE '.DB::implode_field_value($setarr, ' AND '))) {
			return true;
		} else {
			return false;
		}
	}

	public function clear_by_daytime($daytime) {
		$daytime = intval($daytime);
		DB::delete('common_statuser', "`daytime` != '$daytime'");
	}
}

