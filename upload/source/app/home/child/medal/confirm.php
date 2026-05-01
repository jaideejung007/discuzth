<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

include libfile('function/forum');
$medal = table_forum_medal::t()->fetch($_GET['medalid']);
$medal['permission'] = medalformulaperm(serialize(['medal' => dunserialize($medal['permission'])]), $medal['type']);
$medal['image'] = preg_match('/^https?:\/\//is', $medal['image']) ? $medal['image'] : STATICURL.'image/common/'.$medal['image'];
if($medal['price']) {
	$medal['credit'] = $medal['credit'] ? $medal['credit'] : $_G['setting']['creditstransextra'][3];
	$medalcredits[$medal['credit']] = $medal['credit'];
}
include template('home/space_medal_float');
	