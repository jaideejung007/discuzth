<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($_GET['blockid'])) {
	cpmsg('undefined_action');
}

$blockid = $_GET['blockid'];

$block = table_common_editorblock::t()->fetch($blockid);
if(!$block) {
	cpmsg('editorblock_nonexistence', '', 'error');
}
$lpp = empty($_GET['lpp']) ? 10 : $_GET['lpp'];
if(!submitcheck('editorupdatesubmit')) {
	cpmsg('editorblock_update', 'action=editorblock&operation=update&blockid='.$blockid.'&editorupdatesubmit=yes&lpp='.$lpp.'&page='.$page, 'form');
} else {
	$class = $block['class'];
	$plugin = $block['plugin'];
	$identifier = $block['identifier'];
	if(!empty($plugin)) {
		$dir = DISCUZ_PLUGIN($plugin).'/editorblock';
		$filename = 'editorblock_'.$class.'.php';
	} else {
		$dir = DISCUZ_ROOT.'./source/class/editorblock';
		$filename = 'editorblock_'.$class.'.php';
	}
	if(!is_file($dir.'/'.$filename)) {
		cpmsg('editorblock_nonexistence', '', 'error');
	}

	@include_once $dir.'/'.$filename;
	$editorblockclass = substr($filename, 0, -4);
	if(class_exists($editorblockclass)) {
		$editorblock = new $editorblockclass();
		$script = substr($editorblockclass, 12);
		// $script = ($plugin ? $plugin.':' : '').$script;
		$editorblockdata = [
			'class' => $script,
			'name' => lang('editorblock/'.$script, $editorblock->name),
			'available' => $editorblock->available,
			'columns' => $editorblock->columns,
			'version' => $editorblock->version,
			'type' => $editorblock->type,
			'parser' => $editorblock->getParser(),
			'style' => $editorblock->getStyle(),
			'filename' => $editorblock->filename,
			'config' => $editorblock->getConfig(),
			'identifier' => $editorblock->identifier,
			'description' => $editorblock->description,
			'filemtime' => @filemtime($dir.'/'.$filename),
			'plugin' => $plugin,
			'copyright' => $editorblock->copyright
		];
	}
	table_common_editorblock::t()->update($block['blockid'], $editorblockdata);

	$_editorblock = table_common_editorblock::t()->fetch($block['blockid']);
	memory('set', 'editorblock_'.$_editorblock['class'], $_editorblock);

	if($editorblock->global_css) {
		$settings = [
			'editor_global_css' => ($_G['setting']['editor_global_css'] ?? '').$editorblock->getStyle(),
		];
		table_common_setting::t()->update_batch($settings);
		updatecache('setting');
	}

	cpmsg('editorblock_succeed', 'action=editorblock&operation=list&lpp='.$lpp.'&page='.$page, 'succeed');
}
	