<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class media_youtube { /*discuzth*/

	public static $version = '1.0';
	public static $name = 'youtube';
	public static $checkurl = ['youtube.com', 'youtu.be'];

	public static function parse($url, $width, $height) {
		$flv = '';
		$iframe = '';
		$imgurl = '';
		
		if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:(?:v|e(?:mbed)?)/|.*[?&]v=|[^/]+/.+/)|youtu\.be/)([^"&?/ ]{11})%i', $url, $matches)) {
			$flv = 'https://www.youtube.com/v/'.$matches[1].'&fs=1';
			$iframe = 'https://www.youtube.com/embed/'.$matches[1];
			$imgurl = 'https://i.ytimg.com/vi/'.$matches[1].'/hqdefault.jpg';
		}
		return [$flv, $iframe, $url, $imgurl];
	}

}