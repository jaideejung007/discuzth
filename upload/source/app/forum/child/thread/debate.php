<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$debate = $_G['forum_thread'];
$debate = table_forum_debate::t()->fetch($_G['tid']);
$debate['dbendtime'] = $debate['endtime'];
if($debate['dbendtime']) {
	$debate['endtime'] = dgmdate($debate['dbendtime']);
}
if($debate['dbendtime'] > TIMESTAMP) {
	$debate['remaintime'] = remaintime($debate['dbendtime'] - TIMESTAMP);
}
$debate['starttime'] = dgmdate($debate['starttime'], 'u');
$debate['affirmpoint'] = discuzcode($debate['affirmpoint'], 0, 0, 0, 1, 1, 0, 0, 0, 0, 0);
$debate['negapoint'] = discuzcode($debate['negapoint'], 0, 0, 0, 1, 1, 0, 0, 0, 0, 0);
if($debate['affirmvotes'] || $debate['negavotes']) {
	if($debate['affirmvotes'] && $debate['affirmvotes'] > $debate['negavotes']) {
		$debate['affirmvoteswidth'] = 100;
		$debate['negavoteswidth'] = intval($debate['negavotes'] / $debate['affirmvotes'] * 100);
		$debate['negavoteswidth'] = $debate['negavoteswidth'] > 0 ? $debate['negavoteswidth'] : 5;
	} elseif($debate['negavotes'] && $debate['negavotes'] > $debate['affirmvotes']) {
		$debate['negavoteswidth'] = 100;
		$debate['affirmvoteswidth'] = intval($debate['affirmvotes'] / $debate['negavotes'] * 100);
		$debate['affirmvoteswidth'] = $debate['affirmvoteswidth'] > 0 ? $debate['affirmvoteswidth'] : 5;
	} else {
		$debate['affirmvoteswidth'] = $debate['negavoteswidth'] = 100;
	}
} else {
	$debate['negavoteswidth'] = $debate['affirmvoteswidth'] = 5;
}
if($debate['umpirepoint']) {
	$debate['umpirepoint'] = discuzcode($debate['umpirepoint'], 0, 0, 0, 1, 1, 1, 0, 0, 0, 0);
}
$debate['umpireurl'] = rawurlencode($debate['umpire']);
list($debate['bestdebater'], $debate['bestdebateruid'], $debate['bestdebaterstand'], $debate['bestdebatervoters'], $debate['bestdebaterreplies']) = explode("\t", $debate['bestdebater']);
$debate['bestdebaterurl'] = rawurlencode($debate['bestdebater']);
foreach(table_forum_post::t()->fetch_all_debatepost_by_tid_stand($_G['tid'], 1, 0, 16) as $affirmavatar) {
	if(!isset($debate['affirmavatars'][$affirmavatar['authorid']])) {
		$affirmavatar['avatar'] = avatar($affirmavatar['authorid'], 'small');
		$debate['affirmavatars'][$affirmavatar['authorid']] = $affirmavatar;
	}
}

foreach(table_forum_post::t()->fetch_all_debatepost_by_tid_stand($_G['tid'], 2, 0, 16) as $negaavatar) {
	if(!isset($debate['negaavatars'][$negaavatar['authorid']])) {
		$negaavatar['avatar'] = avatar($negaavatar['authorid'], 'small');
		$debate['negaavatars'][$negaavatar['authorid']] = $negaavatar;
	}
}

if($_G['setting']['fastpost'] && $allowpostreply && $_G['forum_thread']['closed'] == 0) {
	$firststand = table_forum_debatepost::t()->get_firststand($_G['tid'], $_G['uid']);
}

