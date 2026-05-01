<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$plugin = table_common_plugin::t()->fetch($pluginid);
if(!$plugin) {
	cpmsg('plugin_not_found', '', 'error');
}
$code = moduleample($_GET['typeid'], $_GET['module'], $plugin);
if(!$code) {
	cpmsg('NO_OPERATION');
}
dheader('Content-Disposition: attachment; filename='.$_GET['module'].$_GET['fn']);
dheader('Content-Type: application/octet-stream');
ob_end_clean();
echo $code;
const FOOTERDISABLED = 1;
exit();
	