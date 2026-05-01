<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$operation = in_array($_GET['op'], ['order', 'pay']) ? trim($_GET['op']) : 'order';
$opactives = [$operation => ' class="a"'];

if($_G['setting']['ec_ratio']) {
	$is_enable_pay = payment::enable();
} else {
	$is_enable_pay = false;
}

if(!$_G['setting']['ec_ratio'] || !$is_enable_pay) {
	showmessage('action_closed', null);
}

include_once childfile('payment_'.$operation, 'home/spacecp');


