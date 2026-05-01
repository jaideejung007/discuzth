<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/post');
include template('common/header_ajax');
$_POST = ['action' => $_GET['ac']];
list($seccodecheck, $secqaacheck) = seccheck('post', $_GET['ac']);
if($seccodecheck || $secqaacheck) {
	include template('forum/seccheck_post');
}
include template('common/footer_ajax');
exit;
	