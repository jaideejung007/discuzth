<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$predefinedvars = ['available' => [], 'boardimg' => [], 'searchimg' => [], 'touchimg' => [], 'imgdir' => [], 'styleimgdir' => [], 'stypeid' => [],
	'headerbgcolor' => [0, $lang['styles_edit_type_bg']],
	'bgcolor' => [0],
	'sidebgcolor' => [0, '', '#FFF sidebg.gif repeat-y 100% 0'],
	'titlebgcolor' => [0],

	'headerborder' => [1, $lang['styles_edit_type_header'], '1px'],
	'headertext' => [0],
	'footertext' => [0],

	'font' => [1, $lang['styles_edit_type_font']],
	'fontsize' => [1],
	'threadtitlefont' => [1, $lang['styles_edit_type_thread_title']],
	'threadtitlefontsize' => [1],
	'smfont' => [1],
	'smfontsize' => [1],
	'tabletext' => [0],
	'midtext' => [0],
	'lighttext' => [0],

	'link' => [0, $lang['styles_edit_type_url']],
	'highlightlink' => [0],
	'lightlink' => [0],

	'wrapbg' => [0],
	'wrapbordercolor' => [0],

	'msgfontsize' => [1, $lang['styles_edit_type_post'], '14px'],
	'contentwidth' => [1],
	'contentseparate' => [0],

	'menubgcolor' => [0, $lang['styles_edit_type_menu']],
	'menutext' => [0],
	'menuhoverbgcolor' => [0],
	'menuhovertext' => [0],

	'inputborder' => [0, $lang['styles_edit_type_input']],
	'inputborderdarkcolor' => [0],
	'inputbg' => [0, '', '#FFF'],

	'dropmenuborder' => [0, $lang['styles_edit_type_dropmenu']],
	'dropmenubgcolor' => [0],

	'floatbgcolor' => [0, $lang['styles_edit_type_float']],
	'floatmaskbgcolor' => [0],

	'commonborder' => [0, $lang['styles_edit_type_other']],
	'commonbg' => [0],
	'specialborder' => [0],
	'specialbg' => [0],
	'noticetext' => [0],
];
if(!submitcheck('addsubmit')) {
	shownav('template', 'templates_add');
	showsubmenu('styles_admin', [
		['templates_add', 'templates&operation=add', 1],
		['nav_templates', 'templates&operation=admin', 0],
		['cloudaddons_style_link', 'cloudaddons&frame=no&operation=templates&from=more', 0, 1],
	]);
	showtips('templates_add_tips');

	showformheader('templates&operation=add', '', 'configform');
	showtableheader();
	showsetting('templates_edit_name', 'namenew', '', 'text');
	showsetting('templates_edit_copyright', 'copyrightnew', '', 'text');
	showsetting('templates_edit_identifier', 'identifiernew', '', 'text');

	$styleselect = [];
	$styleselect[] = [0, $lang['templates_empty']];
	foreach(table_common_style::t()->fetch_all_data(true) as $value) {
		$styleselect[] = [$value['styleid'], $value['name']];
	}
	showsetting('templates_edit_style', ['styleidnew', $styleselect], '', 'select');
	showsubmit('addsubmit');
	showtablefooter();
	showformfooter();
} else {
	$namenew = dhtmlspecialchars(trim($_GET['namenew']));
	$identifiernew = trim($_GET['identifiernew']);
	$copyrightnew = dhtmlspecialchars($_GET['copyrightnew']);
	$styleidnew = dintval($_GET['styleidnew']);

	if(!$namenew) {
		cpmsg('templates_edit_name_invalid', '', 'error');
	}

	if(!ispluginkey($identifiernew)) {
		cpmsg('templates_edit_identifier_invalid', '', 'error');
	}

	$templateid = table_common_template::t()->insert(['name' => $namenew, 'directory' => './template/'.$identifiernew, 'copyright' => $copyrightnew], true);
	$styleid = table_common_style::t()->insert(['name' => $namenew, 'templateid' => $templateid], true);
	if($styleidnew) {
		foreach(table_common_stylevar::t()->fetch_all_by_styleid($styleidnew) as $stylevar) {
			table_common_stylevar::t()->insert(['styleid' => $styleid, 'variable' => $stylevar['variable'], 'substitute' => $stylevar['substitute']]);
		}
	} else {
		foreach(array_keys($predefinedvars) as $variable) {
			$substitute = $predefinedvars[$variable][2] ?? '';
			table_common_stylevar::t()->insert(['styleid' => $styleid, 'variable' => $variable, 'substitute' => $substitute]);
		}
	}
	dmkdir(DISCUZ_TEMPLATE($identifiernew).'/');
	updatecache(['setting', 'styles']);
	loadcache('style_default', true);
	updatecache('updatediytemplate');
	cpmsg('templates_add_succeed', 'action=styles', 'succeed');
}
	