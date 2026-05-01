<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function _dfsockopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE, $encodetype = 'URLENCODE', $allowcurl = TRUE, $position = 0, $files = []) {
	$param = [
		'url' => $url,
		'limit' => $limit,
		'post' => $post,
		'cookie' => $cookie,
		'ip' => $ip,
		'block' => $block,
		'encodetype' => $encodetype,
		'allowcurl' => $allowcurl,
		'position' => $position,
		'files' => $files,
		'timeout' => $timeout,
	];
	$fs = filesock::open($param);
	return $fs->request();
}

