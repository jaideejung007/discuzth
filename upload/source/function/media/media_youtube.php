<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$checkurl = array('youtube.com','youtu.be'); /*jaideejung007*/

function media_youtube($url, $width, $height) { 
/*jaideejung007*/	if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:(?:v|e(?:mbed)?)/|.*[?&]v=|[^/]+/.+/)|youtu\.be/)([^"&?/ ]{11})%i', $url, $matches)) { 
		$flv = 'https://www.youtube.com/v/'.$matches[1].'&fs=1';
		$iframe = 'https://www.youtube.com/embed/'.$matches[1];
		if(!$width && !$height) {
			$str = dfsockopen($url);
			if(!empty($str) && preg_match("/'VIDEO_HQ_THUMB':\s'(.+?)'/i", $str, $image)) {
				$url = substr($image[1], 0, strrpos($image[1], '/')+1);
				$filename = substr($image[1], strrpos($image[1], '/')+3);
				$imgurl = $url.$filename;
			}
		}
	}
	return array($flv, $iframe, $url, $imgurl);
}