<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_G['adminid'] != 1) {
	showmessage('no_privilege_indexheats');
}
table_forum_thread::t()->update($_G['tid'], ['heats' => 0]);
require_once libfile('function/cache');
updatecache('heats');
dheader('Location: '.dreferer());
	