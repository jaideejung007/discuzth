<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_syscache extends discuz_table {

	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_syscache';
		$this->_pk = 'cname';
		$this->_pre_cache_key = '';
		$this->_allowmem = memory('check');

		parent::__construct();
	}

	public function fetch($id, $force_from_db = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::fetch($id, $force_from_db);
		} else {
			return $this->fetch_syscache($id);
		}
	}

	public function fetch_all($ids, $force_from_db = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::fetch_all($ids, $force_from_db);
		} else {
			return $this->fetch_all_syscache($ids);
		}
	}

	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::insert($data, $return_insert_id, $replace, $silent);
		} else {
			return $this->insert_syscache($data, $return_insert_id);
		}
	}

	public function update($val, $data, $unbuffered = false, $low_priority = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::update($val, $data, $unbuffered, $low_priority);
		} else {
			return $this->update_syscache($val, $data);
		}
	}

	public function delete($val, $unbuffered = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::delete($val, $unbuffered);
		} else {
			return $this->delete_syscache($val);
		}
	}

	public function fetch_syscache($cachename) {
		$data = $this->fetch_all_syscache([$cachename]);
		return $data[$cachename] ?? false;
	}

	public function fetch_all_syscache($cachenames, $force = false) {
		$data = [];
		$cachenames = is_array($cachenames) ? $cachenames : [$cachenames];
		if($this->_allowmem && !$force) {
			$data = memory('get', $cachenames);
			$newarray = $data !== false ? array_diff($cachenames, array_keys($data)) : $cachenames;
			if(empty($newarray)) {
				return $data;
			} else {
				$cachenames = $newarray;
			}
		}

		$query = DB::query('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field('cname', $cachenames));
		while($syscache = DB::fetch($query)) {
			$data[$syscache['cname']] = $syscache['ctype'] ? dunserialize($syscache['data']) : $syscache['data'];
			if($this->_allowmem) {
				memory('set', $syscache['cname'], $data[$syscache['cname']]);
			}
		}

		foreach($cachenames as $name) {
			if(!isset($data[$name]) || $data[$name] === null) {
				$data[$name] = null;
				$this->_allowmem && (memory('set', $name, []));
			}
		}

		return $data;
	}

	public function insert_syscache($cachename, $data) {

		parent::insert([
			'cname' => $cachename,
			'ctype' => is_array($data) ? 1 : 0,
			'dateline' => TIMESTAMP,
			'data' => is_array($data) ? serialize($data) : $data,
		], false, true);

		if($this->_allowmem && memory('exists', $cachename) !== false) {
			memory('set', $cachename, $data);
		}
	}

	public function update_syscache($cachename, $data) {
		$this->insert_syscache($cachename, $data);
	}

	public function delete_syscache($cachenames) {
		parent::delete($cachenames);
		if($this->_allowmem) {
			foreach((array)$cachenames as $cachename) {
				$this->_allowmem && memory('rm', $cachename);
			}
		}
	}
}

