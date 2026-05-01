<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('MITFRAME_APP')) {
	exit('Access Denied');
}

const APPTYPEID = 0;

require './source/class/class_core.php';

$discuz = C::app();

$modarray = ['user', 'curforum', 'newthread'];

$cachelist = $slist = [];
$mod = '';
$discuz->cachelist = $cachelist;
$discuz->init();

if(in_array($discuz->var['mod'], $modarray) || (!empty($_G['setting']['search'][$discuz->var['mod']]['status']) && $_G['setting'][($discuz->var['mod'] == 'curforum' ? 'forum' : ($discuz->var['mod'] == 'user' ? 'friend' : $discuz->var['mod'])).'status'])) {
	$mod = $discuz->var['mod'];
} else {
	foreach($_G['setting']['search'] as $mod => $value) {
		if(!empty($value['status']) && $_G['setting'][($mod == 'curforum' ? 'forum' : ($mod == 'user' ? 'friend' : $mod)).'status']) {
			break;
		}
	}
}
if(empty($mod)) {
	showmessage('search_closed');
}
define('CURMODULE', $mod);


runhooks();

if(!$_G['setting'][($mod == 'curforum' ? 'forum' : ($mod == 'user' ? 'friend' : $mod)).'status']) {
	showmessage(($mod == 'curforum' ? 'forum' : ($mod == 'user' ? 'friend' : ($mod == 'group' ? 'group_module' : $mod))).'_status_off');
}

require_once libfile('function/search');


$navtitle = lang('core', 'title_search');

if($mod == 'curforum') {
	$mod = 'forum';
	$_GET['srchfid'] = [$_GET['srhfid']];
} elseif($mod == 'forum') {
	$_GET['srhfid'] = 0;
}

if(!empty($_GET['srchtxt']) && getglobal('setting/srchcensor')) {
	$_GET['srchtxt'] = censor($_GET['srchtxt']);
}

require_once appfile('module/'.$mod);

