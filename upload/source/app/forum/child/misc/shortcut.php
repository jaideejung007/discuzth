<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_GET['type'] == 'ico') {
	$shortcut = @readfile(DISCUZ_ROOT.'favicon.ico');
	$filename = 'favicon.ico';
} else {
	$shortcut = '[InternetShortcut]
URL='.$_G['siteurl'].'
IconFile='.$_G['siteurl'].'favicon.ico
IconIndex=1
';
	$filename = $_G['setting']['bbname'].'.url';
}


$filenameencode = strtolower(CHARSET) == 'utf-8' ? rawurlencode($filename) : rawurlencode(diconv($filename, CHARSET, 'UTF-8'));


$rfc6266blacklist = strexists($_SERVER['HTTP_USER_AGENT'], 'UCBrowser') || strexists($_SERVER['HTTP_USER_AGENT'], 'Quark') || strexists($_SERVER['HTTP_USER_AGENT'], 'SogouM') || strexists($_SERVER['HTTP_USER_AGENT'], 'baidu');
dheader('Content-type: application/octet-stream');
dheader('Content-Disposition: attachment; filename="'.$filenameencode.'"'.(($filename == $filenameencode || $rfc6266blacklist) ? '' : '; filename*=utf-8\'\''.$filenameencode));
echo $shortcut;
exit;
	