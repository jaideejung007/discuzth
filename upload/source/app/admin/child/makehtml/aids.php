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
$catids = $_GET['catids'];
if($catids) {
	$catids = array_map('intval', explode(',', $catids));
}
$startid = intval($_GET['startid']);
$endid = intval($_GET['endid']);
$data = [];
if($starttime || $catids || $startid || $endid) {
	$data = table_portal_article_title::t()->fetch_all_aid_by_dateline($starttime, $catids, $startid, $endid);
}

helper_output::xml($data ? implode(',', array_keys($data)) : '');
	