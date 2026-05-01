<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class ultrax_cache {

	function __construct($conf) {
		$this->conf = $conf;
	}

	function get_cache($key) {
		static $data = [];
		if(!isset($data[$key])) {
			$cache = table_common_cache::t()->fetch($key);
			if(!$cache) {
				return false;
			}
			$data[$key] = dunserialize($cache['cachevalue']);
			if($cache['life'] && ($cache['dateline'] < time() - $data[$key]['life'])) {
				return false;
			}
		}
		return $data[$key]['data'];
	}

	function set_cache($key, $value, $life) {
		$data = [
			'cachekey' => $key,
			'cachevalue' => serialize(['data' => $value, 'life' => $life]),
			'dateline' => time(),
		];
		return table_common_cache::t()->insert($data);
	}

	function del_cache($key) {
		return table_common_cache::t()->delete($key);
	}
}