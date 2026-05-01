<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowcopythread'] || !$thread) {
	showmessage('no_privilege_copythread');
}

if(!submitcheck('modsubmit')) {
	require_once libfile('function/forumlist');
	$forumselect = forumselect();
	include template('forum/topicadmin_action');

} else {

	$modaction = 'CPY';
	$reason = checkreasonpm();
	$copyto = $_GET['copyto'];
	$toforum = table_forum_forum::t()->fetch_info_by_fid($copyto);
	if(!$toforum || $toforum['status'] != 1 || $toforum['type'] == 'group') {
		showmessage('admin_copy_invalid');
	} else {
		$modnewthreads = (!$_G['group']['allowdirectpost'] || $_G['group']['allowdirectpost'] == 1) && $toforum['modnewposts'] ? 1 : 0;
		$modnewreplies = (!$_G['group']['allowdirectpost'] || $_G['group']['allowdirectpost'] == 2) && $toforum['modnewposts'] ? 1 : 0;
		if($modnewthreads || $modnewreplies) {
			showmessage('admin_copy_hava_mod');
		}
	}
	$toforum['threadsorts_arr'] = dunserialize($toforum['threadsorts']);

	if($thread['sortid'] != 0 && $toforum['threadsorts_arr']['types'][$thread['sortid']]) {
		foreach(table_forum_typeoptionvar::t()->fetch_all_by_search($thread['sortid'], null, $thread['tid']) as $result) {
			$typeoptionvar[] = $result;
		}
	} else {
		$thread['sortid'] = '';
	}

	$sourcetid = $thread['tid'];
	unset($thread['tid']);
	$thread['fid'] = $copyto;
	$thread['dateline'] = $thread['lastpost'] = TIMESTAMP;
	$thread['lastposter'] = $thread['author'];
	$thread['views'] = $thread['replies'] = $thread['highlight'] = $thread['digest'] = 0;
	$thread['rate'] = $thread['displayorder'] = $thread['attachment'] = 0;
	$thread['typeid'] = $_GET['threadtypeid'];
	$thread = daddslashes($thread);

	$thread['posttableid'] = 0;
	$threadid = table_forum_thread::t()->insert($thread, true);
	table_forum_newthread::t()->insert([
		'tid' => $threadid,
		'fid' => $thread['fid'],
		'dateline' => $thread['dateline'],
	]);
	table_forum_sofa::t()->insert(['tid' => $threadid, 'fid' => $thread['fid']]);
	if($post = table_forum_post::t()->fetch_threadpost_by_tid_invisible($_G['tid'])) {
		$post['pid'] = '';
		$post['tid'] = $threadid;
		$post['fid'] = $copyto;
		$post['dateline'] = $thread['dateline'];
		$post['attachment'] = 0;
		$post['invisible'] = $post['rate'] = $post['ratetimes'] = 0;
		$post['message'] .= "\n".lang('forum/thread', 'source').": [url=forum.php?mod=viewthread&tid={$sourcetid}]{$thread['subject']}[/url]";
		$pid = insertpost($post);
	}

	$class_tag = new tag();
	$class_tag->copy_tag($_G['tid'], $threadid, 'tid');

	if($typeoptionvar) {
		foreach($typeoptionvar as $key => $value) {
			$value['tid'] = $threadid;
			$value['fid'] = $toforum['fid'];
			table_forum_typeoptionvar::t()->insert($value);
		}
	}
	updatepostcredits('+', $post['authorid'], 'post', $copyto);

	updateforumcount($copyto);
	updateforumcount($_G['fid']);

	$modpostsnum++;
	$resultarray = [
		'redirect' => "forum.php?mod=forumdisplay&fid={$_G['fid']}",
		'reasonpm' => ($sendreasonpm ? ['data' => [$thread], 'var' => 'thread', 'item' => 'reason_copy', 'notictype' => 'post'] : []),
		'reasonvar' => ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason, 'threadid' => $threadid],
		'modtids' => $thread['tid'],
		'modlog' => [$thread, $other]
	];
}

