<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowbanpost']) {
	showmessage('no_privilege_banpost');
}

$topiclist = $_GET['topiclist'];
$modpostsnum = count($topiclist);
if(!($banpids = dimplode($topiclist))) {
	showmessage('admin_banpost_invalid');
} elseif(!$_G['group']['allowbanpost'] || !$_G['tid']) {
	showmessage('admin_nopermission');
}

$posts = $authors = [];
$banstatus = 0;
foreach(table_forum_post::t()->fetch_all_post('tid:'.$_G['tid'], $topiclist) as $post) {
	if($post['tid'] != $_G['tid']) {
		continue;
	}
	$banstatus = ($post['status'] & 1) || $banstatus;
	$authors[$post['authorid']] = 1;
	$posts[] = $post;
}

$authorcount = count(array_keys($authors));

if(!submitcheck('modsubmit')) {

	$banid = $checkunban = $checkban = '';
	foreach($topiclist as $id) {
		$banid .= '<input type="hidden" name="topiclist[]" value="'.$id.'" />';
	}

	$banstatus ? $checkunban = 'checked="checked"' : $checkban = 'checked="checked"';

	if($modpostsnum == 1 || $authorcount == 1) {
		include_once libfile('function/member');
		$crimenum = crime('getcount', $posts[0]['authorid'], 'crime_banpost');
		$crimeauthor = $posts[0]['author'];
	}

	include template('forum/topicadmin_action');

} else {

	$banned = intval($_GET['banned']);
	$modaction = $banned ? 'BNP' : 'UBN';

	$reason = checkreasonpm();

	include_once libfile('function/member');

	$modtids = 0;
	$pids = $comma = '';
	foreach($posts as $k => $post) {
		if($banned) {
			table_forum_postcomment::t()->delete_by_rpid($post['pid']);
			table_forum_post::t()->increase_status_by_pid('tid:'.$_G['tid'], $post['pid'], 1, '|', true);
			crime('recordaction', $post['authorid'], 'crime_banpost', lang('forum/misc', 'crime_postreason', ['reason' => $reason, 'tid' => $_G['tid'], 'pid' => $post['pid']]));
		} else {
			table_forum_post::t()->increase_status_by_pid('tid:'.$_G['tid'], $post['pid'], 1, '&~', true);
		}
		if($post['first']) {
			$modtids = $thread['tid'];
		}
		$pids .= $comma.$post['pid'];
		$comma = ',';
	}

	$resultarray = [
		'redirect' => "forum.php?mod=viewthread&tid={$_G['tid']}&page=$page",
		'reasonpm' => ($sendreasonpm ? ['data' => $posts, 'var' => 'post', 'item' => 'reason_ban_post', 'notictype' => 'post'] : []),
		'reasonvar' => ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason],
		'modtids' => $modtids,
		'modlog' => $thread
	];

}

