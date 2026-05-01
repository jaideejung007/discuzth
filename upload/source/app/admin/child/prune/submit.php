<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$pidsdelete = $tidsdelete = [];
$pids = authcode($_GET['pids'], 'DECODE');

loadcache('posttableids');
$posttable = !empty($_G['cache']['posttableids']) && in_array($_GET['posttableid'], $_G['cache']['posttableids']) ? $_GET['posttableid'] : 0;
foreach(table_forum_post::t()->fetch_all_post($posttable, ($pids ? explode(',', $pids) : $_GET['pidarray']), false) as $post) {
	$prune['forums'][] = $post['fid'];
	$prune['thread'][$post['tid']]++;

	$pidsdelete[] = $post['pid'];
	if($post['first']) {
		$tidsdelete[] = $post['tid'];
	}
}

if($pidsdelete) {
	require_once libfile('function/post');
	require_once libfile('function/delete');
	$deletedposts = deletepost($pidsdelete, 'pid', !$_GET['donotupdatemember'], $posttable);
	$deletedthreads = deletethread($tidsdelete, !$_GET['donotupdatemember'], !$_GET['donotupdatemember']);

	if(count($prune['thread']) < 50) {
		foreach($prune['thread'] as $tid => $decrease) {
			updatethreadcount($tid);
		}
	} else {
		$repliesarray = [];
		foreach($prune['thread'] as $tid => $decrease) {
			$repliesarray[$decrease][] = $tid;
		}
		foreach($repliesarray as $decrease => $tidarray) {
			table_forum_thread::t()->increase($tidarray, ['replies' => -$decrease]);
		}
	}

	if($_G['setting']['globalstick']) {
		updatecache('globalstick');
	}

	foreach(array_unique($prune['forums']) as $fid) {
		updateforumcount($fid);
	}

}

$deletedthreads = intval($deletedthreads);
$deletedposts = intval($deletedposts);
updatemodworks('DLP', $deletedposts);
$cpmsg = cplang('prune_succeed', ['deletedthreads' => $deletedthreads, 'deletedposts' => $deletedposts]);

echo '<script type="text/JavaScript">alert(\''.$cpmsg.'\');parent.$(\'pruneforum\').searchsubmit.click();</script>';
	