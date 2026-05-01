<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class ip_base_exception extends Exception {
}

class ip_base {

	public function convertSimple($ip) {
		static $ipdbLang = null;
		static $chinaProvinces = null;
		static $countries = null;

		$fullAddr = $this->convert($ip);

		if($fullAddr == '- Unknown' || $fullAddr == '- System Error') {
			return substr($fullAddr, 2);
		}

		$addr = substr($fullAddr, 2);

		if($ipdbLang === null) {
			$ipdbLang = lang('ipdb');
			$chinaProvinces = array_filter($ipdbLang, function($key) {
				return $key > 0;
			}, ARRAY_FILTER_USE_KEY);
		}

		$isChina = false;
		$province = '';

		foreach($chinaProvinces as $p) {
			if(str_contains($addr, $p)) {
				$isChina = true;
				$province = $p;
				break;
			}
		}

		if($isChina) {
			return $province;
		} else {
			if($countries == null) {
				$countries = array_filter($ipdbLang, function($key) {
					return $key < 0;
				}, ARRAY_FILTER_USE_KEY);
				$countries = implode('|', $countries);
			}
			if(preg_match('/^('.$countries.')/', $addr, $matches)) {
				return $matches[1];
			} else {
				$parts = explode(' ', $addr);
				return $parts[0];
			}
		}
	}
}

