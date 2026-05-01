<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_GET['uid']) {
	showmessage('undefined_action');
}
$warnuser = getuserbyuid($_GET['uid']);
$warnuser = $warnuser['username'];
if(!$warnuser) {
	showmessage('member_no_found');
}

$warnings = [];
$warnings = table_forum_warning::t()->fetch_all_by_authorid($_GET['uid']);

if(!$warnings) {
	showmessage('thread_warning_nonexistence');
}

foreach($warnings as $key => $warning) {
	$warning['dateline'] = dgmdate($warning['dateline'], 'u');
	$warning['reason'] = dhtmlspecialchars($warning['reason']);
	$warnings[$key] = $warning;
}
$warnnum = count($warnings);

include template('forum/warn_view');
	