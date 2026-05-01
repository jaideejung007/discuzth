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
if(!isfounder()) cpmsg('noaccess_isfounder', '', 'error');

$isplugindeveloper = isset($_G['config']['plugindeveloper']) && $_G['config']['plugindeveloper'] > 0;
if(!$isplugindeveloper) {
	cpmsg('undefined_action', '', 'error');
}

$operation = empty($operation) ? 'admin' : $operation;

$file = childfile('templates/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;