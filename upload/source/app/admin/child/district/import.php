<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('importsubmit')) {

	/*search={"district":"action=district&operation=import"}*/
	showtips('district_import_tips');
	/*search*/

	showformheader('district&operation=import', 'enctype');
	showtableheader('');
	showsetting('import_type', ['importtype', [
		cplang('restful_import_local'),
		['file', cplang('import_type_file'), ['importfile' => '', 'importtxt' => 'none']],
		['txt', cplang('import_type_txt'), ['importfile' => 'none', 'importtxt' => '']]
	]], 'file', 'mradio');
	showtagheader('tbody', 'importfile', TRUE);
	showsetting('import_file', 'importfile', '', 'file');
	showtagfooter('tbody');
	showtagheader('tbody', 'importtxt');
	showsetting('import_txt', 'importtxt', '', 'textarea');
	showtagfooter('tbody');
	showsubmit('importsubmit');
	showtablefooter();
	showformfooter();

} else {

	if($_GET['importtype'] == 'file') {
		$data = @implode('', file($_FILES['importfile']['tmp_name']));
		@unlink($_FILES['importfile']['tmp_name']);
	} elseif(!empty($_GET['importtxt'])) {
		$data = $_GET['importtxt'];
	} else {
		cpmsg('import_data_typeinvalid', '', 'error');
	}
	$data = parseHierarchy($data);
	$displayorder = 1;
	foreach($data as $row) {
		insertWithChilds($row);
	}
	cpmsg('setting_update_succeed', 'action=district', 'succeed');

}

function insertWithChilds($data, $upid = 0) {
	$id = table_common_district::t()->insert([
		'name' => $data['name'],
		'level' => $data['level'],
		'upid' => $upid, 'displayorder' => $GLOBALS['displayorder']++
	], true);
	foreach($data['childs'] as $child) {
		insertWithChilds($child, $id);
	}
}

function parseHierarchy($str) {
	$lines = explode("\n", $str);
	$lines = array_filter(array_map(function($line) {
		return trim($line) !== '' ? $line : null;
	}, $lines));

	if (empty($lines)) {
		return [];
	}

	$parsedLines = [];
	foreach ($lines as $line) {
		$level = 0;
		$content = $line;
		while (str_starts_with($content, "\t")) {
			$level++;
			$content = substr($content, 1);
		}
		$parsedLines[] = [
			'level' => $level,
			'content' => $content
		];
	}

	$result = [];
	$stack = [];

	foreach ($parsedLines as $item) {
		$currentLevel = $item['level'];
		$currentContent = $item['content'];
		$node = [
			'name' => $currentContent,
			'level' => $currentLevel,
			'childs' => []
		];
		while (count($stack) > $currentLevel) {
			array_pop($stack);
		}
		if (empty($stack)) {
			$result[] = &$node;
		} else {
			$lastIndex = count($stack) - 1;
			$stack[$lastIndex]['childs'][] = &$node;
		}
		$stack[] = &$node;
		unset($node);
	}

	return $result;
}