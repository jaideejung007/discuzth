<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(empty($_G['setting']['modworkstatus'])) {
	showmessage('undefined_action');
}

$statvars = getstatvars('modworks');
extract($statvars);
if($_GET['exportexcel']) {
	$filename = 'stat_modworks_'.($username ? $username.'_' : '').$starttime.'_'.$endtime.'.csv';
	
	$filenameencode = strtolower(CHARSET) == 'utf-8' ? rawurlencode($filename) : rawurlencode(diconv($filename, CHARSET, 'UTF-8'));
	
	
	$rfc6266blacklist = strexists($_SERVER['HTTP_USER_AGENT'], 'UCBrowser') || strexists($_SERVER['HTTP_USER_AGENT'], 'Quark') || strexists($_SERVER['HTTP_USER_AGENT'], 'SogouM') || strexists($_SERVER['HTTP_USER_AGENT'], 'baidu');
	include template('forum/stat_misc_export');
	$csvstr = ob_get_contents();
	ob_end_clean();
	header('Content-Encoding: none');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.$filenameencode.'"'.(($filename == $filenameencode || $rfc6266blacklist) ? '' : '; filename*=utf-8\'\''.$filenameencode));
	header('Pragma: no-cache');
	header('Expires: 0');
	if($_G['charset'] != 'gbk') {
		$csvstr = diconv($csvstr, $_G['charset'], 'GBK');
	}
	echo $csvstr;
	exit;
} else {
	include template('forum/stat_misc');
}