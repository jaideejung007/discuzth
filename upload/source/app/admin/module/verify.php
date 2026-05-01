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

$anchor = in_array($_GET['anchor'], ['base', 'edit', 'verify', 'verify1', 'verify2', 'verify3', 'verify4', 'verify5', 'verify6', 'authstr', 'refusal', 'pass']) ? $_GET['anchor'] : 'base';
$current = [$anchor => 1];
$navmenu = [];

$file = childfile('verify/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;

function getverifyicon($iconkey = 'iconnew', $vid = 1, $extstr = 'verify_icon') {
	global $_G, $_FILES;

	if($_FILES[$iconkey]) {
		$data = ['extid' => "$vid"];
		$iconnew = upload_icon_banner($data, $_FILES[$iconkey], $extstr);
	} else {
		$iconnew = $_GET[''.$iconkey];
	}
	return $iconnew;
}

function delverifyicon($icon) {
	global $_G;

	$valueparse = parse_url($icon);
	if(!isset($valueparse['host']) && preg_match('/^'.preg_quote($_G['setting']['attachurl'].'common/', '/').'/', $icon)) {
		@unlink($icon);
	}
	return '';
}

