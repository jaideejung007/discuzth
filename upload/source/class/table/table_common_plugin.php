<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_plugin extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_plugin';
		$this->_pk = 'pluginid';

		parent::__construct();
	}

	public function fetch_by_identifier($identifier) {
		return DB::fetch_first('SELECT * FROM %t WHERE identifier=%s', [$this->_table, $identifier]);
	}

	public function fetch_all_identifier($identifier) {
		return DB::fetch_all('SELECT * FROM %t WHERE identifier IN (%n)', [$this->_table, $identifier], 'identifier');
	}

	public function fetch_all_data($available = false) {
		$available = $available !== false ? 'WHERE available='.intval($available) : '';
		return DB::fetch_all('SELECT * FROM %t %i ORDER BY available DESC, pluginid DESC', [$this->_table, $available]);
	}

	public function fetch_all_by_identifier($identifier) {
		if(!$identifier) {
			return;
		}
		return DB::fetch_all('SELECT * FROM %t WHERE %i', [$this->_table, DB::field('identifier', $identifier)]);
	}

	public function fetch_by_pluginvarid($pluginid, $pluginvarid) {
		return DB::fetch_first('SELECT * FROM %t p, %t pv WHERE p.pluginid=%d AND pv.pluginid=p.pluginid AND pv.pluginvarid=%d',
			[$this->_table, 'common_pluginvar', $pluginid, $pluginvarid]);
	}

	public function delete_by_identifier($identifier) {
		if(!$identifier) {
			return;
		}
		DB::delete('common_plugin', DB::field('identifier', $identifier));
	}

}

