<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cloudaddons_check();
shownav('cloudaddons');
$extra = '';
if(empty($_GET['frame'])) {
	parse_str($_SERVER['QUERY_STRING'], $query);
	$query['frame'] = 'no';
	$query_sting_tmp = http_build_query($query);
	$url = ADMINSCRIPT.'?'.$query_sting_tmp;
	echo '<script type="text/javascript">top.location.href=\''.$url.'\';</script>';
} else {
	if(!empty($operation)) {
		$extra .= '&view='.rawurlencode($operation);
	} elseif(!empty($_GET['id'])) {
		$extra .= '&mod=app&ac=item&id='.rawurlencode($_GET['id']);
	}
	if(!empty($_GET['from'])) {
		$extra .= '&from='.rawurlencode($_GET['from']);
	}
	if(!empty($_GET['extra'])) {
		$extra .= '&'.addslashes($_GET['extra']);
	}
	$url = cloudaddons_url($extra);
	echo '<script type="text/javascript">location.href=\''.$url.'\';</script>';
}
	