<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_G['inajax'] && $_GET['showcredit']) {
	include template('common/extcredits');
	exit;
}

include_once libfile('function/credit');

$perpage = 20;
$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
if($page < 1) $page = 1;
$start = ($page - 1) * $perpage;
ckstart($start, $perpage);

checkusergroup();

$operation = in_array(getgpc('op'), ['base', 'buy', 'transfer', 'exchange', 'log', 'rule']) ? trim($_GET['op']) : 'base';
$opactives = [$operation => ' class="a"'];
if(in_array($operation, ['base', 'buy', 'transfer', 'exchange', 'rule'])) {
	$operation = 'base';
}

if($_G['setting']['ec_ratio']) {
	$is_enable_pay = payment::enable();
} else {
	$is_enable_pay = false;
}

include_once childfile('credit_'.$operation, 'home/spacecp');

