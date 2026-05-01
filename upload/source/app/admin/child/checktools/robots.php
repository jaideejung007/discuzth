<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($do == 'output') {
	$robots = implode('', file(DISCUZ_ROOT.'./source/data/admincp/robots.txt'));
	$robots = str_replace('{path}', $_G['siteroot'], $robots);
	$robots = str_replace('{ver}', $_G['setting']['version'], $robots);
	ob_end_clean();
	dheader('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	dheader('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	dheader('Cache-Control: no-cache, must-revalidate');
	dheader('Pragma: no-cache');
	dheader('Content-Encoding: none');
	dheader('Content-Length: '.strlen($robots));
	dheader('Content-Disposition: attachment; filename=robots.txt');
	dheader('Content-Type: text/plain');
	echo $robots;
	define('FOOTERDISABLED', 1);
	exit();
}
cpmsg('robots_output', 'action=checktools&operation=robots&do=output&frame=no', 'download', ['siteurl' => dhtmlspecialchars($_G['scheme'].'://'.$_SERVER['HTTP_HOST'].'/')]);
	