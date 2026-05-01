<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$showerror = true;
if(helper_access::check_module('album')) {
	require_once libfile('function/spacecp');
	if($_FILES['Filedata']['error']) {
		$file = lang('spacecp', 'file_is_too_big');
	} else {
		require_once libfile('function/home');
		$_FILES['Filedata']['name'] = addslashes(diconv(urldecode($_FILES['Filedata']['name']), 'UTF-8'));
		$file = pic_save($_FILES['Filedata'], 0, '', true, 0);
		if(!empty($file) && is_array($file)) {
			$url = pic_get($file['filepath'], 'album', $file['thumb'], $file['remote']);
			$bigimg = pic_get($file['filepath'], 'album', 0, $file['remote']);
			echo "{\"picid\":\"{$file['picid']}\", \"url\":\"$url\", \"bigimg\":\"$bigimg\"}";
			$showerror = false;
		}
	}
}
if($showerror) {
	echo "{\"picid\":\"0\", \"url\":\"0\", \"bigimg\":\"0\"}";
}
	