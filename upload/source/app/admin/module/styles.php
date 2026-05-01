<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!empty($_GET['preview'])) {
	loadcache('style_'.$_GET['styleid']);
	$_G['style'] = $_G['cache']['style_'.$_GET['styleid']];
	include template('common/preview', $_G['style']['templateid'], $_G['style']['tpldir']);
	exit;
}

require_once libfile('function/cloudaddons');

$scrolltop = $_GET['scrolltop'];
$anchor = $_GET['anchor'];
$namenew = $_GET['namenew'];
$defaultnew = $_GET['defaultnew'];
$newname = $_GET['newname'];
$id = $_GET['id'];
$isplugindeveloper = isset($_G['config']['plugindeveloper']) && $_G['config']['plugindeveloper'] > 0;

$operation = empty($operation) ? 'admin' : $operation;

if($operation == 'export' && $id) {
	require_once childfile('styles/export');
}

cpheader();

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
	'menucurbgcolor' => [0],
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

$file = childfile('styles/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}
require_once $file;

