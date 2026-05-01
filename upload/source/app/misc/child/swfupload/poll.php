<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowpostpoll'] || !$_G['group']['allowpostimage']) {
	exit("{\"aid\":0, \"errorcode\":4}");
}

$upload = new discuz_upload();

$_FILES['Filedata']['name'] = addslashes(diconv(urldecode($_FILES['Filedata']['name']), 'UTF-8'));
$upload->init($_FILES['Filedata'], 'forum');
$attach = $upload->attach;
if(!$upload->attach['isimage']) {
	$errorcode = 4;
} else {
	$upload->save();
	$errorcode = 0;
}
if($upload->error()) {
	$errorcode = 4;
} else {
	if($attach['isimage']) {
		require_once libfile('class/image');
		$image = new image();
		$thumbimgwidth = 300;
		$thumbimgheight = 300;
		$attach['thumb'] = $image->Thumb($attach['target'], '', $thumbimgwidth, $thumbimgheight, 2);
		$image->Watermark($attach['target'], '', 'forum');
		$imginfo = @getimagesize($attach['target']);
		if($imginfo !== FALSE) {
			$attach['width'] = $imginfo[0];
			$attach['height'] = $imginfo[1];
		}
	}

	$attach['remote'] = 0;
	if(ftpperm($attach['ext'], $attach['size'])) {
		if(ftpcmd('upload', 'forum/'.$attach['attachment']) && (!$attach['thumb'] || ftpcmd('upload', 'forum/'.getimgthumbname($attach['attachment'])))) {
			@unlink($_G['setting']['attachdir'].'/forum/'.$attach['attachment']);
			@unlink($_G['setting']['attachdir'].'/forum/'.getimgthumbname($attach['attachment']));
			$attach['remote'] = 1;
		} else {
			if(getglobal('setting/ftp/mirror')) {
				@unlink($attach['target']);
				@unlink(getimgthumbname($attach['target']));
				$errorcode = 5;
			}
		}
	}
}
if(!$errorcode) {
	$aid = intval($_GET['aid']);
	$setarr = [
		'uid' => $_G['uid'],
		'filename' => $attach['name'],
		'attachment' => $attach['attachment'],
		'filesize' => $attach['size'],
		'thumb' => $attach['thumb'],
		'remote' => $attach['remote'],
		'dateline' => $_G['timestamp'],
		'width' => $attach['width'],
		'height' => $attach['height']
	];
	$image = [];
	if($aid) {
		$image = table_forum_polloption_image::t()->fetch($aid);
	}
	if($image['uid'] == $_G['uid']) {
		table_forum_polloption_image::t()->update($aid, $setarr);
		@unlink($_G['setting']['attachdir'].'/forum/'.$image['attachment']);
		@unlink($_G['setting']['attachdir'].'/forum/'.getimgthumbname($image['attachment']));
		$attach['attachid'] = $aid;
	} else {
		$attach['attachid'] = table_forum_polloption_image::t()->insert($setarr, true);
	}

	require_once libfile('function/home');
	$smallimg = pic_get($attach['attachment'], 'forum', $attach['thumb'], $attach['remote']);
	$bigimg = pic_get($attach['attachment'], 'forum', 0, $attach['remote']);
	echo "{\"aid\":{$attach['attachid']}, \"smallimg\":\"$smallimg\", \"bigimg\":\"$bigimg\", \"errorcode\":$errorcode}";
	exit();
} else {
	echo "{\"aid\":0, \"errorcode\":$errorcode}";
}
	