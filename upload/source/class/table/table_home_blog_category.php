<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_blog_category extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'home_blog_category';
		$this->_pk = 'catid';

		parent::__construct();
	}

	public function fetch_all_by_displayorder() {
		return DB::fetch_all('SELECT * FROM %t ORDER BY displayorder', [$this->_table], $this->_pk);
	}

	public function fetch_all_numkey($numkey) {
		$allow_numkey = ['portal', 'articles', 'num'];
		if(!in_array($numkey, $allow_numkey)) {
			return null;
		}
		return DB::fetch_all("SELECT catid, $numkey FROM %t", [$this->_table], $this->_pk);
	}

	public function update_num_by_catid($num, $catid, $numplus = true, $numlimit = false) {
		$args = [$this->_table, $num, $catid];
		if($numlimit !== false) {
			$sql = ' AND num>0';
			$args[] = $numlimit;
		}
		return DB::query('UPDATE %t SET num='.(($numplus) ? 'num+' : '')."%d WHERE catid=%d {$sql}", $args);
	}

	public function fetch_catname_by_catid($catid) {
		return DB::result_first('SELECT catname FROM %t WHERE catid=%d', [$this->_table, $catid]);
	}

}

