<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('magics');

$lpp = empty($_GET['lpp']) ? 20 : $_GET['lpp'];
$start_limit = ($page - 1) * $lpp;

$mpurl = ADMINSCRIPT."?action=logs&operation=magic&lpp=$lpp";

$wheresql = '';
$wherearr = [];
$magicid = $action = 0;
if(in_array($_GET['opt'], ['1', '2', '3', '4', '5'])) {
	$wherearr[] = "ma.action='{$_GET['opt']}'";
	$action = $_GET['opt'];
	$mpurl .= '&opt='.$_GET['opt'];
}

if(!empty($_GET['magicid'])) {
	$wherearr[] = "ma.magicid='".intval($_GET['magicid'])."'";
	$magicid = intval($_GET['magicid']);
	$mpurl .= '&magicid='.$_GET['magicid'];
}

$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE '.implode(' AND ', $wherearr) : '';

$check1 = $check2 = [];
$check1[$_GET['magicid']] = 'selected="selected"';
$check2[$_GET['opt']] = 'selected="selected"';

$filters .= '<select onchange="window.location=\''.ADMINSCRIPT.'?action=logs&operation=magic&opt='.$_GET['opt'].'&lpp='.$lpp.'&opt='.$_GET['opt'].'&magicid=\'+this.options[this.selectedIndex].value"><option value="">'.$lang['magics_type'].'</option>';
foreach($_G['cache']['magics'] as $id => $magic) {
	$filters .= '<option value="'.$id.'" '.$check1[$id].'>'.$magic['name'].'</option>';
}
$filters .= '</select>';

$filters .= '<select onchange="window.location=\''.ADMINSCRIPT.'?action=logs&operation=magic&magicid='.$magicid.'&lpp='.$lpp.'&magicid='.$_GET['magicid'].'&opt=\'+this.options[this.selectedIndex].value"><option value="">'.$lang['all'].'</option>';
foreach(['1', '2', '3', '4', '5'] as $o) {
	$filters .= '<option value="'.$o.'" '.$check2[$o].'>'.$lang['logs_magic_operation_'.$o].'</option>';
}
$filters .= '</select>';

showtableheader('', 'fixpadding');

showtablerow('class="header"', ['class="td23"', 'class="td23"', 'class="td24"', 'class="td23"', 'class="td23"', 'class="td24"'], [
	cplang('username'),
	cplang('name'),
	cplang('time'),
	cplang('num'),
	cplang('price'),
	cplang('action')
]);

$num = table_common_magiclog::t()->count_by_magicid_action($magicid, $action);
if($num) {
	$multipage = multi($num, $lpp, $page, $mpurl, 0, 3);

	$luids = $targetuids = $logs = [];
	$mlogs = table_common_magiclog::t()->fetch_all_by_magicid_action($magicid, $action, $start_limit, $lpp);
	foreach($mlogs as $log) {
		$luids[$log['uid']] = $log['uid'];
	}
	$members = table_common_member::t()->fetch_all($luids);
	foreach($mlogs as $log) {
		$log['username'] = $members[$log['uid']]['username'];
		$log['name'] = $_G['cache']['magics'][$log['magicid']]['name'];
		$log['dateline'] = dgmdate($log['dateline'], 'Y-n-j H:i');
		if($log['action'] == 3) {
			$targetuids[] = $log['targetuid'];
		}
		$logs[] = $log;
	}

	if($targetuids) {
		$targetuids = table_common_member::t()->fetch_all_username_by_uid($targetuids);
	}

	foreach($logs as $log) {
		showtablerow('', ['class="bold"'], [
			"<a href=\"home.php?mod=space&username=".rawurlencode($log['username'])."\" target=\"_blank\">{$log['username']}",
			$log['name'],
			$log['dateline'],
			$log['amount'],
			$log['price'],
			$lang['logs_magic_operation_'.$log['action']].($log['action'] == 3 ? '<a href="home.php?mod=space&uid='.$log['targetuid'].'" target="_blank">'.$targetuids[$log['targetuid']].'</a>' : ''),
		]);
	}
}
	