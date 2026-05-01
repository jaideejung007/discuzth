<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_portal_topic_pic extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'portal_topic_pic';
		$this->_pk = 'picid';

		parent::__construct();
	}

	public function count_by_topicid($topicid) {
		return $topicid ? DB::result_first('SELECT COUNT(*) FROM %t WHERE topicid=%d', [$this->_table, $topicid]) : 0;
	}

	public function fetch_all_by_topicid($topicid, $start = 0, $limit = 0) {
		return $topicid ? DB::fetch_all('SELECT * FROM %t WHERE topicid=%d ORDER BY picid DESC'.DB::limit($start, $limit), [$this->_table, $topicid]) : [];
	}

}

