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
$operation = in_array($operation, ['edit', 'perm']) ? $operation : 'list';

shownav('portal', 'diytemplate');

$file = childfile('diytemplate/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;

