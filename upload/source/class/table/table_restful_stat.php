<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_restful_stat extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {
		$this->_table = 'restful_stat';
		$this->_pk = '';

		parent::__construct();
	}

	public function updatestat($appid, $uri, $num = 1) {
		$nowdaytime = dgmdate(TIMESTAMP, 'Ymd');
		$num = abs(intval($num));
		if(DB::result_first('SELECT COUNT(*) FROM %t WHERE `appid` = %d AND `uri` = %s AND daytime = %s',
			[$this->_table, $appid, $uri, $nowdaytime])) {
			DB::query('UPDATE %t SET %i WHERE `appid` = %d AND `uri` = %s AND daytime = %s',
				[$this->_table, "`request`=`request`+$num", $appid, $uri, $nowdaytime], false, true);
		} else {
			DB::insert($this->_table, [
				'appid' => $appid,
				'uri' => $uri,
				'daytime' => $nowdaytime,
				'request' => $num
			]);
		}
	}

	public function fetch_all_stat($appid, $uri, $begin, $end) {
		$data = [];
		$wheresql = '`appid` = %d ';
		$parameter = [$this->_table, $appid];
		if($uri) {
			$wheresql .= 'AND `uri` = %s ';
			$parameter[] = $uri;
		}
		$wheresql .= 'AND daytime >= %d AND daytime <= %d ORDER BY daytime ASC';
		$parameter[] = $begin;
		$parameter[] = $end;
		$query = DB::query('SELECT * FROM %t WHERE '.$wheresql, $parameter);
		while($value = DB::fetch($query)) {
			$data[$value['uri']][$value['daytime']] = $value['request'];
		}
		return $data;
	}

	public function clearstat($daytime) {
		DB::delete($this->_table, DB::field('daytime', $daytime, '<'));
	}
}

