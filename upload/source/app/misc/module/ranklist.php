<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['rankliststatus']) {
	showmessage('ranklist_status_off');
}

require_once childfile('function');

$page = $_G['page'];
$type = $_GET['type'];

$_G['disabledwidthauto'] = 1;

if(!in_array($type, ['index', 'member', 'thread', 'blog', 'poll', 'picture', 'activity', 'forum', 'group'])) {
	$type = 'index';
}

$ranklist_setting = $_G['setting']['ranklist'];

$navtitle = lang('core', 'title_ranklist_'.$type);

$allowtype = ['member' => 'ranklist', 'thread' => 'forum', 'blog' => 'blog', 'poll' => 'forum', 'picture' => 'album', 'activity' => 'forum', 'forum' => 'forum', 'group' => 'group'];

if($type != 'index') {
	if(!array_key_exists($type, $allowtype) || !$_G['setting'][$allowtype[$type].'status'] || !$ranklist_setting[$type]['available']) {
		showmessage('ranklist_this_status_off');
	}
}

include childfile($type);