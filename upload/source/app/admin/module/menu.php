<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

global $_G;

require_once childfile('menu/class');

$isfounder = $isfounder ?? isfounder();

loaducenter();

$menuData = [];

menu_loader::run($menuData);

$menu = &$menuData['menu'];
foreach($menu as $top => $v) {
	if(empty($v)) {
		continue;
	}
	$topmenu[$top] = '';
}

if(!$isfounder && !isset($GLOBALS['admincp']->perms['all'])) {
	$menunew = $menu;
	foreach($menu as $topkey => $datas) {
		if($topkey == 'index') {
			continue;
		}
		$itemexists = 0;
		foreach($datas as $key => $data) {
			if(array_key_exists($data[1], $GLOBALS['admincp']->perms)) {
				$itemexists = 1;
			} else {
				unset($menunew[$topkey][$key]);
			}
		}
		if(!$itemexists) {
			unset($topmenu[$topkey]);
			unset($menunew[$topkey]);
		}
	}
	$menu = $menunew;
}

if(PLATFORM == 'system') {
	loadcache('admincp_menu');
	if(!empty($_G['cache']['admincp_menu'])) {
		$topmenu = array_merge($topmenu, $_G['cache']['admincp_menu']['topmenu']);
		$menu = array_merge($menu, $_G['cache']['admincp_menu']['menu']);
	}
}