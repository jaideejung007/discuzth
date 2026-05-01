<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once libfile('function/cloudaddons');

cpheader();

if(!$admincp->isfounder) {
	cpmsg('noaccess_isfounder', '', 'error');
}

if(!$operation || in_array($operation, ['plugins', 'templates'])) {
	require_once childfile('cloudaddons/location');
} else {
	$file = childfile('cloudaddons/'.$operation);
	if(!file_exists($file)) {
		cpmsg('undefined_action');
	}
	require_once $file;
}

function dir_clear($dir) {
	if($directory = @dir($dir)) {
		while($entry = $directory->read()) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$filename = $dir.'/'.$entry;
			if(is_file($filename)) {
				@unlink($filename);
			} else {
				dir_clear($filename);
			}
		}
		$directory->close();
		@rmdir($dir);
	}
}

