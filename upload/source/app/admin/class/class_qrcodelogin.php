<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace admin;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class class_qrcodelogin {

	const BASEURL = 'https://api.witframe.com/discuzlogin';

	public static function param($param) {
		global $_G;

		ksort($param);
		$t = time();
		$param['authsign'] = sha1($t.'|'.http_build_query($param).'|'.md5($_G['config']['security']['authkey']));
		$param['t'] = $t;
		return http_build_query($param);
	}

	public static function login($qrcodeReturnCode) {
		$v = dfsockopen(self::BASEURL.'/validator?'.self::param(['code' => $qrcodeReturnCode]));
		if(empty($v)) {
			return [];
		}
		return json_decode($v, true);
	}

	public static function notify($param) {
		dfsockopen(self::BASEURL.'/notify?'.self::param($param));
	}

	public static function admin($action, $param) {
		$v = dfsockopen(self::BASEURL.'admin/'.$action.'?'.self::param($param));
		if(empty($v)) {
			return [];
		}
		return json_decode($v, true);
	}

}