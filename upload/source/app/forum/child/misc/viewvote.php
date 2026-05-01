<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_G['forum_thread']['special'] != 1) {
	showmessage('thread_poll_none');
}
require_once libfile('function/post');
$polloptionid = is_numeric($_GET['polloptionid']) ? $_GET['polloptionid'] : '';

$page = intval($_GET['page']) ? intval($_GET['page']) : 1;
$perpage = 100;
$pollinfo = table_forum_poll::t()->fetch($_G['tid']);
$overt = $pollinfo['overt'];

$polloptions = [];
$query = table_forum_polloption::t()->fetch_all_by_tid($_G['tid']);
foreach($query as $options) {
	if(empty($polloptionid)) {
		$polloptionid = $options['polloptionid'];
	}
	$options['polloption'] = preg_replace("/\[url=(https?){1}:\/\/([^\[\"']+?)\](.+?)\[\/url\]/i",
		"<a href=\"\\1://\\2\" target=\"_blank\">\\3</a>", $options['polloption']);
	$polloptions[] = $options;
}

$arrvoterids = [];
if($overt || $_G['adminid'] == 1 || $thread['authorid'] == $_G['uid']) {
	$polloptioninfo = table_forum_polloption::t()->fetch($polloptionid);
	$voterids = $polloptioninfo['voterids'];
	$arrvoterids = explode("\t", trim($voterids));
} else {
	showmessage('thread_poll_nopermission');
}

if(!empty($arrvoterids)) {
	$count = count($arrvoterids);
	$multi = $perpage * ($page - 1);
	$multipage = multi($count, $perpage, $page, "forum.php?mod=misc&action=viewvote&tid={$_G['tid']}&polloptionid=$polloptionid".($_GET['handlekey'] ? '&handlekey='.$_GET['handlekey'] : ''));
	$arrvoterids = array_slice($arrvoterids, $multi, $perpage);
}
$voterlist = $voter = [];
if($arrvoterids) {
	$voterlist = table_common_member::t()->fetch_all($arrvoterids);
}
include template('forum/viewthread_poll_voter');
	