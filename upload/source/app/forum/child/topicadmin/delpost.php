<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowdelpost']) {
	showmessage('no_privilege_delpost');
}

$topiclist = $_GET['topiclist'];
$modpostsnum = count($topiclist);

$authorcount = $crimenum = 0;
$crimeauthor = '';
$pids = $posts = $authors = [];

if(!($deletepids = dimplode($topiclist))) {
	showmessage('admin_delpost_invalid');
} elseif(!$_G['group']['allowdelpost'] || !$_G['tid']) {
	showmessage('admin_nopermission');
} else {
	$posttable = getposttablebytid($_G['tid']);
	foreach(table_forum_post::t()->fetch_all_post('tid:'.$_G['tid'], $topiclist, false) as $post) {
		if($post['tid'] != $_G['tid']) {
			continue;
		}
		if($post['first'] == 1) {
			dheader("location: {$_G['siteurl']}forum.php?mod=topicadmin&action=moderate&operation=delete&optgroup=3&fid={$_G['fid']}&moderate[]={$thread['tid']}&inajax=yes".($_GET['infloat'] ? "&infloat=yes&handlekey={$_GET['handlekey']}" : ''));
		} else {
			$authors[$post['authorid']] = 1;
			$pids[] = $post['pid'];
			$posts[] = $post;
		}
	}
}

if(!submitcheck('modsubmit')) {

	$deleteid = '';
	foreach($topiclist as $id) {
		$deleteid .= '<input type="hidden" name="topiclist[]" value="'.$id.'" />';
	}

	$authorcount = count(array_keys($authors));

	if($modpostsnum == 1 || $authorcount == 1) {
		include_once libfile('function/member');
		$crimenum = crime('getcount', $posts[0]['authorid'], 'crime_delpost');
		$crimeauthor = $posts[0]['author'];
	}

	include template('forum/topicadmin_action');

} else {

	$reason = checkreasonpm();

	$uidarray = $puidarray = $auidarray = [];
	$losslessdel = $_G['setting']['losslessdel'] > 0 ? TIMESTAMP - $_G['setting']['losslessdel'] * 86400 : 0;

	if($pids) {
		require_once libfile('function/delete');
		if($_G['forum']['recyclebin']) {
			deletepost($pids, 'pid', true, false, true);
			manage_addnotify('verifyrecyclepost', $modpostsnum);
		} else {
			$logs = [];
			$ratelog = table_forum_ratelog::t()->fetch_all_by_pid($pids);
			$rposts = table_forum_post::t()->fetch_all_post('tid:'.$_G['tid'], $pids, false);
			foreach(table_forum_ratelog::t()->fetch_all_by_pid($pids) as $rpid => $author) {
				if($author['score'] > 0) {
					$rpost = $rposts[$rpid];
					updatemembercount($rpost['authorid'], [$author['extcredits'] => -$author['score']]);
					$author['score'] = $_G['setting']['extcredits'][$id]['title'].' '.-$author['score'].' '.$_G['setting']['extcredits'][$id]['unit'];
					if($_G['setting']['log']['rate']) {
						$errorlog = [
							'timestamp' => TIMESTAMP,
							'operator_username' => $_G['member']['username'],
							'operator_adminid' => $_G['adminid'],
							'member_username' => $rpost['author'],
							'extcredits' => $author['extcredits'],
							'diff' => $author['score'],
							'tid' => $thread['tid'],
							'subject' => $thread['subject'],
							'reason' => $delpostsubmit,
							'd' => '',
						];
						$member_log = table_common_member::t()->fetch_by_username($rpost['author']);
						logger('rate', $member_log, $_G['member']['uid'], $errorlog);
					}
				}
			}
			deletepost($pids, 'pid', true);
		}

		if($_G['group']['allowbanuser'] && ($_GET['banuser'] || $_GET['userdelpost']) && $_G['deleteauthorids']) {
			$members = table_common_member::t()->fetch_all($_G['deleteauthorids']);
			$banuins = [];
			foreach($members as $member) {
				if(($_G['cache']['usergroups'][$member['groupid']]['type'] == 'system' &&
						in_array($member['groupid'], [1, 2, 3, 6, 7, 8])) || $_G['cache']['usergroups'][$member['groupid']]['type'] == 'special') {
					continue;
				}
				$banuins[$member['uid']] = $member['uid'];
			}
			if($banuins) {
				if($_GET['banuser']) {
					table_common_member::t()->update($banuins, ['groupid' => 4]);
				}

				if($_GET['userdelpost']) {
					deletememberpost($banuins);
				}
			}
		}

		if($_GET['crimerecord']) {
			include_once libfile('function/member');

			foreach($posts as $post) {
				crime('recordaction', $post['authorid'], 'crime_delpost', lang('forum/misc', 'crime_postreason', ['reason' => $reason, 'tid' => $post['tid'], 'pid' => $post['pid']]));
			}
		}
	}

	updatethreadcount($_G['tid'], 1);
	updateforumcount($_G['fid']);

	$_G['forum']['threadcaches'] && deletethreadcaches($thread['tid']);

	$modaction = 'DLP';

	$resultarray = [
		'redirect' => "forum.php?mod=viewthread&tid={$_G['tid']}&page={$_GET['page']}",
		'reasonpm' => ($sendreasonpm ? ['data' => $posts, 'var' => 'post', 'item' => 'reason_delete_post', 'notictype' => 'post'] : []),
		'reasonvar' => ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason],
		'modtids' => 0,
		'modlog' => $thread
	];

}

