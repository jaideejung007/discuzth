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
$operation = in_array($operation, ['add', 'edit', 'update', 'delete']) ? $operation : 'list';
$signtypearr = [['', cplang('blockxml_signtype_no')], ['MD5', cplang('blockxml_signtype_md5')]];
shownav('portal', 'blockxml');

$file = childfile('blockxml/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}
require_once $file;

