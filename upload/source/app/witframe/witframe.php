<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('MITFRAME_APP')) {
	exit('Access Denied');
}

const APPTYPEID = 126;
const NOT_IN_MOBILE_API = 1;

require './source/class/class_core.php';

$discuz = C::app();
$discuz->init();

if(empty($_GET['path'])) {
	showmessage('plugin_nonexistence');
}

$url = witframe_plugin::getApiUrl('page', $_GET['path'], true);
if(empty($url)) {
	showmessage('plugin_nonexistence');
}

require_once template('common/witframe');

