<?php
/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('MITFRAME_APP')) {
	exit('Access Denied');
}

const APPTYPEID = 3;

require './source/class/class_core.php';

$discuz = C::app();

$cachelist = ['grouptype', 'groupindex', 'diytemplatenamegroup'];
$discuz->cachelist = $cachelist;
$discuz->init();

$_G['disabledwidthauto'] = 0;

$modarray = ['index', 'my', 'attentiongroup'];
$mod = !in_array($_G['mod'], $modarray) ? 'index' : $_G['mod'];

define('CURMODULE', $mod);

runhooks();

if(!$_G['setting']['groupstatus']) {
	showmessage('group_module_status_off');
}

$navtitle = str_replace('{bbname}', $_G['setting']['bbname'], $_G['setting']['seotitle']['group']);

require_once appfile('module/'.$mod);
