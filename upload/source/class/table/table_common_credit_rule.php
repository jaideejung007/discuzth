<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_credit_rule extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_credit_rule';
		$this->_pk = 'rid';

		parent::__construct();
	}

	public function fetch_all_by_rid($rid = 0) {
		$parameter = [$this->_table];
		$wherearr = [];
		if($rid) {
			$rid = dintval($rid, true);
			$parameter[] = $rid;
			$wherearr[] = is_array($rid) ? 'rid IN(%n)' : 'rid=%d';
		}
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';
		return DB::fetch_all("SELECT * FROM %t $wheresql ORDER BY rid ASC", $parameter, $this->_pk);
	}

	public function fetch_all_rule() {
		return DB::fetch_all('SELECT * FROM %t ORDER BY rid ASC', [$this->_table]);
	}

	public function fetch_all_by_action($action) {
		if(!empty($action)) {
			$rules = [];
			foreach($this->fetch_all_rule() as $value) {
				if(in_array($value['action'], $action)) {
					$rules[$value['rid']] = $value;
					continue;
				}
				list($mainAction, $sub) = explode('/', $value['action']);
				if($sub && in_array($mainAction, $action)) {
					$rules[$value['rid']] = $value;
				}
			}
			return $rules;
		}
		return [];
	}

}

