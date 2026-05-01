<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$searchkey = stripsearchkey($_GET['searchkey']);
$query = table_common_tag::t()->fetch_all_by_status(0, $searchkey, 50, 0);
foreach($query as $value) {
	$taglist[] = $value;
}
$searchkey = dhtmlspecialchars($searchkey);