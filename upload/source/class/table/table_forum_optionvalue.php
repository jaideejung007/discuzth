<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_optionvalue extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = '';
		$this->_pk = '';

		parent::__construct();
	}

	public function create($sortid, $fields, $dbcharset) {
		if(!$sortid || !$fields || !$dbcharset) {
			return;
		}
		$sortid = intval($sortid);
		$this->_table = 'forum_optionvalue'.$sortid;
		$query = DB::query("SHOW TABLES LIKE '%t'", [$this->_table]);
		if(DB::num_rows($query) != 1) {
			$engine = strtolower(getglobal('config/db/common/engine')) !== 'innodb' ? 'MyISAM' : 'InnoDB';
			$create_table_sql = 'CREATE TABLE '.DB::table($this->_table)." ($fields) ENGINE=".$engine.';';
			$db = DB::object();
			$create_table_sql = $this->syntablestruct($create_table_sql, true, $dbcharset);
			DB::query($create_table_sql);
		}
	}

	public function truncate($null = 0) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('UnsupportedOperationException');
		} else {
			return $this->truncate_by_sortid($null);
		}
	}

	public function truncate_by_sortid($sortid) {
		if(!$sortid) {
			return;
		}
		$sortid = intval($sortid);
		$this->_table = 'forum_optionvalue'.$sortid;
		DB::query('TRUNCATE %t', [$this->_table]);
	}

	public function showcolumns($sortid) {
		if(!$sortid) {
			return;
		}
		$sortid = intval($sortid);
		$this->_table = 'forum_optionvalue'.$sortid;
		$db = DB::object();
		$query = DB::query('SHOW FULL COLUMNS FROM %t', [$this->_table], true);
		$tables = [];
		while($field = @DB::fetch($query)) {
			$tables[$field['Field']] = 1;
		}
		return $tables;
	}

	public function alter($sortid, $sql) {
		if(!$sortid) {
			return;
		}
		$sortid = intval($sortid);
		$this->_table = 'forum_optionvalue'.$sortid;
		DB::query('ALTER TABLE %t %i', [$this->_table, $sql]);
	}

	public function drop($sortid) {
		if(!$sortid) {
			return;
		}
		$sortid = intval($sortid);
		$this->_table = 'forum_optionvalue'.$sortid;
		DB::query('DROP TABLE IF EXISTS %t', [$this->_table]);
	}

	public function syntablestruct($sql, $version, $dbcharset) {

		if(!str_contains(trim(substr($sql, 0, 18)), 'CREATE TABLE')) {
			return $sql;
		}

		$sqlversion = !(strpos($sql, 'ENGINE=') === FALSE);

		if($sqlversion === $version) {

			return $sqlversion && $dbcharset ? preg_replace(['/ character set \w+/i', '/ collate \w+/i', '/DEFAULT CHARSET=\w+/is'], ['', '', "DEFAULT CHARSET=$dbcharset"], $sql) : $sql;
		}

		if($version) {
			return preg_replace(['/TYPE=HEAP/i', '/TYPE=(\w+)/is'], ["ENGINE=MEMORY DEFAULT CHARSET=$dbcharset", "ENGINE=\\1 DEFAULT CHARSET=$dbcharset"], $sql);

		} else {
			return preg_replace(['/character set \w+/i', '/collate \w+/i', '/ENGINE=MEMORY/i', '/\s*DEFAULT CHARSET=\w+/is', '/\s*COLLATE=\w+/is', '/ENGINE=(\w+)(.*)/is'], ['', '', 'ENGINE=HEAP', '', '', 'TYPE=\\1\\2'], $sql);
		}
	}

	public function fetch_all_tid($sortid, $where) {
		if(!$sortid) {
			return;
		}
		$sortid = intval($sortid);
		$this->_table = 'forum_optionvalue'.$sortid;
		$query = DB::query('SELECT tid FROM %t %i', [$this->_table, $where], true);
		$return = [];
		while($thread = DB::fetch($query)) {
			$return[] = $thread['tid'];
		}
		return $return;
	}

	public function update($sortid, $tid, $fid = null, $fields = null) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('UnsupportedOperationException');
		} else {
			return $this->update_optionvalue($sortid, $tid, $fid, $fields);
		}
	}

	public function update_optionvalue($sortid, $tid, $fid, $fields) {
		if(!$sortid || !$fields) {
			return;
		}
		$sortid = intval($sortid);
		$this->_table = 'forum_optionvalue'.$sortid;
		DB::query('UPDATE %t SET %i WHERE tid=%d AND fid=%d', [$this->_table, $fields, $tid, $fid]);
	}

	public function insert($sortid, $fields = null, $replace = false, $null = null) {
		
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('UnsupportedOperationException');
		} else {
			return $this->insert_optionvalue($sortid, $fields, $replace);
		}
	}

	public function insert_optionvalue($sortid, $fields, $replace = false) {
		if(!$sortid || !$fields) {
			return;
		}
		$sortid = intval($sortid);
		$this->_table = 'forum_optionvalue'.$sortid;
		DB::query('%i INTO %t %i', [!$replace ? 'INSERT' : 'REPLACE', $this->_table, $fields]);
	}

}

