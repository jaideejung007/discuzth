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

$ops = ['article', 'template', 'block'];
$operation = in_array($operation, $ops, true) ? $operation : 'article';
$opdata = [];
foreach($ops as $op) {
	$opdata[] = ['portalpermission_'.$op, 'portalpermission&operation='.$op, $op == $operation];
}

$line = '&minus;';
$right = '&radic;';
$adminscript = $mpurl = ADMINSCRIPT.'?action=portalpermission&operation='.$operation;

$permissions = $members = $uids = [];

shownav('portal', 'portalpermission');
showsubmenu('portalpermission', $opdata);

$_GET['ordersc'] = in_array($_GET['ordersc'], ['desc', 'asc'], true) ? $_GET['ordersc'] : 'desc';
$_GET['uid'] = dintval($_GET['uid']);
if(($_GET['uid'] = $_GET['uid'] ? $_GET['uid'] : '')) {
	$mpurl .= '&uid='.$_GET['uid'];
} elseif($_GET['username']) {
	$uids = array_keys(table_common_member::t()->fetch_all_by_like_username($_GET['username']));
	$uids = $uids ? $uids : [0];
	$mpurl .= '&username='.dhtmlspecialchars($_GET['username']);
}
if($_GET['inherited']) {
	$inherited = ' checked';
	$mpurl .= '&inherited=1';
}
$ordersc = [$_GET['ordersc'] => ' selected'];
$perpage = in_array($_GET['perpage'], [10, 20, 50, 100]) ? $_GET['perpage'] : 20;
$start = ($page - 1) * $perpage;
$perpages = [$perpage => ' selected'];
$searchlang = [];
$keys = ['search', 'resultsort', 'orderdesc', 'orderasc', 'perpage_10', 'perpage_20', 'perpage_50', 'perpage_100', 'likesupport',
	'uid', 'username', 'portalpermission_no_inherited'];
foreach($keys as $key) {
	$searchlang[$key] = cplang($key);
}

require_once childfile('portalpermission/search_form');

showformheader('portalpermission&operation='.$operation);
showtableheader('portalpermission');

if($operation == 'article') {
	require_once childfile('portalpermission/article');
} elseif($operation == 'template') {
	require_once childfile('portalpermission/template');
} elseif($operation == 'block') {
	require_once childfile('portalpermission/block');
}

showtablefooter();
showformfooter();

