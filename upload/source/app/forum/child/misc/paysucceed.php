<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$orderid = trim($_GET['orderid']);
$url = !empty($orderid) ? 'forum.php?mod=trade&orderid='.$orderid : 'home.php?mod=spacecp&ac=credit';
showmessage('payonline_succeed', $url);
	