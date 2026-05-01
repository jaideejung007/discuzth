<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$ac = empty($_GET['ac']) ? '' : $_GET['ac'];
$acs = isset($_G['group']['allowvisit']) && $_G['group']['allowvisit'] ? ['swfupload', 'inputpwd', 'ajax', 'sendmail', 'emailcheck'] : ['swfupload', 'sendmail', 'emailcheck'];

if(empty($ac) || !in_array($ac, $acs)) {
	showmessage('enter_the_space', 'home.php?mod=space');
}

$theurl = 'home.php?mod=misc&ac='.$ac;
require_once childfile($ac, 'home/misc');;

