<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$idhash = isset($_GET['idhash']) && preg_match('/^\w+$/', $_GET['idhash']) ? $_GET['idhash'] : '';
$modid = isset($_GET['modid']) && preg_match('/^[\w:]+$/', $_GET['modid']) ? $_GET['modid'] : '';

if(getgpc('action') == 'update' && !defined('IN_MOBILE')) {
	require_once childfile('update');
} elseif(getgpc('action') == 'update' && defined('IN_MOBILE') && constant('IN_MOBILE') == 2) {
	require_once childfile('update_mobile');
} elseif(getgpc('action') == 'check') {
	require_once childfile('check');
} else {
	require_once childfile('output');
}

