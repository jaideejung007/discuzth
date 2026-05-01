<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('MITFRAME_APP')) {
	exit('Access Denied');
}

const APPTYPEID = 4;

require './source/class/class_core.php';
$discuz = C::app();

$cachelist = ['portalcategory', 'diytemplatenameportal'];
$discuz->cachelist = $cachelist;
$discuz->init();

require DISCUZ_ROOT.'./source/function/function_home.php';
require DISCUZ_ROOT.'./source/function/function_portal.php';

if(empty($_GET['mod']) || !in_array($_GET['mod'], ['list', 'view', 'comment', 'portalcp', 'topic', 'attachment', 'rss', 'block', 'mobilediy'])) $_GET['mod'] = 'index';


define('CURMODULE', $_GET['mod']);
runhooks();

if(!$_G['setting']['portalstatus'] && $_GET['mod'] != 'portalcp' && $_GET['mod'] != 'mobilediy') {
	showmessage('portal_status_off');
}

$navtitle = str_replace('{bbname}', $_G['setting']['bbname'], $_G['setting']['seotitle']['portal']);
$_G['disabledwidthauto'] = 1;

require_once appfile('module/'.$_GET['mod']);

