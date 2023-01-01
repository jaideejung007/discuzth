<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$checkurl = array('youtube.com','youtu.be'); /*jaideejung007*/

function media_youtube($url, $width, $height) { /*jaideejung007*/
	if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:(?:v|e(?:mbed)?)/|.*[?&]v=|[^/]+/.+/)|youtu\.be/)([^"&?/ ]{11})%i', $url, $matches)) { 
		$flv = 'https://www.youtube.com/v/'.$matches[1].'&fs=1';
		$iframe = 'https://www.youtube.com/embed/'.$matches[1];
		$imgurl = 'https://i.ytimg.com/vi/'.$matches[1].'/maxresdefault.jpg';
	}
	return array($flv, $iframe, $url, $imgurl);
}