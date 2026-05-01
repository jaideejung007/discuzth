<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class media_youtube {

	public static $version = '1.0';
	public static $name = 'youtube';
	public static $checkurl = ['youtube.com/watch?'];

	public static function parse($url, $width, $height) {
		if(preg_match('/^https?:\/\/(|m.|www.)youtube.com\/watch\?v=([^\/&]+)&?/i', $url, $matches)) {
			$flv = 'https://www.youtube.com/v/'.$matches[2].'&fs=1';
			$iframe = 'https://www.youtube.com/embed/'.$matches[2];
			if(!$width && !$height) {
				$str = dfsockopen($url);
				if(!empty($str) && preg_match("/'VIDEO_HQ_THUMB':\s'(.+?)'/i", $str, $image)) {
					$url = substr($image[1], 0, strrpos($image[1], '/') + 1);
					$filename = substr($image[1], strrpos($image[1], '/') + 3);
					$imgurl = $url.$filename;
				}
			}
		}
		return [$flv, $iframe, $url, $imgurl];
	}

}