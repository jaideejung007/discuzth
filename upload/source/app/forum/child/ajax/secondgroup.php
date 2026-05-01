<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/group');
$groupselect = get_groupselect($_GET['fupid'], $_GET['groupid']);
include template('common/header_ajax');
include template('forum/ajax_secondgroup');
include template('common/footer_ajax');
dexit();
	