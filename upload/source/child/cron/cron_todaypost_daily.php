<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$yesterdayposts = intval(table_forum_forum::t()->fetch_sum_todaypost());

table_forum_forum::t()->update_oldrank_and_yesterdayposts();

$historypost = table_common_setting::t()->fetch_setting('historyposts');
$hpostarray = explode("\t", $historypost);
$_G['setting']['historyposts'] = $hpostarray[1] < $yesterdayposts ? "$yesterdayposts\t$yesterdayposts" : "$yesterdayposts\t$hpostarray[1]";

table_common_setting::t()->update_setting('historyposts', $_G['setting']['historyposts']);
$date = date('Y-m-d', TIMESTAMP - 86400);

table_forum_statlog::t()->insert_stat_log($date);
table_forum_forum::t()->clear_todayposts();
$rank = 1;
foreach(table_forum_statlog::t()->fetch_all_rank_by_logdate($date) as $value) {
	table_forum_forum::t()->update($value['fid'], ['rank' => $rank]);
	$rank++;
}
savecache('historyposts', $_G['setting']['historyposts']);

