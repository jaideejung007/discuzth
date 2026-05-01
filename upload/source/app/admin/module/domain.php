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
$operation = in_array($operation, ['global', 'app', 'root']) ? $operation : 'global';
$current = [$operation => 1];

shownav('global', 'setting_domain');
showsubmenu('setting_domain', [
	['setting_domain_base', 'domain', $current['global']],
	['setting_domain_app', 'domain&operation=app', $current['app']],
	['setting_domain_root', 'domain&operation=root', $current['root']],
]);
$navs = $_G['setting']['navs'];

$file = childfile('domain/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;