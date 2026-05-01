<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$navtitle = $_G['setting']['plugins']['faq'][$_GET['id']]['name'];
$navigation = '<em>&rsaquo;</em> '.$_G['setting']['plugins']['faq'][$_GET['id']]['name'];
include pluginmodule($_GET['id'], 'faq');
	