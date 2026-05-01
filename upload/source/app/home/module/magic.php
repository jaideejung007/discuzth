<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['uid']) {
	showmessage('not_loggedin', NULL, [], ['login' => 1]);
}

if(!$_G['setting']['creditstransextra'][3]) {
	showmessage('credits_transaction_disabled');
} elseif(!$_G['setting']['magicstatus']) {
	showmessage('magics_close');
}

require_once libfile('function/magic');
loadcache('magics');

$_G['mnid'] = 'mn_common';
$magiclist = [];
$_G['tpp'] = 16;
$page = max(1, intval($_GET['page']));
$action = $_GET['action'];
$operation = $_GET['operation'];
$start_limit = ($page - 1) * $_G['tpp'];
$_GET['idtype'] = dhtmlspecialchars($_GET['idtype']);

$comma = $typeadd = $filteradd = $forumperm = $targetgroupperm = '';
$magicarray = is_array($_G['cache']['magics']) ? $_G['cache']['magics'] : [];

if(!$_G['uid'] && ($operation || $action == 'mybox')) {
	showmessage('not_loggedin', NULL, [], ['login' => 1]);
}

if(!$_G['group']['allowmagics']) {
	showmessage('magics_nopermission');
}

$totalweight = getmagicweight($_G['uid'], $magicarray);
$allowweight = $_G['group']['maxmagicsweight'] - $totalweight;
$location = 0;

if(empty($action) && !empty($_GET['mid'])) {
	$_GET['magicid'] = table_common_member_magic::t()->fetch_magicid_by_identifier($_G['uid'], $_GET['mid']);
	if(!$_GET['magicid']) {
		$magic = table_common_magic::t()->fetch_by_identifier($_GET['mid']);
		if(!$magic['price'] && $magic['num']) {
			getmagic($magic['magicid'], 1, $magic['weight'], $totalweight, $_G['uid'], $_G['group']['maxmagicsweight']);
			updatemagiclog($magic['magicid'], '1', 1, $magic['price'].'|'.$magic['credit'], $_G['uid']);

			table_common_magic::t()->update_salevolume($magic['magicid'], 1);
			updatemembercount($_G['uid'], [$magic['credit'] => -0], true, 'BMC', $magic['magicid']);
			$_GET['magicid'] = $magic['magicid'];
		}
	}
	if($_GET['magicid']) {
		$action = 'mybox';
		$operation = 'use';
	} else {
		$action = 'shop';
		$operation = 'buy';
		$location = 1;
	}
}

$action = empty($action) ? 'shop' : $action;
$actives[$action] = ' class="a"';

if($action == 'shop') {

	require_once childfile('shop');

} elseif($action == 'mybox') {

	require_once childfile('mybox');

} elseif($action == 'log') {

	require_once childfile('log');

} else {
	showmessage('undefined_action');
}

include template('home/space_magic');

