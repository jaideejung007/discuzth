<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$tag = table_common_tag::t()->fetch($_GET['tagid']);
if(!$tag || $tag['status'] != 3) {
	cpmsg('undefined_action');
}

if(empty($_GET['confirmed']) || FORMHASH != $_GET['formhash']) {
	cpmsg('usertag_delete_confirm', '', 'form');
} else {
	table_common_tagitem::t()->delete_tagitem($_GET['tagid'], $_GET['uid'], 'uid');
	table_common_tag::t()->increase($_GET['tagid'], ['related_count' => -1]);
	helper_forumperm::clear_cache($_GET['uid']);
	cpmsg('usertag_delete_succeed', 'action=members&operation=edit&uid='.$_GET['uid'], 'succeed');
}
