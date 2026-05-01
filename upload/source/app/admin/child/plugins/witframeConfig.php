<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('editsubmit')) {

	loadwitframe();
	$baseConf = Lib\Core::GetSetting();
	if(!$baseConf) {
		cpmsg('plugin_not_found', '', 'error');
	}

	$witPlugins = witframe_plugin::pluginList();
	$apis = witframe_plugin::getApis($witPlugins);
	if(empty($apis[$_GET['do']])) {
		cpmsg('plugin_not_found', '', 'error');
	}

	$plugin = $apis[$_GET['do']];

	$submenus = [];
	foreach($plugin['config'] as $config) {
		$submenus[$config['type']] = ['witframe_type_'.$config['type'], 'plugins&operation=witframeConfig&do='.$_GET['do'].'&type='.$config['type']];
	}
	$type = !empty($_GET['type']) ? $_GET['type'] : array_keys($submenus)[0];
	!empty($submenus[$type]) && $submenus[$type][2] = 1;

	if(!$submenus) {
		cpmsg('plugin_not_found', '', 'error');
	}

	//
	shownav('plugin', $plugin['appName']);
	showsubmenu($plugin['appName'], $submenus);

	if($type == 'page') {
		showformheader('plugins&operation=witframeConfig&do='.$_GET['do'].'&type='.$type);
		showtableheader();

		showtitle('witframe_nav_link');
		foreach($plugin['config'] as $config) {
			if($config['type'] != $type) {
				continue;
			}
			$path = $plugin['path'].'/'.$config['page'];

			$data = table_common_nav::t()->fetch_all_by_type_identifier(6, $path);
			$nav = [];
			foreach($data as $row) {
				$nav[$row['identifier']][$row['navtype']] = $row['navtype'];
			}

			$title = '<a href="index.php?app=witframe&path='.$path.'" target="_blank">'.$config['name'].'</a>';
			$selectdata = ['navnew['.$path.'][]', [
				[0, $lang['plugins_edit_modules_type_1']],
				[4, $lang['plugins_edit_modules_type_27']],
				[1, $lang['plugins_edit_modules_type_23']],
				[3, $lang['plugins_edit_modules_type_25']],
				[2, $lang['plugins_edit_modules_type_24']],
				[5, $lang['plugins_edit_modules_type_30']],
			]];
			showsetting($title, $selectdata, $nav[$path], 'mselect_6');
			showhiddenfields(['navname['.$path.']' => $config['name']]);
		}
		showsubmit('editsubmit');
		showtablefooter();
		showformfooter();

	} elseif($type == 'diy') {
		showtips('witframe_diy_tips');

		showtableheader();
		foreach($plugin['config'] as $config) {
			if($config['type'] != $type) {
				continue;
			}
			$path = $plugin['path'].'/'.$config['page'];

			echo '<tr><td class="td27">'.$config['name'].'</td></tr>';
		}
		showtablefooter();
	}

} else {

	foreach($_GET['navname'] as $identifier => $name) {
		$data = table_common_nav::t()->fetch_all_by_type_identifier(6, $identifier);
		$nav = [];
		foreach($data as $row) {
			$nav[$row['identifier']][$row['navtype']] = $row['navtype'];
			if(!in_array($row['navtype'], (array)$_GET['navnew'][$identifier])) {
				table_common_nav::t()->delete_by_type_identifier(6, $row['identifier']);
			}
		}
	}

	foreach($_GET['navnew'] as $identifier => $navtypes) {
		foreach($navtypes as $navtype) {
			if(!isset($nav[$identifier][$navtype])) {
				table_common_nav::t()->insert([
					'name' => $_GET['navname'][$identifier],
					'title' => '',
					'url' => 'index.php?app=witframe&path='.$identifier,
					'type' => 6,
					'identifier' => $identifier,
					'navtype' => $navtype,
					'available' => 0,
					'icon' => '',
					'subname' => '',
					'suburl' => '',
				]);
			}
		}
	}

	updatecache(['setting']);
	cpmsg('plugins_edit_vars_succeed', 'action=plugins&operation=witframeConfig&do='.$_GET['do'].'&type='.$_GET['type'], 'succeed');
}
	