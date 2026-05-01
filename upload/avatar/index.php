<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

const DISABLEDEFENSE = true;
const APPTYPEID = 199;
require_once '../source/class/class_core.php';
$discuz = C::app();
$discuz->init_db = $discuz->init_user = $discuz->init_session = $discuz->init_cron = $discuz->init_misc = $discuz->init_mobile = false;
$discuz->init();

header('Cache-Control: max-age=86400');

$url = avatar($_GET['uid'] ?? 0, in_array($size, ['big', 'middle', 'small']) ? $size : 'middle', true);
if(!filter_var($url, FILTER_VALIDATE_URL)) {
	$url = '../'.$url;
} else {
	$c = new filesock_curl();
	$c->unsafe = true;
	$c->returnbody = false;
	$c->conntimeout = 2;
	$c->timeout = 2;
	$c->header = [
		'referer' => $_G['siteurl']
	];
	$c->request(['url' => $url]);
	if(!in_array($c->curlstatus['http_code'], [200, 301, 302])) {
		$url = '../data/avatar/noavatar.svg';
	}
}

header('Location: '.$url);