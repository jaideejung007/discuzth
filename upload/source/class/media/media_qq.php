<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class media_qq {

	public static $version = '1.0';
	public static $name = 'qq';
	public static $checkurl = ['v.qq.com/x/page/', 'v.qq.com/x/cover/'];

	public static function parse($url, $width, $height) {
		if(preg_match('/https?:\/\/v.qq.com\/x\/page\/([^\/]+)(.html|)/i', $url, $matches)) {
			$vid = explode('.html', $matches[1]);
			$flv = 'https://imgcache.qq.com/tencentvideo_v1/playerv3/TPout.swf?vid='.$vid[0];
			$iframe = 'https://v.qq.com/txp/iframe/player.html?vid='.$vid[0];
			$imgurl = '';
		} else if(preg_match('/https?:\/\/v.qq.com\/x\/cover\/([^\/]+)\/([^\/]+)(.html|)/i', $url, $matches)) {
			$vid = explode('.html', $matches[2]);
			$flv = 'https://imgcache.qq.com/tencentvideo_v1/playerv3/TPout.swf?vid='.$vid[0];
			$iframe = 'https://v.qq.com/txp/iframe/player.html?vid='.$vid[0];
			$imgurl = '';
		}
		return [$flv, $iframe, $url, $imgurl];
	}

}