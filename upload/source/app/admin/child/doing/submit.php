<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$doids = authcode($doids, 'DECODE');
$doidsadd = $doids ? explode(',', $doids) : $_GET['delete'];
include_once libfile('function/delete');
$deletecount = count(deletedoings($doidsadd));
$cpmsg = cplang('doing_succeed', ['deletecount' => $deletecount]);

echo '<script type="text/JavaScript">alert(\''.$cpmsg.'\');parent.$(\'doingforum\').searchsubmit.click();</script>';
	