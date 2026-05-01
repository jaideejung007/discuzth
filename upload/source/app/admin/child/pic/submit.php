<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$picids = authcode($picids, 'DECODE');
$picidsadd = $picids ? explode(',', $picids) : $_GET['delete'];
include_once libfile('function/delete');
$deletecount = count(deletepics($picidsadd));
$cpmsg = cplang('pic_succeed', ['deletecount' => $deletecount]);

echo '<script type="text/JavaScript">alert(\''.$cpmsg.'\');parent.$(\'picforum\').searchsubmit.click();</script>';
	