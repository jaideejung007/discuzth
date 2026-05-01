<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

@set_time_limit(0);
@ignore_user_abort(TRUE);

@set_time_limit(0);
@ignore_user_abort(true);
ini_set('max_execution_time', 0);
ini_set('mysql.connect_timeout', 0);

header_remove();
ob_end_clean();
ob_implicit_flush();
header('X-Accel-Buffering: no');
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');
ob_start();

if(!defined('IN_DISCUZ')) {
	sse_output('Access Denied');
}

if(file_exists(DISCUZ_DATA.'./install.lock') && file_exists(DISCUZ_DATA.'./update.lock')) {
	sse_output('Access Denied');
}

@touch(DISCUZ_DATA.'./install.lock');
@touch(DISCUZ_DATA.'./update.lock');

$force = !empty($_GET['force']) && authcode($_GET['force'], 'DECODE', $_G['config']['security']['authkey']) - time() < 10;
if(!($_G['adminid'] == 1 && $_GET['formhash'] == formhash()) && $_G['setting'] && !$force) {
	exit('Access Denied');
}

require_once libfile('function/cache');
updatecache();

require_once libfile('function/block');
blockclass_cache();

if($_G['config']['output']['tplrefresh']) {
	cleartemplatecache();
}

C::memory()->clear();

sse_output('Done');

function sse_output($message, $close = false) {
	ob_end_clean();
	echo "data:{$message}\n\n";
	ob_flush();
	flush();
	exit;
}