<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

/*

php.ini

opcache.preload=/[path]/api/opcache/preload.php
opcache.preload_user=www

*/

$paths = [
	'/source/class/discuz',
	'/source/function',
	'/source/child',
	'/source/i18n',
	'/source/app/forum',
	'/source/app/home',
	'/source/app/misc',
];
$root = dirname(dirname(__DIR__));

foreach($paths as $path) {
	$dir = new RecursiveDirectoryIterator($root.$path);
	$iterator = new RecursiveIteratorIterator($dir);
	foreach($iterator as $file) {
		if($file->isDir()) {
			continue;
		}
		opcache_compile_file($file);
	}
}