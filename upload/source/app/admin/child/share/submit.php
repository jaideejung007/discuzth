<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$sids = authcode($sids, 'DECODE');
$sidsadd = $sids ? explode(',', $sids) : $_GET['delete'];
include_once libfile('function/delete');
$deletecount = count(deleteshares($sidsadd));
$cpmsg = cplang('share_succeed', ['deletecount' => $deletecount]);

echo '<script type="text/JavaScript">alert(\''.$cpmsg.'\');parent.$(\'shareforum\').searchsubmit.click();</script>';
	