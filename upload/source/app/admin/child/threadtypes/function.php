<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

function threadtype_data($pluginid, &$typeSetting) {
	$dir = DISCUZ_PLUGIN($pluginid).'/threadtype';
	if(!file_exists($dir)) {
		return false;
	}

	$typedir = dir($dir);
	while($filename = $typedir->read()) {
		if(!in_array($filename, ['.', '..']) && fileext($filename) == 'php') {
			$c = substr($filename, 0, -4);
			if(!class_exists($class = '\\'.$pluginid.'\\'.$c)) {
				continue;
			}
			$_n = str_replace('threadtype_', '', $c);
			$n = new $class();
			$name = property_exists($n, 'name') ? $n->name : $pluginid.'_'.$_n;
			$desc = property_exists($n, 'desc') ? $n->desc : '';
			$typeSetting[$pluginid.':'.$_n] = [
				$pluginid.':'.$_n,
				$name,
				$desc,
			];
		}
	}
}

function threadtype_sysdata(&$typeSetting) {
	$dir = DISCUZ_ROOT.'./source/class/threadtype';

	$typedir = dir($dir);
	while($filename = $typedir->read()) {
		if(!in_array($filename, ['.', '..']) && fileext($filename) == 'php') {
			$class = substr($filename, 0, -4);
			if(!class_exists($class)) {
				continue;
			}
			$n = new $class();
			$name = property_exists($n, 'name') ? $n->name : $class;
			$desc = property_exists($n, 'desc') ? $n->desc : '';
			$typeSetting[$class] = [
				$class,
				$name,
				$desc,
			];
		}
	}
}