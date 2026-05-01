<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_admincp_menu() {
	global $_G;

	$subperms = [];
	foreach(table_common_admincp_menu_platform::t()->fetch_all_data() as $data) {
		$menuData = dunserialize($data['menu']);
		foreach($menuData['menu'] as $topmenu => $submenu) {
			foreach($submenu as $row) {
				if(!empty($row[5])) {
					foreach($row[5] as $perm) {
						$subperms[$perm] = $row[1];
					}
				}
			}
		}
	}

	$_G['cache']['admin']['subperms'] = $subperms;
	savecache('admin', $_G['cache']['admin']);
}

