<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_banned extends discuz_table {
	
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_banned';
		$this->_pk = 'id';

		$this->_pre_cache_key = 'common_banned_';
		$this->_cache_ttl = 600;

		parent::__construct();

		$this->_allowmem = $this->_allowmem && C::memory()->gotsortedset;
	}

	public function fetch_by_ip($ip) {
		return DB::fetch_first('SELECT * FROM %t WHERE ip=%s', [$this->_table, $ip]);
	}

	public function fetch_all_order_dateline() {
		return DB::fetch_all('SELECT * FROM %t ORDER BY dateline DESC', [$this->_table]);
	}

	public function fetch_all($ids = [], $force_from_db = false) {
		
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::fetch_all($ids, $force_from_db);
		} else {
			return $this->fetch_all_banned();
		}
	}

	public function fetch_all_banned() {
		return DB::fetch_all('SELECT * FROM %t', [$this->_table]);
	}

	public function delete_by_id($ids, $adminid, $adminname) {
		$ids = array_map('intval', (array)$ids);
		if($ids) {
			if($this->_allowmem) memory('rm', 'index', $this->_pre_cache_key);
			return DB::query('DELETE FROM %t WHERE id IN(%n) AND (1=%d OR `admin`=%s)', [$this->_table, $ids, $adminid, $adminname]);
		}
		return 0;
	}

	public function update_expiration_by_id($id, $expiration, $isadmin, $admin) {
		if($this->_allowmem) memory('rm', 'index', $this->_pre_cache_key);
		return DB::query('UPDATE %t SET expiration=%d WHERE id=%d AND (1=%d OR `admin`=%s)', [$this->_table, $expiration, $id, $isadmin, $admin]);
	}

	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		$cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
		if(!str_starts_with($data['lowerip'], '0x')) $data['lowerip'] = '0x'.$data['lowerip'];
		if(!str_starts_with($data['upperip'], '0x')) $data['upperip'] = '0x'.$data['upperip'];
		if($this->_allowmem) memory('rm', 'index', $this->_pre_cache_key);
		return DB::query(
			$cmd.' %t SET `ip`=%s, `lowerip`=%i, `upperip`=%i, `admin`=%s, `dateline`=%d, `expiration`=%d',
			[$this->_table, $data['ip'], $data['lowerip'], $data['upperip'], $data['admin'], $data['dateline'], $data['expiration']],
			$silent, !$return_insert_id
		);
	}

	public function check_banned($time_to_check, $ip) {
		$iphex = ip::ip_to_hex_str($ip);
		$banned = true;
		if($this->_allowmem) $banned = memory('zscore', 'index', $iphex, 0, $this->_pre_cache_key);
		if($banned === false || !$this->_allowmem) { 
			$iphex_val = '0x'.$iphex;
			$ret = DB::result_first(
				'SELECT id from %t WHERE expiration > %d AND lowerip <= %i AND upperip >= %i',
				[$this->_table, $time_to_check, $iphex_val, $iphex_val]
			);
			if($ret) {
				if($this->_allowmem) memory('zadd', 'index', $iphex, 1, $this->_pre_cache_key);
				return true;
			}
			if($this->_allowmem) memory('zadd', 'index', $iphex, 0, $this->_pre_cache_key);
			return false;
		}

		
		return $banned === 1;
	}

}

