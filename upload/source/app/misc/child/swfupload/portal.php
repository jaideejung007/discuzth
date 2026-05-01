<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$aid = intval($_POST['aid']);
$catid = intval($_POST['catid']);
$msg = '';
$errorcode = 0;
require_once libfile('function/portalcp');
if($aid) {
	$article = table_portal_article_title::t()->fetch($aid);
	if(!$article) {
		$errorcode = 1;
	}

	if(check_articleperm($catid, $aid, $article, false, true) !== true) {
		$errorcode = 2;
	}

} else {
	if(check_articleperm($catid, $aid, null, false, true) !== true) {
		$errorcode = 3;
	}
}

$upload = new discuz_upload();

$_FILES['Filedata']['name'] = addslashes(diconv(urldecode($_FILES['Filedata']['name']), 'UTF-8'));
$upload->init($_FILES['Filedata'], 'portal');
$attach = $upload->attach;
if(!$upload->error()) {
	$upload->save();
}
if($upload->error()) {
	$errorcode = 4;
}
if(!$errorcode) {
	if($attach['isimage'] && empty($_G['setting']['portalarticleimgthumbclosed'])) {
		require_once libfile('class/image');
		$image = new image();
		$thumbimgwidth = $_G['setting']['portalarticleimgthumbwidth'] ? $_G['setting']['portalarticleimgthumbwidth'] : 300;
		$thumbimgheight = $_G['setting']['portalarticleimgthumbheight'] ? $_G['setting']['portalarticleimgthumbheight'] : 300;
		$attach['thumb'] = $image->Thumb($attach['target'], '', $thumbimgwidth, $thumbimgheight, 2);
		$image->Watermark($attach['target'], '', 'portal');
	}

	if(ftpperm($attach['ext'], $attach['size'])) {
		if(ftpcmd('upload', 'portal/'.$attach['attachment']) && (!$attach['thumb'] || ftpcmd('upload', 'portal/'.getimgthumbname($attach['attachment'])))) {
			@unlink($_G['setting']['attachdir'].'/portal/'.$attach['attachment']);
			@unlink($_G['setting']['attachdir'].'/portal/'.getimgthumbname($attach['attachment']));
			$attach['remote'] = 1;
		} else {
			if(getglobal('setting/ftp/mirror')) {
				@unlink($attach['target']);
				@unlink(getimgthumbname($attach['target']));
				$errorcode = 5;
			}
		}
	}

	$setarr = [
		'uid' => $_G['uid'],
		'filename' => $attach['name'],
		'attachment' => $attach['attachment'],
		'filesize' => $attach['size'],
		'isimage' => $attach['isimage'],
		'thumb' => $attach['thumb'],
		'remote' => $attach['remote'] ?? 0,
		'filetype' => $attach['extension'],
		'dateline' => $_G['timestamp'],
		'aid' => $aid,
	];
	$setarr['attachid'] = table_portal_attachment::t()->insert($setarr, true);
	if($attach['isimage']) {
		require_once libfile('function/home');
		$smallimg = pic_get($attach['attachment'], 'portal', $attach['thumb'], $attach['remote']);
		$bigimg = pic_get($attach['attachment'], 'portal', 0, $attach['remote']);
		$coverstr = addslashes(serialize(['pic' => 'portal/'.$attach['attachment'], 'thumb' => $attach['thumb'], 'remote' => $attach['remote']]));
		echo "{\"aid\":{$setarr['attachid']}, \"isimage\":{$attach['isimage']}, \"smallimg\":\"$smallimg\", \"bigimg\":\"$bigimg\", \"errorcode\":$errorcode, \"cover\":\"$coverstr\"}";
		exit();
	} else {
		$fileurl = 'portal.php?mod=attachment&id='.$attach['attachid'];
		echo "{\"aid\":{$setarr['attachid']}, \"isimage\":{$attach['isimage']}, \"file\":\"$fileurl\", \"errorcode\":$errorcode}";
		exit();
	}
} else {
	echo "{\"aid\":0, \"errorcode\":$errorcode}";
}
	