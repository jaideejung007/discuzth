<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
const NOROBOT = TRUE;
@list($_GET['aid'], $_GET['k'], $_GET['t'], $_GET['uid'], $_GET['tableid']) = daddslashes(explode('|', base64_decode($_GET['aid'])));

$requestmode = !empty($_GET['request']) && empty($_GET['uid']);
$aid = intval($_GET['aid']);
$k = $_GET['k'];
$t = $_GET['t'];
$authk = !$requestmode ? substr(md5($aid.md5($_G['config']['security']['authkey']).$t.$_GET['uid']), 0, 8) : md5($aid.md5($_G['config']['security']['authkey']).$t);
$sameuser = !empty($_GET['uid']) && $_GET['uid'] == $_G['uid'];

if($k !== $authk || $t > TIMESTAMP + 3600) {
	if(!$requestmode) {
		showmessage('attachment_nonexistence');
	} else {
		exit;
	}
}

if(!empty($_GET['findpost']) && ($attach = table_forum_attachment::t()->fetch($aid))) {
	dheader('location: forum.php?mod=redirect&goto=findpost&pid='.$attach['pid'].'&ptid='.$attach['tid']);
}

if($_GET['uid'] != $_G['uid'] && $_GET['uid']) {
	$_G['uid'] = $_GET['uid'] = intval($_GET['uid']);
	$member = getuserbyuid($_GET['uid']);
	loadcache('usergroup_'.$member['groupid']);
	$_G['group'] = $_G['cache']['usergroup_'.$member['groupid']];
	$_G['group']['grouptitle'] = $_G['cache']['usergroup_'.$_G['groupid']]['grouptitle'];
	$_G['group']['color'] = $_G['cache']['usergroup_'.$_G['groupid']]['color'];
}


$tableid = 'aid:'.$aid;

if($_G['setting']['attachexpire']) {

	if(TIMESTAMP - $t > $_G['setting']['attachexpire'] * 3600) {
		$aid = intval($aid);
		if($attach = table_forum_attachment_n::t()->fetch_attachment($tableid, $aid)) {
			if($attach['isimage']) {
				dheader('location: '.$_G['siteurl'].'static/image/common/none.gif');
			} else {
				if(!$requestmode) {
					
					if($sameuser) {
						showmessage('attachment_expired', '', ['aid' => aidencode($aid, 0, $attach['tid']), 'pid' => $attach['pid'], 'tid' => $attach['tid']]);
					} else {
						showmessage('attachment_expired_nosession', '', ['pid' => $attach['pid'], 'tid' => $attach['tid']]);
					}
				} else {
					exit;
				}
			}
		} else {
			if(!$requestmode) {
				showmessage('attachment_nonexistence');
			} else {
				exit;
			}
		}
	}
}

$readmod = getglobal('config/download/readmod');
$readmod = $readmod > 0 && $readmod < 5 ? $readmod : 2;

$refererhost = parse_url($_SERVER['HTTP_REFERER']);
$serverhost = $_SERVER['HTTP_HOST'];
if(($pos = strpos($serverhost, ':')) !== FALSE) {
	$serverhost = substr($serverhost, 0, $pos);
}

if(!$requestmode && $_G['setting']['attachrefcheck'] && $_SERVER['HTTP_REFERER'] && !($refererhost['host'] == $serverhost)) {
	showmessage('attachment_referer_invalid', NULL);
}

periodscheck('attachbanperiods');


loadcache('threadtableids');
$threadtableids = !empty($_G['cache']['threadtableids']) ? $_G['cache']['threadtableids'] : [];
if(!in_array(0, $threadtableids)) {
	$threadtableids = array_merge([0], $threadtableids);
}
$archiveid = in_array($_GET['archiveid'], $threadtableids) ? intval($_GET['archiveid']) : 0;


$attachexists = FALSE;
if(!empty($aid) && is_numeric($aid)) {
	$attach = table_forum_attachment_n::t()->fetch_attachment($tableid, $aid);
	$thread = table_forum_thread::t()->fetch_by_tid_displayorder($attach['tid'], 0, '>=', null, $archiveid);
	if($_G['uid'] && $attach['uid'] != $_G['uid']) {
		if($attach) {
			$attachpost = table_forum_post::t()->fetch_post($thread['posttableid'], $attach['pid'], false);
			$attach['invisible'] = $attachpost['invisible'];
			unset($attachpost);
		}
		if($attach && $attach['invisible'] == 0) {
			$thread && $attachexists = TRUE;
		}
	} else {
		$attachexists = TRUE;
	}
}

if(!$attachexists) {
	if(!$requestmode) {
		showmessage('attachment_nonexistence');
	} else {
		exit;
	}
}

require_once childfile('check');

require_once childfile('output');

function getremotefile($file) {
	global $_G;
	@set_time_limit(0);
	if(!@readfile($_G['setting']['ftp']['attachurl'].'forum/'.$file)) {
		$ftp = ftpcmd('object');
		$tmpfile = @tempnam($_G['setting']['attachdir'], '');
		if(is_object($ftp) && $ftp->ftp_get($tmpfile, 'forum/'.$file, FTP_BINARY)) {
			@readfile($tmpfile);
			@unlink($tmpfile);
		} else {
			@unlink($tmpfile);
			return FALSE;
		}
	}
	return TRUE;
}

function getlocalfile($filename, $readmod = 2, $range_start = 0, $range_end = 0) {
	if($readmod == 1 || $readmod == 3 || $readmod == 4) {
		if($fp = @fopen($filename, 'rb')) {
			@fseek($fp, $range_start);
			if(function_exists('fpassthru') && ($readmod == 3 || $readmod == 4) && ($range_end <= 0)) {
				@fpassthru($fp);
			} else {
				if($range_end > 0) {
					send_file_by_chunk($fp, $range_end - $range_start + 1);
				} else {
					send_file_by_chunk($fp);
				}
			}
		}
		@fclose($fp);
	} else {
		@readfile($filename);
	}
	@flush();
	@ob_flush();
}

function send_file_by_chunk($fp, $limit = PHP_INT_MAX) {
	static $CHUNK_SIZE = 65536; 
	$count = 0;
	while(!feof($fp)) {
		$size_to_read = $CHUNK_SIZE;
		if($count + $size_to_read > $limit) $size_to_read = $limit - $count;
		$buf = fread($fp, $size_to_read);
		echo $buf;
		flush();
		ob_flush();
		$count += strlen($buf);
		if($count >= $limit) break;
	}
}

function attachment_updateviews($logfile) {
	$viewlog = $viewarray = [];
	$newlog = $logfile.random(6);
	if(@rename($logfile, $newlog)) {
		$viewlog = file($newlog);
		unlink($newlog);
		if(is_array($viewlog) && !empty($viewlog)) {
			$viewlog = array_count_values($viewlog);
			foreach($viewlog as $id => $views) {
				if($id > 0) {
					$viewarray[$views][] = intval($id);
				}
			}
			foreach($viewarray as $views => $ids) {
				table_forum_attachment::t()->update_download($ids, $views);
			}
		}
	}
}

function ext_to_mimetype($path) {
	$ext = pathinfo($path, PATHINFO_EXTENSION);
	$map = [
		'aac' => 'audio/aac',
		'flac' => 'audio/flac',
		'mp3' => 'audio/mpeg',
		'm4a' => 'audio/mp4',
		'wav' => 'audio/wav',
		'ogg' => 'audio/ogg',
		'weba' => 'audio/webm',
		'flv' => 'video/x-flv',
		'mp4' => 'video/mp4',
		'm4v' => 'video/mp4',
		'3gp' => 'video/3gpp',
		'ogv' => 'video/ogg',
		'webm' => 'video/webm'
	];
	$mime = $map[$ext];
	if(!$mime) $mime = 'application/octet-stream';
	return $mime;
}

