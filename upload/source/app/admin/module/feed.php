<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

$operation = $operation ? $operation : 'search';

shownav('topic', 'nav_feed');
$anchor = in_array($operation, ['search', 'global']) ? $operation : 'search';
$current = [$anchor => 1];
if(empty($_GET['feedid'])) {
	showsubmenu('nav_feed', [
		['nav_feed', 'feed', $current['search']],
		['feed_global', 'feed&operation=global', $current['global']],
	]);
}

$file = childfile('feed/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;