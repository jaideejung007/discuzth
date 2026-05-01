<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($pluginid) && !empty($do)) {
	$pluginid = $do;
}
if($_GET['identifier']) {
	$plugin = table_common_plugin::t()->fetch_by_identifier($_GET['identifier']);
} else {
	$plugin = table_common_plugin::t()->fetch($pluginid);
}
if(!$plugin) {
	cpmsg('plugin_not_found', '', 'error');
} else {
	$pluginid = $plugin['pluginid'];
}

$plugin['modules'] = dunserialize($plugin['modules']);

$pluginvars = [];
$custom = $customMenus = false;
$anchorCount = 0;
foreach(table_common_pluginvar::t()->fetch_all_visible_by_pluginid($pluginid) as $var) {
	$pluginvars[$var['variable']] = $var;
	if(str_starts_with($var['type'], 'style')) {
		$custom = true;
	}
	if($var['type'] == 'stylePage') {
		if(!$_GET['anchor'] && !$anchorCount) {
			$_GET['anchor'] = $var['variable'];
		}
		$customMenus = true;
		$submenuitem[] = [$lang[$var['title']] ?? dhtmlspecialchars($var['title']),
			!empty($_GET['pmod']) ? 'plugins&operation=config&do='.$pluginid.'#anchor_'.$var['variable'] : $var['variable'], empty($_GET['pmod']) && $_GET['anchor'] == $var['variable']];
		$anchorCount++;
	}
}

if($pluginvars) {
	if(!$customMenus) {
		$submenuitem[] = ['config', "plugins&operation=config&do=$pluginid", !$_GET['pmod']];
	}
}
if(is_array($plugin['modules'])) {
	foreach($plugin['modules'] as $module) {
		if(intval($module['displayorder']) < 0) {
			continue;
		}
		if(isset($module['type']) && $module['type'] == 3) {
			parse_str($module['param'], $param);
			if(!$pluginvars && empty($_GET['pmod'])) {
				$_GET['pmod'] = $module['name'];
				if($param) {
					foreach($param as $_k => $_v) {
						$_GET[$_k] = $_v;
					}
				}
			}
			if($param) {
				$m = true;
				foreach($param as $_k => $_v) {
					if(!isset($_GET[$_k]) || $_GET[$_k] != $_v) {
						$m = false;
						break;
					}
				}
			} else {
				$m = true;
			}
			$submenuitem[] = [$module['menu'], "plugins&operation=config&do=$pluginid&identifier={$plugin['identifier']}&pmod={$module['name']}".($module['param'] ? '&'.$module['param'] : ''), $_GET['pmod'] == $module['name'] && $m, !$_GET['pmod'] ? 1 : 0];
		}
	}
}

if(empty($_GET['pmod'])) {

	if(!submitcheck('editsubmit')) {
		$operation = '';
		shownav('plugin', $plugin['name']);
		showsubmenuanchors($plugin['name'].' '.$plugin['version'].(!$plugin['available'] ? ' ('.$lang['plugins_unavailable'].')' : ''), $submenuitem);

		if($pluginvars) {
			showformheader("plugins&operation=config&do=$pluginid", 'enctype');
			if(!$custom) {
				showtableheader();
				showtitle($lang['plugins_config']);
			}

			foreach($pluginvars as $var) {
				$extra = [];
				if(strexists($var['type'], ':') || str_starts_with($var['type'], 'component_')) {
					$var['variable'] = 'varsnew['.$var['variable'].']';
					$_G['showcomponent'][$var['type']][] = $var['variable'];
					admin\class_component::type_plugin($var, $extra);
				} else {
					if(strexists($var['type'], '_')) {
						continue;
					}
					$var['var'] = $var['variable'];
					$var['variable'] = 'varsnew['.$var['variable'].']';
					if($var['type'] == 'styleTitle') {
						showtitle($lang[$var['title']] ?? dhtmlspecialchars($var['title']));
						continue;
					} elseif($var['type'] == 'stylePage') {
						if($boxHeader) {
							showsubmit('editsubmit');
							showtablefooter();
							echo '</div>';
							$boxHeader = false;
						}
						$boxHeader = true;
						echo '<div id="'.$var['var'].'"'.($_GET['anchor'] != $var['var'] ? ' style="display: none"' : '').'>';
						showtableheader();
						continue;
					} elseif(method_exists('admin\class_component', $_method = 'type_'.$var['type'])) {
						admin\class_component::$_method($var, $extra);
					}
				}

				showsetting($lang[$var['title']] ?? dhtmlspecialchars($var['title']), $var['variable'], $var['value'], $var['type'], '', 0, $lang[$var['description']] ?? nl2br(dhtmlspecialchars($var['description'])), dhtmlspecialchars($var['extra']), '', true, 0, !empty($var['widemode']));
				echo implode('', $extra);
			}
			if(!$custom || $boxHeader) {
				showsubmit('editsubmit');
				showtablefooter();
				showboxfooter();
			}
			if(!$custom) {
				showformfooter();
			}
		}

	} else {

		if(isset($_FILES['varsnew']['name'])) {
			$upload = new discuz_upload();
			foreach($_FILES['varsnew']['name'] as $varid => $value) {
				$file = [
					'name' => $_FILES['varsnew']['name'][$varid],
					'type' => $_FILES['varsnew']['type'][$varid],
					'tmp_name' => $_FILES['varsnew']['tmp_name'][$varid],
					'error' => $_FILES['varsnew']['error'][$varid],
					'size' => $_FILES['varsnew']['size'][$varid],
				];
				$_GET['varsnew'][$varid] = admin\class_attach::upload($file, 'common', 'plugin', 0, $value);
			}
		}
		if(!empty($_GET['deleteUploadimage'])) {
			foreach($_GET['deleteUploadimage'] as $key) {
				if(!isset($_GET['varsnew'][$key])) {
					continue;
				}
				admin\class_attach::delete($_GET['varsnew'][$key]);
				$_GET['varsnew'][$key] = '';
			}
		}

		if(is_array($_GET['varsnew'])) {
			foreach($_GET['varsnew'] as $variable => $value) {
				if(isset($pluginvars[$variable])) {
					if($pluginvars[$variable]['type'] == 'number') {
						$value = (float)$value;
					} elseif(in_array($pluginvars[$variable]['type'], ['forums', 'groups', 'selects', 'portalcats'])) {
						$value = serialize($value);
					}
					$value = (string)$value;
					table_common_pluginvar::t()->update_by_variable($pluginid, $variable, ['value' => $value]);
				}
			}
		}

		updatecache(['plugin', 'setting', 'styles']);
		cleartemplatecache();
		cpmsg('plugins_setting_succeed', 'action=plugins&operation=config&do='.$pluginid.'&anchor='.$anchor, 'succeed');

	}

} else {

	$scriptlang[$plugin['identifier']] = lang('plugin/'.$plugin['identifier']);
	$modfile = '';
	if(is_array($plugin['modules'])) {
		foreach($plugin['modules'] as $module) {
			if(isset($module['type']) && $module['type'] == 3 && $module['name'] == $_GET['pmod']) {
				$plugin['directory'] .= (!empty($plugin['directory']) && !str_ends_with($plugin['directory'], '/')) ? '/' : '';
				$modfile = DISCUZ_PLUGIN($plugin['directory']).$module['name'].'.inc.php';
				break;
			}
		}
	}

	if($modfile) {
		$hidemenu_block = false;
		if(!empty($_GET['pmod']) && is_array($plugin['modules'])) {
			$curr_pmod_key = array_search($_GET['pmod'], array_column($plugin['modules'], 'name'));
			if($plugin['modules'][$curr_pmod_key]['displayorder'] == -2) {
				$hidemenu_block = true;
			}
		}
		if(!$hidemenu_block) {
			shownav('plugin', $plugin['name']);
			showsubmenu($plugin['name'].' '.$plugin['version'].(!$plugin['available'] ? ' ('.$lang['plugins_unavailable'].')' : ''), $submenuitem);
		} else {
			shownav('plugin', $plugin['name']);
			echo '</div><div class="cpcontainer">';
		}
		if(!@include($modfile)) {
			cpmsg('plugins_setting_module_nonexistence', '', 'error', ['modfile' => $plugin['directory'].$module['name'].'.inc.php']);
		} else {
			exit();
		}
	} else {
		if(preg_match('/^[a-z0-9_\-\/]+$/i', $_GET['pmod'])) {
			$_rp = strrpos($_GET['pmod'], '/');
			if($_rp !== false) {
				$_path = str_replace('/', '\\', substr($_GET['pmod'], 0, $_rp));
				$c = $plugin['identifier'].'\admin\\'.$_path.'_'.substr($_GET['pmod'], $_rp + 1);
			} else {
				$c = $plugin['identifier'].'\admin_'.$_GET['pmod'];
			}
			if(!class_exists($c)) {
				cpmsg('plugin_file_error', '', 'error');
			}
			if(method_exists($c, 'run')) {
				$c::run();
			} else {
				$p = new $c();
				$p->init();
			}
		} else {
			cpmsg('plugin_file_error', '', 'error');
		}
	}

}
	