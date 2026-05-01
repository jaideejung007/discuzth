<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$starttime = strtotime($_GET['starttime']);
$data = [];
if($starttime) {
	$data = table_portal_topic::t()->fetch_all_topicid_by_dateline($starttime);
}

helper_output::xml($data ? implode(',', array_keys($data)) : '');
	