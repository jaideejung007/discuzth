<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class media_acfun {

	public static $version = '1.0';
	public static $name = 'acfun';
	public static $checkurl = ['acfun.cn', 'acfun.tv'];

	public static function parse($url, $width, $height) {
		if(preg_match('/https?:\/\/(www.|)acfun.(cn|tv)\/v\/ac(\d+)/i', $url, $matches)) {
			$vid = $matches[3];
			$flv = '';
			$iframe = 'https://www.acfun.cn/player/ac'.$vid;
			$imgurl = '';
		} elseif(preg_match('/https?:\/\/m.acfun.(cn|tv)\/v\/\?ac=(\d+)/i', $url, $matches)) {
			$vid = $matches[2];
			$flv = '';
			$iframe = 'https://www.acfun.cn/player/ac'.$vid;
			$imgurl = '';
		}
		return [$flv, $iframe, $url, $imgurl];
	}

}

