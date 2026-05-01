<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

$operation = $operation ? $operation : 'list';

$file = childfile('usergroups/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;

function array_flip_keys($arr) {
	$arr2 = [];
	$arr = is_array($arr) ? $arr : [];
	$arrkeys = is_array($arr) ? array_keys($arr) : [];
	$first = current(array_slice($arr, 0, 1));
	if($first) {
		foreach($first as $k => $v) {
			foreach($arrkeys as $key) {
				$arr2[$k][$key] = $arr[$key][$k];
			}
		}
	}
	return $arr2;
}

function deletegroupcache($groupidarray) {
	global $_G;
	if(!empty($groupidarray) && is_array($groupidarray)) {
		$cachenames = [];
		foreach($groupidarray as $id) {
			if(($id = dintval($id))) {
				$cachenames['usergroup_'.$id] = 'usergroup_'.$id;
				$cachenames['admingroup_'.$id] = 'admingroup_'.$id;
				unset($_G['setting']['upgroup_name'][$id]);
			}
		}
		if(!empty($cachenames)) {
			table_common_syscache::t()->delete_syscache($cachenames);
		}
		$settings = [
			'upgroup_name' => $_G['setting']['upgroup_name'],
		];
		table_common_setting::t()->update_batch($settings);
	}
}

function iconimg($icon) {
	global $_G;
	if(empty($icon)) {
		$s = '';
	} else {
		if(preg_match('/^https?:\/\//is', $icon)) {
			$s = '<img src="'.$icon.'" alt="" class="vm" style="width:auto;height:20px" />';
		} else {
			$s = '<img src="'.$_G['setting']['attachurl'].'common/'.$icon.'" alt="" class="vm" style="width:auto;height:20px" />';
		}
	}
	return $s;
}

