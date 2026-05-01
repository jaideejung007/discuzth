<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showsubmenu('setting_sec_seccode');

$setting['seccodedata'] = dunserialize($setting['seccodedata']);
$setting['secqaa'] = dunserialize($setting['secqaa']);

if(submitcheck('settingsubmit')) {

	if(isset($settingnew['seccodedata'])) {
		$settingnew['seccodedata']['width'] = intval($settingnew['seccodedata']['width']);
		$settingnew['seccodedata']['height'] = intval($settingnew['seccodedata']['height']);
		if($settingnew['seccodedata']['type'] != 3) {
			$settingnew['seccodedata']['width'] = $settingnew['seccodedata']['width'] < 100 ? 100 : ($settingnew['seccodedata']['width'] > 200 ? 200 : $settingnew['seccodedata']['width']);
			$settingnew['seccodedata']['height'] = $settingnew['seccodedata']['height'] < 30 ? 30 : ($settingnew['seccodedata']['height'] > 80 ? 80 : $settingnew['seccodedata']['height']);
		} else {
			$settingnew['seccodedata']['width'] = 85;
			$settingnew['seccodedata']['height'] = 25;
		}
		$seccoderoot = '';
		if($settingnew['seccodedata']['type'] == 0 || $settingnew['seccodedata']['type'] == 2) {
			$seccoderoot = 'source/data/seccode/font/en/';
		} elseif($settingnew['seccodedata']['type'] == 1) {
			$seccoderoot = 'source/data/seccode/font/ch/';
		}
		if($seccoderoot) {
			$dirs = opendir($seccoderoot);
			$seccodettf = [];
			while($entry = readdir($dirs)) {
				if($entry != '.' && $entry != '..' && in_array(strtolower(fileext($entry)), ['ttf', 'ttc'])) {
					$seccodettf[] = $entry;
				}
			}
			if(!$seccodettf) {
				cpmsg('setting_seccode_ttf_lost', '', 'error', ['path' => $seccoderoot]);
			}
		}
	}

	if(!is_numeric($settingnew['seccodedata']['type']) && !preg_match('/^[\w\_]+:[\w\_]+$/', $settingnew['seccodedata']['type'])) {
		$settingnew['seccodedata']['type'] = 0;
	}
	$settingnew['seccodestatus'] = !empty($setting['secqaa']['statuses']) ? 1 : 0;

} else {

	showformheader('setting&edit=yes');
	showhiddenfields(['operation' => $operation]);

	$seccodecheck = 1;
	$sechash = 'S'.$_G['sid'];
	$seccheckhtml = "<span id=\"seccode_c$sechash\"></span><script type=\"text/javascript\">updateseccode('c$sechash', '<br /><sec> <sec> <sec>', 'admin');</script>";

	$checksc = [];

	$seccodetypearray = [
		[0, cplang('setting_sec_seccode_type_image'), ['seccodeimageext' => '', 'seccodeimagewh' => '']],
		[1, cplang('setting_sec_seccode_type_chnfont'), ['seccodeimageext' => '', 'seccodeimagewh' => '']],
		[99, cplang('setting_sec_seccode_type_bitmap'), ['seccodeimageext' => 'none', 'seccodeimagewh' => 'none']],
	];

	$seccodetypearray = array_merge($seccodetypearray, getseccodes($seccodesettings));

	/*search={"setting_seccheck":"action=setting&operation=sec","setting_sec_seccode":"action=setting&operation=sec&anchor=seccode"}*/
	showtips('setting_sec_code_tips', 'seccode_tips');

	showtableheader();
	showtitle('setting_sec_seccode_type_setting');
	showsetting('setting_sec_seccode_type', ['settingnew[seccodedata][type]', $seccodetypearray], $setting['seccodedata']['type'], 'mradio', '', 0, cplang('setting_sec_seccode_type_comment').$seccheckhtml);
	showtagheader('tbody', 'seccodeimagewh', is_numeric($setting['seccodedata']['type']) && $setting['seccodedata']['type'] != 3 && $setting['seccodedata']['type'] != 99, 'sub');
	showsetting('setting_sec_seccode_width', 'settingnew[seccodedata][width]', $setting['seccodedata']['width'], 'text');
	showsetting('setting_sec_seccode_height', 'settingnew[seccodedata][height]', $setting['seccodedata']['height'], 'text');
	showtagfooter('tbody');
	showtagheader('tbody', 'seccodeimageext', is_numeric($setting['seccodedata']['type']) && $setting['seccodedata']['type'] != 2 && $setting['seccodedata']['type'] != 3 && $setting['seccodedata']['type'] != 99, 'sub');
	showsetting('setting_sec_seccode_shuffer_order', 'settingnew[seccodedata][shuffer_order]', $setting['seccodedata']['shuffer_order'], 'radio');
	showsetting('setting_sec_seccode_scatter', 'settingnew[seccodedata][scatter]', $setting['seccodedata']['scatter'], 'text');
	showsetting('setting_sec_seccode_background', 'settingnew[seccodedata][background]', $setting['seccodedata']['background'], 'radio');
	showsetting('setting_sec_seccode_adulterate', 'settingnew[seccodedata][adulterate]', $setting['seccodedata']['adulterate'], 'radio');
	showsetting('setting_sec_seccode_ttf', 'settingnew[seccodedata][ttf]', $setting['seccodedata']['ttf'], 'radio', !function_exists('imagettftext'));
	showsetting('setting_sec_seccode_angle', 'settingnew[seccodedata][angle]', $setting['seccodedata']['angle'], 'radio');
	showsetting('setting_sec_seccode_warping', 'settingnew[seccodedata][warping]', $setting['seccodedata']['warping'], 'radio');
	showsetting('setting_sec_seccode_color', 'settingnew[seccodedata][color]', $setting['seccodedata']['color'], 'radio');
	showsetting('setting_sec_seccode_size', 'settingnew[seccodedata][size]', $setting['seccodedata']['size'], 'radio');
	showsetting('setting_sec_seccode_shadow', 'settingnew[seccodedata][shadow]', $setting['seccodedata']['shadow'], 'radio');
	showsetting('setting_sec_seccode_animator', 'settingnew[seccodedata][animator]', $setting['seccodedata']['animator'], 'radio', !function_exists('imagegif'));
	showtagfooter('tbody');

	showsubmit('settingsubmit');
	showtablefooter();
	/*search*/

}