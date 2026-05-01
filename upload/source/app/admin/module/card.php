<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
if($operation != 'export') {
	cpheader();
}

$operation = $_GET['operation'] ? $_GET['operation'] : 'set';
$card_setting = $_G['setting']['card'];

if($operation == 'set') {
	$nav = 'config';
	$submenu['set'] = 1;
} elseif($operation == 'manage') {
	$nav = 'admin';
	$submenu['manage'] = 1;
} elseif($operation == 'type') {
	$nav = 'nav_card_type';
	$submenu['type'] = 1;
} elseif($operation == 'make') {
	$nav = 'nav_card_make';
	$submenu['make'] = 1;
} elseif($operation == 'log') {
	$nav = 'nav_card_log';
} else {
	$nav = '';
}
if($nav != '') {
	if(!submitcheck('cardsubmit', 1) || $operation == 'manage' || $operation == 'type') {
		shownav('extended', 'nav_card', $nav);
		showsubmenu('nav_card', [
			['config', 'card', $submenu['set']],
			['admin', 'card&operation=manage', $submenu['manage']],
			['nav_card_type', 'card&operation=type', $submenu['type']],
			['nav_card_make', 'card&operation=make', $submenu['make']],
			[['menu' => 'nav_card_log', 'submenu' => [
				['nav_card_log_add', 'card&operation=log&do=add', $_GET['do'] == 'add'],
				['nav_card_log_del', 'card&operation=log&do=del', $_GET['do'] == 'del'],
				['nav_card_log_cron', 'card&operation=log&do=cron', $_GET['do'] == 'cron']
			]], in_array($_GET['do'], ['add', 'del', 'cron'])]
		]);
	}
}

$file = childfile('card/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}
require_once $file;

function cardsql() {


	$_GET = daddslashes($_GET);

	$_GET['srch_id'] = trim($_GET['srch_id']);

	$_GET['srch_price_max'] = intval($_GET['srch_price_max']);
	$_GET['srch_price_min'] = intval($_GET['srch_price_min']);

	$_GET['srch_useddateline'] = trim($_GET['srch_useddateline']);
	$_GET['srch_username'] = trim($_GET['srch_username']);
	$_GET['srch_extcredits'] = trim($_GET['srch_extcredits']);
	$_GET['srch_extcreditsval'] = intval($_GET['srch_extcreditsval']) > 0 ? intval($_GET['srch_extcreditsval']) : '';
	$_GET['srch_username'] = trim($_GET['srch_username']);

	$_GET['srch_useddateline_start'] = trim($_GET['srch_useddateline_start']);
	$_GET['srch_useddateline_end'] = trim($_GET['srch_useddateline_end']);

	$sqladd = '';
	if($_GET['srch_id']) {
		$sqladd .= " AND id LIKE '%{$_GET['srch_id']}%' ";
	}
	if($_GET['srch_card_type'] != '') {
		$sqladd .= " AND typeid = '{$_GET['srch_card_type']}'";
	}
	if($_GET['srch_price_min'] && !$_GET['srch_price_max']) {
		$sqladd .= " AND price = '{$_GET['srch_price_min']}'";
	} elseif($_GET['srch_price_max'] && !$_GET['srch_price_min']) {
		$sqladd .= " AND price = '{$_GET['srch_price_max']}'";
	} elseif($_GET['srch_price_min'] && $_GET['srch_price_max']) {
		$sqladd .= " AND price between '{$_GET['srch_price_min']}' AND '{$_GET['srch_price_max']}'";
	}

	if($_GET['srch_extcredits']) {
		$sqladd .= " AND extcreditskey = '{$_GET['srch_extcredits']}'";
	}
	if($_GET['srch_extcreditsval']) {
		$sqladd .= " AND extcreditsval = '{$_GET['srch_extcreditsval']}'";
	}

	if($_GET['srch_username']) {
		$uid = ($uid = table_common_member::t()->fetch_uid_by_username($_GET['srch_username'])) ? $uid : table_common_member_archive::t()->fetch_uid_by_username($_GET['srch_username']);
		$sqladd .= " AND uid = '{$uid}'";
	}
	if($_GET['srch_card_status']) {
		$sqladd .= " AND status = '{$_GET['srch_card_status']}'";
	}
	if($_GET['srch_useddateline_start'] || $_GET['srch_useddateline_end']) {
		if($_GET['srch_useddateline_start']) {
			list($y, $m, $d) = explode('-', $_GET['srch_useddateline_start']);
			$sqladd .= " AND useddateline >= '".mktime('0', '0', '0', $m, $d, $y)."' ";
		}
		if($_GET['srch_useddateline_end']) {
			list($y, $m, $d) = explode('-', $_GET['srch_useddateline_end']);
			$sqladd .= " AND useddateline <= '".mktime('23', '59', '59', $m, $d, $y)."' AND useddateline <> 0 ";
		}
	}
	return $sqladd ? ' 1 '.$sqladd : '';
}

