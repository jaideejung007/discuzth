<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_postimg() {
	$imgextarray = ['jpg', 'gif', 'png'];
	$imgdir = ['hrline', 'postbg'];
	$postimgjs = 'var postimg_type = new Array();';
	foreach($imgdir as $perdir) {
		$count = 0;
		$pdir = DISCUZ_ROOT.'./static/image/'.$perdir;
		$postimgdir = dir($pdir);
		$postimgjs .= 'postimg_type["'.$perdir.'"]=[';
		while($entry = $postimgdir->read()) {
			if(in_array(strtolower(fileext($entry)), $imgextarray) && preg_match('/^[\w\-\.\[\]\(\)\<\> &]+$/', substr($entry, 0, strrpos($entry, '.'))) && strlen($entry) < 30 && is_file($pdir.'/'.$entry)) {
				$postimg[$perdir][] = ['url' => $entry];
				$postimgjs .= ($count ? ',' : '').'"'.$entry.'"';
				$count++;
			}
		}
		$postimgjs .= '];';
		$postimgdir->close();
	}
	savecache('postimg', $postimg);
	$cachedir = DISCUZ_DATA.'./cache/';
	if(!is_dir($cachedir)) {
		dmkdir($cachedir);
	}
	if(file_put_contents($cachedir.'common_postimg.js', $postimgjs, LOCK_EX) === false) {
		exit('Can not write to cache files, please check directory ./data/ and ./data/cache/ .');
	}
	if(defined('IN_UPDATECACHE')) {
		oss::writeCache('common_postimg.js');
	}
}

