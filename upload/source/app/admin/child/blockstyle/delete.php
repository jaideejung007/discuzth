<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$_GET['styleid'] = intval($_GET['styleid']);
$thestyle = table_common_block_style::t()->fetch($_GET['styleid']);
if(empty($thestyle)) {
	cpmsg('blockstyle_not_found', 'action=blockstyle', 'error');
}
$styles = [];
if(($styles = table_common_block_style::t()->fetch_all_by_blockclass($thestyle['blockclass']))) {
	unset($styles[$_GET['styleid']]);
}
if(empty($styles)) {
	cpmsg('blockstyle_should_be_kept', 'action=blockstyle', 'error');
}

if(submitcheck('deletesubmit')) {
	$_POST['moveto'] = intval($_POST['moveto']);
	$newstyle = table_common_block_style::t()->fetch($_POST['moveto']);
	if($newstyle['blockclass'] != $thestyle['blockclass']) {
		cpmsg('blockstyle_blockclass_not_match', 'action=blockstyle', 'error');
	}
	table_common_block::t()->update_by_styleid($styleid, ['styleid' => $_POST['moveto']]);
	table_common_block_style::t()->delete($_GET['styleid']);
	require_once libfile('function/block');
	blockclass_cache();
	cpmsg('blockstyle_delete_succeed', 'action=blockstyle', 'succeed');
}

if(table_common_block::t()->fetch_by_styleid($_GET['styleid'])) {
	showtips('blockstyle_delete_tips');
	showformheader('blockstyle&operation=delete&styleid='.$_GET['styleid']);
	showtableheader();
	$movetoselect = '<select name="moveto">';
	foreach($styles as $key => $value) {
		$movetoselect .= "<option value=\"$key\">{$value['name']}</option>";
	}
	$movetoselect .= '</select>';
	showsetting('blockstyle_moveto', '', '', $movetoselect);
	showsubmit('deletesubmit');
	showtablefooter();
	showformfooter();

} else {
	table_common_block_style::t()->delete($_GET['styleid']);
	require_once libfile('function/block');
	blockclass_cache();
	cpmsg('blockstyle_delete_succeed', 'action=blockstyle', 'succeed');
}
	