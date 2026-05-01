<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('MITFRAME_APP')) {
	exit('Access Denied');
}

const APPTYPEID = 1;

if(!empty($_GET['mod']) && ($_GET['mod'] == 'misc' || $_GET['mod'] == 'invite')) {
	define('ALLOWGUEST', 1);
}

require_once './source/class/class_core.php';
require_once './source/function/function_home.php';

$discuz = C::app();

$cachelist = ['magic', 'usergroups', 'diytemplatenamehome'];
$discuz->cachelist = $cachelist;
$discuz->init();

$space = [];

$mod = getgpc('mod');
if(!in_array($mod, ['space', 'spacecp', 'misc', 'magic', 'editor', 'invite', 'task', 'medal', 'rss', 'follow'])) {
	$mod = 'space';
	$_GET['do'] = $_G['setting']['feedstatus'] ? 'home' : 'profile';
}

if($mod == 'space' && ((empty($_GET['do']) || $_GET['do'] == 'index') && ($_G['inajax']))) {
	$_GET['do'] = 'profile';
}
$curmod = !empty($_G['setting']['followstatus']) && $_GET['do'] == 'follow' ? 'follow' : $mod;
define('CURMODULE', $curmod);
runhooks(getgpc('do') == 'profile' && $_G['inajax'] ? 'card' : getgpc('do'));

require_once appfile('module/'.$mod);


