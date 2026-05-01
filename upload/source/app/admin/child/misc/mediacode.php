<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$mediacodes = get_mediacode_list();

if(!submitcheck('mediacodesubmit')) {

	showsubmenu('setting_editor', [
		['setting_editor_global', 'setting&operation=editor', 0],
		['setting_editor_code', 'misc&operation=bbcode', 0],
		['setting_editor_media', 'misc&operation=mediacode', 1],
		['setting_editor_block', 'editorblock&operation=list', 0]
	]);

	/*search={"setting_editor":"action=setting&operation=editor","setting_editor_code":"action=setting&operation=mediacode"}*/
	showtips('misc_mediacode_edit_tips');
	showformheader('misc&operation=mediacode');
	showtableheader('', 'fixpadding');
	showsubtitle(['available', 'misc_mediacode_name', 'misc_mediacode_version']);
	foreach($mediacodes as $key => $mediacode) {
		showtablerow('', ['class="td25"', 'class="td21"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"available[]\" value=\"{$key}\"".($mediacode['available'] ? ' checked' : '').'>',
			$mediacode['name'],
			$mediacode['version'],
		]);
	}
	showsubmit('mediacodesubmit', 'submit');
	showtablefooter();
	showformfooter();
	/*search*/

} else {

	$mediasetting = [];
	foreach($_GET['available'] as $mediacode) {
		[$script, $t] = explode(':', $mediacode);
		if(empty($t)) {
			$mediasetting['system'][] = $script;
		} else {
			$mediasetting['plugin'][] = $mediacode;
		}
	}

	$settings = [];
	empty($mediasetting['system']) && $mediasetting['system'][] = '-';
	$settings['mediasetting'] = $mediasetting;
	table_common_setting::t()->update_batch($settings);
	updatecache('setting');

	cpmsg('dzcode_edit_succeed', 'action=misc&operation=mediacode', 'succeed');
}

function get_mediacode_list() {
	global $_G;

	$mediadir = DISCUZ_ROOT.'./source/class/media';
	$parseflv = [];
	if(file_exists($mediadir)) {
		$mediadirhandle = dir($mediadir);
		while($entry = $mediadirhandle->read()) {
			if(!in_array($entry, ['.', '..']) && preg_match('/^media\_([\_\w]+)\.php$/', $entry, $entryr) && str_ends_with($entry, '.php') && is_file($mediadir.'/'.$entry)) {
				$c = 'media_'.$entryr[1];
				if(class_exists($c) && property_exists($c, 'checkurl')) {
					$parseflv[$entryr[1]] = [
						'checkurl' => $c::$checkurl,
						'version' => $c::$version ?? '',
						'name' => $c::$name ?? $c,
						'available' => !isset($_G['setting']['mediasetting']) ? 1 : in_array($entryr[1], $_G['setting']['mediasetting']['system'] ?? [])
					];
				}
			}
		}
	}
	if(!empty($_G['setting']['plugins']['available'])) {
		foreach($_G['setting']['plugins']['available'] as $pluginid) {
			$mediadir = DISCUZ_PLUGIN($pluginid).'/media';
			if(!is_dir($mediadir)) {
				continue;
			}
			$mediadirhandle = dir($mediadir);
			while($entry = $mediadirhandle->read()) {
				if(!in_array($entry, ['.', '..']) && preg_match('/^media\_([\_\w]+)\.php$/', $entry, $entryr) && str_ends_with($entry, '.php') && is_file($mediadir.'/'.$entry)) {
					$c = $pluginid.'\media_'.$entryr[1];
					if(class_exists($c) && property_exists($c, 'checkurl')) {
						$key = $pluginid.':'.$entryr[1];
						$parseflv[$key] = [
							'checkurl' => $c::$checkurl,
							'version' => $c::$version ?? '',
							'name' => $c::$name ?? $key,
							'available' => in_array($key, $_G['setting']['mediasetting']['plugin'] ?? [])
						];
					}
				}
			}
		}
	}

	return $parseflv;
}