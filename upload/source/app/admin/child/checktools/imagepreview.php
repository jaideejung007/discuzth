<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$settingnew = $_GET['settingnew'];
if(!empty($_GET['previewthumb'])) {
	$_G['setting']['imagelib'] = $settingnew['imagelib'];
	$_G['setting']['thumbwidth'] = $settingnew['thumbwidth'];
	$_G['setting']['thumbheight'] = $settingnew['thumbheight'];
	$_G['setting']['thumbquality'] = $settingnew['thumbquality'];

	require_once libfile('class/image');
	@unlink(DISCUZ_ROOT.$_G['setting']['attachdir'].'./temp/watermark_temp1.jpg');
	@unlink(DISCUZ_ROOT.$_G['setting']['attachdir'].'./temp/watermark_temp2.jpg');
	$image = new image;
	$r = 0;
	if(!($r = $image->Thumb(DISCUZ_ROOT.'./static/image/admincp/watermarkpreview.jpg', 'temp/watermark_temp1.jpg', $_G['setting']['thumbwidth'], $_G['setting']['thumbheight'], 1))) {
		$r = $image->error();
	}
	$sizetarget1 = $image->imginfo['size'];
	$image->Thumb(DISCUZ_ROOT.'./static/image/admincp/watermarkpreview.jpg', 'temp/watermark_temp2.jpg', $_G['setting']['thumbwidth'], $_G['setting']['thumbheight'], 2);
	$sizetarget2 = $image->imginfo['size'];
	if($r > 0) {
		showsubmenu('imagepreview_thumb');
		$sizesource = filesize(DISCUZ_ROOT.'./static/image/admincp/watermarkpreview.jpg');
		echo '<img src="data/attachment/temp/watermark_temp1.jpg?'.random(5).'"><br /><br />'.
			$lang['imagepreview_imagesize_source'].' '.number_format($sizesource).' Bytes &nbsp;&nbsp;'.
			$lang['imagepreview_imagesize_target'].' '.number_format($sizetarget1).' Bytes ('.
			(sprintf('%2.1f', $sizetarget1 / $sizesource * 100)).'%)<br /><br />';
		echo '<img src="data/attachment/temp/watermark_temp2.jpg?'.random(5).'"><br /><br />'.
			$lang['imagepreview_imagesize_source'].' '.number_format($sizesource).' Bytes &nbsp;&nbsp;'.
			$lang['imagepreview_imagesize_target'].' '.number_format($sizetarget2).' Bytes ('.
			(sprintf('%2.1f', $sizetarget2 / $sizesource * 100)).'%)';
	} else {
		cpmsg('imagepreview_errorcode_'.$r, '', 'error');
	}
} else {
	$type = $_GET['type'];
	$status = dunserialize($_G['setting']['watermarkstatus']);
	$status = is_array($status) ? $status : [];
	if(!array_key_exists($type, $status) || !$status[$type]) {
		cpmsg('watermarkpreview_error', '', 'error');
	}
	require_once libfile('class/image');
	@unlink(DISCUZ_DATA.'./attachment/temp/watermark_temp3.jpg');
	$image = new image;
	if(!($r = $image->Watermark(DISCUZ_ROOT.'./static/image/admincp/watermarkpreview.jpg', 'temp/watermark_temp3.jpg', $type))) {
		$r = $image->error();
	}
	if($r > 0) {
		showsubmenu('imagepreview_watermark');
		$sizesource = filesize('static/image/admincp/watermarkpreview.jpg');
		$sizetarget = $image->imginfo['size'];
		echo '<img src="data/attachment/temp/watermark_temp3.jpg?'.random(5).'"><br /><br />'.
			$lang['imagepreview_imagesize_source'].' '.number_format($sizesource).' Bytes &nbsp;&nbsp;'.
			$lang['imagepreview_imagesize_target'].' '.number_format($sizetarget).' Bytes ('.
			(sprintf('%2.1f', $sizetarget / $sizesource * 100)).'%)';
	} else {
		cpmsg('imagepreview_errorcode_'.$r, '', 'error');
	}
}
	