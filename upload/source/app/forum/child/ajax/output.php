<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$isimage = $attach['isimage'];
$_G['setting']['ftp']['hideurl'] = $_G['setting']['ftp']['hideurl'] || ($isimage && !empty($_GET['noupdate']) && $_G['setting']['attachimgpost'] && strtolower(substr($_G['setting']['ftp']['attachurl'], 0, 3)) == 'ftp');


if(empty($_GET['nothumb']) && $attach['isimage'] && $attach['thumb']) {
	$db = DB::object();
	$db->close();
	!$_G['config']['output']['gzip'] && ob_end_clean();
	dheader('Content-Disposition: inline; filename='.getimgthumbname($attach['filename']));
	dheader('Content-Type: image/pjpeg');
	if($attach['remote']) {
		$_G['setting']['ftp']['hideurl'] ? getremotefile(getimgthumbname($attach['attachment'])) : dheader('location:'.$_G['setting']['ftp']['attachurl'].'forum/'.getimgthumbname($attach['attachment']));
	} else {
		getlocalfile($_G['setting']['attachdir'].'/forum/'.getimgthumbname($attach['attachment']));
	}
	exit();
}

$filename = $_G['setting']['attachdir'].'/forum/'.$attach['attachment'];
if(!$attach['remote'] && !is_readable($filename)) {
	if(!$requestmode) {
		showmessage('attachment_nonexistence');
	} else {
		exit;
	}
}

if(!$requestmode) {
	
	$exemptvalue = $ismoderator ? 32 : 4;
	if(!$isimage && !($_G['group']['exempt'] & $exemptvalue)) {
		$creditlog = updatecreditbyaction('getattach', $_G['uid'], [], '', 1, 0, $thread['fid']);
		if($creditlog['updatecredit']) {
			if($_G['uid']) {
				$k = $_GET['ck'];
				$t = $_GET['t'];
				if(empty($k) || empty($t) || $k != substr(md5($aid.$t.md5($_G['config']['security']['authkey'])), 0, 8) || TIMESTAMP - $t > 3600) {
					dheader('location: forum.php?mod=misc&action=attachcredit&aid='.$attach['aid'].'&formhash='.FORMHASH);
					exit();
				}
			} else {
				showmessage('attachment_forum_nopermission', NULL, [], ['login' => 1]);
			}
		}
	}
}




$range_start = 0;
$range_end = 0;
$has_range_header = false;
if(($readmod == 4 || $readmod == 1) && !empty($_SERVER['HTTP_RANGE'])) {
	$has_range_header = true;
	list($range_start, $range_end) = explode('-', (str_replace('bytes=', '', $_SERVER['HTTP_RANGE'])));
}


if(!$requestmode && !$has_range_header && empty($_GET['noupdate'])) {
	if($_G['setting']['delayviewcount']) {
		$_G['forum_logfile'] = DISCUZ_DATA.'./cache/forum_attachviews_'.intval(getglobal('config/server/id')).'.log';
		if(str_ends_with(TIMESTAMP, '0')) {
			attachment_updateviews($_G['forum_logfile']);
		}

		if(file_put_contents($_G['forum_logfile'], "$aid\n", FILE_APPEND) === false) {
			if($_G['adminid'] == 1) {
				showmessage('view_log_invalid', '', ['logfile' => 'cache/'.basename($_G['forum_logfile'])]);
			}
			table_forum_attachment::t()->update_download($aid);
		}
	} else {
		table_forum_attachment::t()->update_download($aid);
	}
}


$db = DB::object();
$db->close();
!$_G['config']['output']['gzip'] && ob_end_clean();

$oss = null;
$oss_config = getglobal('setting/oss');
if($_G['setting']['ftp']['on'] == 2) {
	$oss_config['oss_key'] = authcode($oss_config['oss_key'], 'DECODE', md5(getglobal('config/security/authkey')));
	$oss = oss::loadOSS($oss_config);
}

if($attach['remote'] && !$_G['setting']['ftp']['hideurl'] && $isimage) {
	dheader('location:'.$_G['setting']['ftp']['attachurl'].'forum/'.$attach['attachment']);
	exit();
}


if($attach['remote'] && !empty($oss)) {
	$object = $oss_config['oss_rootpath'].'forum/'.$attach['attachment'];
	$attach['filename'] = str_replace(',', '', $attach['filename']);
	dheader('location:'.$oss->signUrl($object, $attach['filename'], 3600));
	exit();
}


$mimetype = ext_to_mimetype($attach['filename']);
$filesize = !$attach['remote'] ? filesize($filename) : $attach['filesize'];

if($has_range_header && !$range_end) $range_end = $filesize - 1;

$filenameencode = strtolower(CHARSET) == 'utf-8' ? rawurlencode($attach['filename']) : rawurlencode(diconv($attach['filename'], CHARSET, 'UTF-8'));



$rfc6266blacklist = strexists($_SERVER['HTTP_USER_AGENT'], 'UCBrowser') || strexists($_SERVER['HTTP_USER_AGENT'], 'Quark') || strexists($_SERVER['HTTP_USER_AGENT'], 'SogouM') || strexists($_SERVER['HTTP_USER_AGENT'], 'baidu');

dheader('Date: '.gmdate('D, d M Y H:i:s', $attach['dateline']).' GMT');
dheader('Last-Modified: '.gmdate('D, d M Y H:i:s', $attach['dateline']).' GMT');
dheader('Content-Encoding: none');

if($isimage && !empty($_GET['noupdate']) || !empty($_GET['request'])) {
	$cdtype = 'inline';
} else {
	$cdtype = 'attachment';
}
dheader('Content-Disposition: '.$cdtype.'; '.'filename="'.$filenameencode.'"'.(($attach['filename'] == $filenameencode || $rfc6266blacklist) ? '' : '; filename*=utf-8\'\''.$filenameencode));

if($isimage) {
	dheader('Content-Type: image');
} else {
	dheader('Content-Type: '.$mimetype);
}

dheader('Content-Length: '.$filesize);

if(!$attach['remote']) {
	$xsendfile = getglobal('config/download/xsendfile');
	if(!empty($xsendfile)) {
		$type = intval($xsendfile['type']);
		if($isimage) {
			$type = 0;
		}
		$cmd = '';
		switch($type) {
			case 1:
				$cmd = 'X-Accel-Redirect';
				$url = $xsendfile['dir'].$attach['attachment'];
				break;
			case 2:
				$cmd = $_SERVER['SERVER_SOFTWARE'] < 'lighttpd/1.5' ? 'X-LIGHTTPD-send-file' : 'X-Sendfile';
				$url = $filename;
				break;
			case 3:
				$cmd = 'X-Sendfile';
				$url = $filename;
				break;
		}
		if($cmd) {
			dheader("$cmd: $url");
			exit();
		}
	}

	
	if(($readmod == 4) || ($readmod == 1)) {
		dheader('Accept-Ranges: bytes');
		if($has_range_header) {
			$rangesize = ($range_end - $range_start) >= 0 ? ($range_end - $range_start) + 1 : 0;
			dheader('Content-Length: '.$rangesize);
			dheader('HTTP/1.1 206 Partial Content');
			dheader('Content-Range: bytes '.$range_start.'-'.$range_end.'/'.($filesize));
		}
	}
}

$attach['remote'] ? getremotefile($attach['attachment']) : getlocalfile($filename, $readmod, $range_start, $range_end);
