<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

class filesock {
	public static function open($param = []) {
		$allowcurl = true;
		if(isset($param['allowcurl']) && !$param['allowcurl']) {
			$allowcurl = false;
		}
		if(function_exists('curl_init') && function_exists('curl_exec') && $allowcurl) {
			return new filesock_curl($param);
		} else {
			return new filesock_stream($param);
		}
	}
}
