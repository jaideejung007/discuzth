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
loadcache('groupreadaccess');
$attachlist = getattach($_GET['pid'], intval($_GET['posttime']), $_GET['aids']);
$attachlist = $attachlist['attachs']['unused'];
$_G['group']['maxprice'] = isset($_G['setting']['extcredits'][$_G['setting']['creditstrans']]) ? $_G['group']['maxprice'] : 0;

include template('common/header_ajax');
include template('forum/ajax_attachlist');
include template('common/footer_ajax');
dexit();
	