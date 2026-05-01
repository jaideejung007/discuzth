<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') && !defined('IN_API')) {
	exit('Access Denied');
}
const DISCUZ_LOG_FUNCTION = true;

function getClientIp() {
	global $_G;
	if(isset($_G['clientip']) and !empty($_G['clientip'])) {
		return $_G['clientip'];
	}
	if(isset($_SERVER['HTTP_CLIENT_IP']) and !empty($_SERVER['HTTP_CLIENT_IP'])) {
		return $_SERVER['HTTP_CLIENT_IP'];
	}
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		return strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
	}
	if(isset($_SERVER['HTTP_PROXY_USER']) and !empty($_SERVER['HTTP_PROXY_USER'])) {
		return $_SERVER['HTTP_PROXY_USER'];
	}
	if(isset($_SERVER['REMOTE_ADDR']) and !empty($_SERVER['REMOTE_ADDR'])) {
		return $_SERVER['REMOTE_ADDR'];
	} else {
		return '0.0.0.0';
	}
}


function getBrowser() {
	$browser = $_SERVER['HTTP_USER_AGENT'];
	if(str_contains($browser, '360SE')) {
		$browser = '360se';
	} elseif(str_contains($browser, 'Maxthon')) {
		$browser = 'Maxthon';
	} elseif(str_contains($browser, 'Tencent')) {
		$browser = 'Tencent Browser';
	} elseif(str_contains($browser, 'QQBrowser')) {
		$browser = 'QQ Browser';
	} elseif(str_contains($browser, 'Green')) {
		$browser = 'Green Browser';
	} elseif(str_contains($browser, 'baidu')) {
		$browser = 'Baidu Browser';
	} elseif(str_contains($browser, 'TheWorld')) {
		$browser = 'The World';
	} elseif(str_contains($browser, 'MetaSr')) {
		$browser = 'Sogou Browser';
	} elseif(str_contains($browser, 'Firefox')) {
		$browser = 'Firefox';
	} elseif(preg_match('/MSIE\s6\.0/', $browser)) {
		$browser = 'IE6.0';
	} elseif(preg_match('/MSIE\s7\.0/', $browser)) {
		$browser = 'IE7.0';
	} elseif(preg_match('/MSIE\s8\.0/', $browser)) {
		$browser = 'IE8.0';
	} elseif(preg_match('/MSIE\s9\.0/', $browser)) {
		$browser = 'IE9.0';
	} elseif(str_contains($browser, 'Netscape')) {
		$browser = 'Netscape';
	} elseif(str_contains($browser, 'Opera')) {
		$browser = 'Opera';
	} elseif(str_contains($browser, 'Chrome')) {
		$browser = 'Chrome';
	} elseif(str_contains($browser, 'Gecko')) {
		$browser = 'Gecko';
	} elseif(str_contains($browser, 'Safari')) {
		$browser = 'Safari';
	} else {
		$browser = 'Unknow Browser';
	}
	return $browser;
}


function getOs() {
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$os = false;
	if(preg_match('/win/i', $agent) && strpos($agent, '95')) {
		$os = 'Windows 95';
	} else if(preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
		$os = 'Windows ME';
	} else if(preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
		$os = 'Windows 98';
	} else if(preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
		$os = 'Windows Vista';
	} else if(preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
		$os = 'Windows 7';
	} else if(preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
		$os = 'Windows 8';
	} else if(preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
		$os = 'Windows 10';
	} else if(preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
		$os = 'Windows XP';
	} else if(preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
		$os = 'Windows 2000';
	} else if(preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
		$os = 'Windows NT';
	} else if(preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
		$os = 'Windows 32';
	} else if(preg_match('/linux/i', $agent)) {
		$os = 'Linux';
	} else if(preg_match('/unix/i', $agent)) {
		$os = 'Unix';
	} else if(preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
		$os = 'SunOS';
	} else if(preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
		$os = 'IBM OS/2';
	} else if(preg_match('/Mac/i', $agent)) {
		$os = 'Mac OS';
	} else if(preg_match('/PowerPC/i', $agent)) {
		$os = 'PowerPC';
	} else if(preg_match('/AIX/i', $agent)) {
		$os = 'AIX';
	} else if(preg_match('/HPUX/i', $agent)) {
		$os = 'HPUX';
	} else if(preg_match('/NetBSD/i', $agent)) {
		$os = 'NetBSD';
	} else if(preg_match('/BSD/i', $agent)) {
		$os = 'BSD';
	} else if(preg_match('/OSF1/i', $agent)) {
		$os = 'OSF1';
	} else if(preg_match('/IRIX/i', $agent)) {
		$os = 'IRIX';
	} else if(preg_match('/FreeBSD/i', $agent)) {
		$os = 'FreeBSD';
	} else if(preg_match('/teleport/i', $agent)) {
		$os = 'teleport';
	} else if(preg_match('/flashget/i', $agent)) {
		$os = 'flashget';
	} else if(preg_match('/webzip/i', $agent)) {
		$os = 'webzip';
	} else if(preg_match('/offline/i', $agent)) {
		$os = 'offline';
	} else {
		$os = 'Unknow OS';
		
	}
	return $os;
}


function getDevice() {
	$device = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($device, 'Windows NT')) {
		$device = 'PC';
	} elseif(strpos($device, 'iPhone')) {
		$device = 'iPhone';
	} elseif(strpos($device, 'Android')) {
		$device = 'Android Mobile';
	} elseif(strpos($device, 'iPad')) {
		$device = 'iPad';
	} elseif(strpos($device, 'iPod')) {
		$device = 'iPod';
	} elseif(strpos($device, 'Mac OS X')) {
		$device = 'Mac';
	} elseif(strpos($device, 'Windows Phone OS')) {
		$device = 'Windows Phone Mobile';
	} else {
		$device = 'Unknow Device';
	}
	return $device;
}


function getUseragent() {
	return $_SERVER['HTTP_USER_AGENT'];
}


function getPort() {
	global $_G;
	return !empty($_G['remoteport']) ? $_G['remoteport'] : ($_SERVER['REMOTE_PORT'] ? $_SERVER['REMOTE_PORT'] : 0);
}


function getLogInfo() {
	$loginfo = [];

	$loginfo['client_ip'] = getClientIp();
	$loginfo['client_port'] = getPort();
	$loginfo['client_browser'] = getBrowser();
	$loginfo['client_os'] = getOs();
	$loginfo['client_device'] = getDevice();
	$loginfo['client_useragent'] = getUseragent();

	return $loginfo;
}

