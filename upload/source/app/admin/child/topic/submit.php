<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($_POST['ids'])) {
	cpmsg('topic_choose_at_least_one_topic', 'action=topic', 'error');
}

if($_POST['optype'] == 'delete') {
	require_once libfile('function/delete');
	deleteportaltopic($_POST['ids']);
	cpmsg('topic_delete_succeed', 'action=topic', 'succeed');

} elseif($_POST['optype'] == 'close') {
	table_portal_topic::t()->update($_POST['ids'], ['closed' => 1]);
	cpmsg('topic_close_succeed', 'action=topic', 'succeed');

} elseif($_POST['optype'] == 'open') {
	table_portal_topic::t()->update($_POST['ids'], ['closed' => 0]);
	cpmsg('topic_open_succeed', 'action=topic', 'succeed');

} else {
	cpmsg('topic_choose_at_least_one_optype', 'action=topic', 'error');
}
	