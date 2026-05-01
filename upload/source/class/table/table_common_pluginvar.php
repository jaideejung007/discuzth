<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_pluginvar extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_pluginvar';
		$this->_pk = 'pluginvarid';

		parent::__construct();
	}

	public function fetch_all_by_pluginid($pluginid) {
		return DB::fetch_all('SELECT * FROM %t WHERE pluginid=%d ORDER BY displayorder', [$this->_table, $pluginid]);
	}

	public function fetch_all_visible_by_pluginid($pluginid) {
		return DB::fetch_all('SELECT * FROM %t WHERE pluginid=%d AND displayorder>=0 ORDER BY displayorder', [$this->_table, $pluginid]);
	}

	public function count_by_pluginid($pluginid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE pluginid=%d %i', [$this->_table, $pluginid, "AND (`type` NOT LIKE 'forum\_%' AND `type` NOT LIKE 'group\_%')"]);
	}

	public function count_by_pluginid_page($pluginid) {
		return DB::result_first("SELECT COUNT(*) FROM %t WHERE pluginid=%d AND type='stylePage'", [$this->_table, $pluginid]);
	}

	public function fetch_first_by_pluginid($pluginid) {
		return DB::fetch_first('SELECT * FROM %t WHERE pluginid=%d ORDER BY displayorder LIMIT 1', [$this->_table, $pluginid]);
	}

	public function update_by_variable($pluginid, $variable, $data) {
		if(!$pluginid || !$variable || !$data || !is_array($data)) {
			return;
		}
		DB::update($this->_table, $data, DB::field('pluginid', $pluginid).' AND '.DB::field('variable', $variable));
	}

	public function update_by_pluginvarid($pluginid, $pluginvarid, $data) {
		if(!$pluginid || !$pluginvarid || !$data || !is_array($data)) {
			return;
		}
		DB::update($this->_table, $data, DB::field('pluginid', $pluginid).' AND '.DB::field('pluginvarid', $pluginvarid));
	}

	public function check_variable($pluginid, $variable) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE pluginid=%d AND variable=%s', [$this->_table, $pluginid, $variable]);
	}

	public function delete_by_pluginid($pluginid) {
		if(!$pluginid) {
			return;
		}
		DB::delete($this->_table, DB::field('pluginid', $pluginid));
	}

	public function delete_by_variable($pluginid, $variable) {
		if(!$pluginid || !$variable) {
			return;
		}
		DB::delete($this->_table, DB::field('pluginid', $pluginid).' AND '.DB::field('variable', $variable));
	}

}

