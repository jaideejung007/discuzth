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
$attachlist = getattach($_GET['pid'], intval($_GET['posttime']), $_GET['aids']);
$imagelist = $attachlist['imgattachs']['unused'];

include template('common/header_ajax');
include template('forum/ajax_imagelist');
include template('common/footer_ajax');
dexit();
	