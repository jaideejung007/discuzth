<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_GET['formhash'] != FORMHASH || !$_G['group']['allowmanagetag']) {
	showmessage('undefined_action');
}
$class_tag = new tag();
$tagstr = $class_tag->update_field($_GET['tags'], intval($_GET['tid']), 'tid', $_G['thread']);
table_forum_post::t()->update_by_tid('tid:'.intval($_GET['tid']), intval($_GET['tid']), ['tags' => $tagstr], false, false, 1);