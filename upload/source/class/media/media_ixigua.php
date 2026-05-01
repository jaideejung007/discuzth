<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class media_ixigua {

	public static $version = '1.0';
	public static $name = 'ixigua';
	public static $checkurl = ['ixigua.com/'];

	public static function parse($url, $width, $height) {
		if(preg_match('/^https?:\/\/(|m.|www.)ixigua.com\/(\d+)/i', $url, $matches)) {
			$iframe = 'https://www.ixigua.com/iframe/'.$matches[2].'?autoplay=0';
			$flv = $imgurl = '';
		}
		return [$flv, $iframe, $url, $imgurl];
	}

}