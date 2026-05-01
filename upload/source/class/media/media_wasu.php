<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class media_wasu {

	public static $version = '1.0';
	public static $name = 'wasu';
	public static $checkurl = ['wasu.cn'];

	public static function parse($url, $width, $height) {
		if(preg_match('/https?:\/\/(www.|)wasu.cn\/(wap\/|)Play\/show\/id\/(\d+)/i', $url, $matches)) {
			$vid = $matches[3];
			$flv = '';
			$iframe = 'https://www.wasu.cn/Play/iframe/id/'.$vid;
			$imgurl = '';
		}
		return [$flv, $iframe, $url, $imgurl];
	}

}