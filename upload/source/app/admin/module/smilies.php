<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$imgextarray = ['jpg', 'gif', 'png'];
$id = $_GET['id'];
if($operation == 'export' && $id) {
	$smileyarray = table_forum_imagetype::t()->fetch($id);
	if(!$smileyarray) {
		cpheader();
		cpmsg('smilies_type_nonexistence', '', 'error');
	}

	$smileyarray['smilies'] = [];
	foreach(table_common_smiley::t()->fetch_all_by_typeid_type($id, 'smiley') as $smiley) {
		$smileyarray['smilies'][] = $smiley;
	}

	$smileyarray['version'] = strip_tags($_G['setting']['version']);
	exportdata('Discuz! Smilies', $smileyarray['name'], $smileyarray);
}

cpheader();

$operation = $operation ? $operation : 'list';

$file = childfile('smilies/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}
require_once $file;

function addsmilies($typeid, $smilies = []) {
	if(is_array($smilies)) {
		$ids = [];
		foreach($smilies as $smiley) {
			if($smiley['available']) {
				$data = [
					'type' => 'smiley',
					'displayorder' => $smiley['displayorder'],
					'typeid' => $typeid,
					'code' => '',
					'url' => $smiley['url'],
				];
				$ids[] = table_common_smiley::t()->insert($data, true);
			}
		}
		if($ids) {
			table_common_smiley::t()->update_code_by_id($ids);
		}
	}
}

function update_smiles($smdir, $id, &$imgextarray) {
	$num = 0;
	$smilies = $imgfilter = [];
	foreach(table_common_smiley::t()->fetch_all_by_typeid_type($id, 'smiley') as $img) {
		$imgfilter[] = $img['url'];
	}
	$smiliesdir = dir($smdir);
	while($entry = $smiliesdir->read()) {
		if(in_array(strtolower(fileext($entry)), $imgextarray) && !in_array($entry, $imgfilter) && preg_match('/^[\w\-\.\[\]\(\)\<\> &]+$/', substr($entry, 0, strrpos($entry, '.'))) && strlen($entry) < 30 && is_file($smdir.'/'.$entry)) {
			$smilies[] = ['available' => 1, 'displayorder' => 0, 'url' => $entry];
			$num++;
		}
	}
	$smiliesdir->close();

	return ['smilies' => $smilies, 'num' => $num];
}

