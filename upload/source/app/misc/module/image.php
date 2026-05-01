<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || empty($_GET['aid']) || empty($_GET['size']) || empty($_GET['key'])) {
	header('location: '.$_G['siteurl'].'static/image/common/none.gif');
	exit;
}

$nocache = !empty($_GET['nocache']) ? 1 : 0;
$module = in_array($_GET['module'], ['forum', 'doing']) ? $_GET['module'] : 'forum';
$daid = intval($_GET['aid']);
$type = !empty($_GET['type']) ? $_GET['type'] : 'fixwr';
[$w, $h] = explode('x', $_GET['size']);
$dw = intval($w);
$dh = intval($h);
$thumbfile = ($module == 'forum' ? 'image/' : $module.'/thumb/').helper_attach::makethumbpath($daid, $dw, $dh);
$attachurl = helper_attach::attachpreurl();
if(!$nocache && !$_G['setting']['ftp']['on']) {
	if(file_exists($_G['setting']['attachdir'].$thumbfile)) {
		dheader('location: '.$attachurl.$thumbfile);
	}
}


$oss = null;
$oss_config = getglobal('setting/oss');
if($_G['setting']['ftp']['on'] == 2) {
	$oss_config['oss_key'] = authcode($oss_config['oss_key'], 'DECODE', md5(getglobal('config/security/authkey')));
	$oss = oss::loadOSS($oss_config);
}

if(!$nocache && !empty($oss) && $oss->isObject($oss_config['oss_rootpath'].$thumbfile)) {
	dheader('location: '.$_G['setting']['ftp']['attachurl'].$thumbfile);
	exit();
}

const NOROBOT = true;
$id = !empty($_GET['atid']) ? $_GET['atid'] : $daid;
if(dsign($module.'|'.$id.'|'.$dw.'|'.$dh) != $_GET['key']) {
	dheader('location: '.$_G['siteurl'].'static/image/common/none.gif');
}
if($module == 'forum'){
	$attach = table_forum_attachment_n::t()->fetch_attachment('aid:'.$daid, $daid, [2, 1, -1]);
}elseif($module == 'doing'){
	$attach = table_home_doing_attachment::t()->fetch_attachment('aid:'.$daid, $daid, [2, 1, -1]);
}

if($attach) {
	$isImage = abs($attach['isimage']) == 1;
	if($isImage && !$dw && !$dh && $attach['tid'] != $id) {
		dheader('location: '.$_G['siteurl'].'static/image/common/none.gif');
	}
	dheader('Expires: '.gmdate('D, d M Y H:i:s', TIMESTAMP + 3600).' GMT');
	if($attach['remote']) {
		$filename = $_G['setting']['ftp']['attachurl'].$module.'/'.$attach['attachment'];
	} else {
		$filename = $_G['setting']['attachdir'].$module.'/'.$attach['attachment'];
	}
	if(!$isImage) {
		$filename .= '.thumb.jpg';
	}
	$e = parse_url($filename);
	if(!empty($e['host'])) {
		$content = dfsockopen($filename, timeout: 10);
		if($content) {
			$filename = $_G['setting']['attachdir'].$thumbfile;
			dmkdir(dirname($filename));
			file_put_contents($filename, $content);
		}
		if(!file_exists($filename)) {
			$filename = $_G['setting']['attachurl'].$module.'/'.$attach['attachment'];
			if(empty($oss)) {
				dheader('Content-Type: image');
				@readfile($filename);
				exit;
			}
		}

	}
	require_once libfile('class/image');
	$img = new image;
	if($img->Thumb($filename, $thumbfile, $w, $h, $type)) {
		if($nocache) {
			dheader('Content-Type: image');
			@readfile($_G['setting']['attachdir'].$thumbfile);
			@unlink($_G['setting']['attachdir'].$thumbfile);
		} else {
			if(!empty($oss)) {
				$oss->uploadFile($_G['setting']['attachdir'].$thumbfile, $oss_config['oss_rootpath'].$thumbfile, 'public');
				@unlink($_G['setting']['attachdir'].$thumbfile);
			}
			dheader('location: '.$attachurl.$thumbfile);
		}
	} else {
		dheader('Content-Type: image');
		@readfile($filename);
	}
}