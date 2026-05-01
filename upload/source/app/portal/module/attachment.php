<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$operation = $_GET['op'] ? $_GET['op'] : '';

$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
$aid = empty($_GET['aid']) ? '' : intval($_GET['aid']);
$attach = table_portal_attachment::t()->fetch($id);
if(empty($attach)) {
	showmessage('portal_attachment_noexist');
}

if($operation == 'delete') {
	require_once childfile('delete');
} elseif($operation == 'getattach') {
	require_once childfile('getattach');
} else {
	require_once childfile('output');
}

function getremotefile($file) {
	global $_G;
	@set_time_limit(0);
	if(!@readfile($_G['setting']['ftp']['attachurl'].'portal/'.$file)) {

		$ftp = new discuz_ftp();
		if(!($_G['setting']['ftp']['connid'] = $ftp->connect())) {
			return FALSE;
		}
		$tmpfile = @tempnam($_G['setting']['attachdir'], '');
		if($ftp->ftp_get($_G['setting']['ftp']['connid'], $tmpfile, $file, FTP_BINARY)) {
			@readfile($tmpfile);
			@unlink($tmpfile);
		} else {
			@unlink($tmpfile);
			return FALSE;
		}
	}
	return TRUE;
}

function getlocalfile($filename, $readmod = 2, $range = 0) {
	if($readmod == 1 || $readmod == 3 || $readmod == 4) {
		if($fp = @fopen($filename, 'rb')) {
			@fseek($fp, $range);
			if(function_exists('fpassthru') && ($readmod == 3 || $readmod == 4)) {
				@fpassthru($fp);
			} else if(filesize($filename)) {
				echo fread($fp, filesize($filename));
			}
		}
		@fclose($fp);
	} else {
		@readfile($filename);
	}
	@flush();
	@ob_flush();
}

