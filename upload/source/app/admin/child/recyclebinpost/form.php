<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$lpp = empty($_GET['lpp']) ? 20 : $_GET['lpp'];
$start = ($page - 1) * $lpp;
$start_limit = ($page - 1) * $lpp;
$multi = '';

$operation = $operation ? $operation : 'list';
$file = childfile('recyclebinpost/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;
