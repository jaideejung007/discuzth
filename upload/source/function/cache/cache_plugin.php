<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_plugin() {
	global $importtxt;
	$data = $pluginsetting = [];
	require_once libfile('function/plugin');
	foreach(table_common_plugin::t()->fetch_all_data(1) as $plugin) {
		$dir = substr($plugin['directory'], 0, -1);
		$plugin['modules'] = dunserialize($plugin['modules']);
		$pluginarray = [];
		if($plugin['modules']['extra']['langexists']) {
			$file = getimportfilename(DISCUZ_PLUGIN($dir).'/discuz_plugin_'.$dir.($plugin['modules']['extra']['installtype'] ? '_'.$plugin['modules']['extra']['installtype'] : ''));
			if($file) {
				require_once libfile('function/admincp');
				$importtxt = @implode('', file($file));
				$pluginarray = getimportdata('Discuz! Plugin', 0, 1);
				if(getglobal('config/plugindeveloper') && !file_exists($langfile = DISCUZ_PLUGIN($dir).'/i18n/'.currentlang().'/lang_plugin.php')) {
					dmkdir(dirname($langfile));
					$langcontent = "<?php\nif(!defined('IN_DISCUZ')) {\n\texit('Access Denied');\n}\n\n";
					if(!empty($pluginarray['language']['scriptlang'])) {
						$langcontent .= '$scriptlang[\''.$plugin['identifier'].'\'] = '.var_export($pluginarray['language']['scriptlang'], 1).";\n\n";
					}
					if(!empty($pluginarray['language']['templatelang'])) {
						$langcontent .= '$templatelang[\''.$plugin['identifier'].'\'] = '.var_export($pluginarray['language']['templatelang'], 1).";\n\n";
					}
					if(!empty($pluginarray['language']['systemlang'])) {
						$langcontent .= '$systemlang[\''.$plugin['identifier'].'\'] = '.var_export($pluginarray['language']['systemlang'], 1).";\n\n";
					}
					if(!empty($pluginarray['language']['installlang'])) {
						$langcontent .= '$installlang[\''.$plugin['identifier'].'\'] = '.var_export($pluginarray['language']['installlang'], 1).";\n\n";
					}
					file_put_contents($langfile, $langcontent);
				}
			}
		}
		if($pluginarray) {
			updatepluginlanguage($pluginarray);
		}

		foreach(table_common_pluginvar::t()->fetch_all_by_pluginid($plugin['pluginid']) as $var) {
			if(strexists($var['type'], ':') || str_starts_with($var['type'], 'component_')) {
				admin\class_component::plugin_unserialize($var['type'], $var['value']);
			}
			$data[$plugin['identifier']][$var['variable']] = $var['value'];
			if(in_array(substr($var['type'], 0, 6), ['group_', 'forum_'])) {
				$stype = substr($var['type'], 0, 5).'s';
				$type = substr($var['type'], 6);
				if($type == 'select') {
					foreach(explode("\n", $var['extra']) as $key => $option) {
						$option = trim($option);
						if(!str_contains($option, '=')) {
							$key = $option;
						} else {
							$item = explode('=', $option);
							$key = trim($item[0]);
							$option = trim($item[1]);
						}
						$var['select'][] = [$key, $option];
					}
				}
				$pluginsetting[$stype][$plugin['identifier']]['name'] = $plugin['name'];
				$pluginsetting[$stype][$plugin['identifier']]['setting'][$var['pluginvarid']] = [
					'title' => $var['title'],
					'description' => $var['description'],
					'type' => $type,
					'select' => $var['select'],
					'variable' => $var['variable'],
				];
				if(str_starts_with($var['variable'], 'fields_')) {
					unset($data[$plugin['identifier']][$var['variable']]);
				}
			}
			if($var['displayorder'] >= 0) {
				$pluginsetting['config'][$plugin['identifier']] = true;
			}
		}
	}

	savecache('pluginsetting', $pluginsetting);
	savecache('plugin', $data);
}

