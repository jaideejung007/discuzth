<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowwarnpost']) {
	showmessage('no_privilege_warnpost');
}

$topiclist = $_GET['topiclist'];
if(!($warnpids = dimplode($topiclist))) {
	showmessage('admin_warn_invalid');
} elseif(!$_G['group']['allowbanpost'] || !$_G['tid']) {
	showmessage('admin_nopermission', NULL);
}

$posts = $authors = [];
$authorwarnings = $warningauthor = $warnstatus = '';
$postlist = table_forum_post::t()->fetch_all_post('tid:'.$_G['tid'], $topiclist);
foreach($postlist as $post) {
	$uids[] = $post['authorid'];
}
$memberlist = table_common_member::t()->fetch_all($uids);
foreach($postlist as $post) {
	if($post['tid'] != $_G['tid']) {
		continue;
	}
	$post['adminid'] = $memberlist[$post['authorid']]['adminid'];
	if($_G['adminid'] == 1 && $post['adminid'] != 1 ||
		$_G['adminid'] == 2 && !in_array($post['adminid'], [1, 2]) ||
		$_G['adminid'] == 3 && in_array($post['adminid'], [0, -1])) {
		$warnstatus = ($post['status'] & 2) || $warnstatus;
		$authors[$post['authorid']] = 1;
		$posts[] = $post;
	}
}
unset($memberlist, $postlist, $uids);

if(!$posts) {
	showmessage('admin_warn_nopermission');
}
$authorcount = count(array_keys($authors));
$modpostsnum = count($posts);

if($modpostsnum == 1 || $authorcount == 1) {
	$authorwarnings = table_forum_warning::t()->count_by_authorid_dateline($posts[0]['authorid']);
	$warningauthor = $posts[0]['author'];
}

if(!submitcheck('modsubmit')) {

	$warnpid = $checkunwarn = $checkwarn = '';
	foreach($topiclist as $id) {
		$warnpid .= '<input type="hidden" name="topiclist[]" value="'.$id.'" />';
	}

	$warnstatus ? $checkunwarn = 'checked="checked"' : $checkwarn = 'checked="checked"';

	include template('forum/topicadmin_action');

} else {

	$warned = intval($_GET['warned']);
	$modaction = $warned ? 'WRN' : 'UWN';

	$reason = checkreasonpm();

	include_once libfile('function/member');

	$pids = $comma = '';
	foreach($posts as $k => $post) {
		if($warned && !($post['status'] & 2)) {
			table_forum_post::t()->increase_status_by_pid('tid:'.$_G['tid'], $post['pid'], 2, '|', true);
			$reason = dhtmlspecialchars($_GET['reason']);
			if($_G['setting']['log']['warn']) {
				$errorlog = [
					'pid' => $post['pid'],
					'operatorid' => $_G['uid'],
					'operator' => $_G['username'],
					'authorid' => $post['authorid'],
					'author' => $post['author'],
					'dateline' => $_G['timestamp'],
					'reason' => $reason,
				];
				$member_log = getuserbyuid($post['authorid']);
				logger('warn', $member_log, $_G['uid'], $errorlog);
			}
			table_forum_warning::t()->insert([
				'pid' => $post['pid'],
				'operatorid' => $_G['uid'],
				'operator' => $_G['username'],
				'authorid' => $post['authorid'],
				'author' => $post['author'],
				'dateline' => $_G['timestamp'],
				'reason' => $reason,
			]);
			$authorwarnings = table_forum_warning::t()->count_by_authorid_dateline($post['authorid'], $_G['timestamp'] - $_G['setting']['warningexpiration'] * 86400);
			if($authorwarnings >= $_G['setting']['warninglimit']) {
				$member = getuserbyuid($post['authorid']);
				$memberfieldforum = table_common_member_field_forum::t()->fetch($post['authorid']);
				$groupterms = dunserialize($memberfieldforum['groupterms']);
				unset($memberfieldforum);
				if($member && $member['groupid'] != 4) {
					$banexpiry = TIMESTAMP + $_G['setting']['warningexpiration'] * 86400;
					$groupterms['main'] = ['time' => $banexpiry, 'adminid' => $member['adminid'], 'groupid' => $member['groupid']];
					$groupterms['ext'][4] = $banexpiry;
					table_common_member::t()->update($post['authorid'], ['groupid' => 4, 'adminid' => -1, 'groupexpiry' => groupexpiry($groupterms)]);
					table_common_member_field_forum::t()->update($post['authorid'], ['groupterms' => serialize($groupterms)]);
				}
			}
			$pids .= $comma.$post['pid'];
			$comma = ',';

			crime('recordaction', $post['authorid'], 'crime_warnpost', lang('forum/misc', 'crime_postreason', ['reason' => $reason, 'tid' => $_G['tid'], 'pid' => $post['pid']]));

		} elseif(!$warned && ($post['status'] & 2)) {
			table_forum_post::t()->increase_status_by_pid('tid:'.$_G['tid'], $post['pid'], 2, '^', true);
			table_forum_warning::t()->delete_by_pid($post['pid']);
			$pids .= $comma.$post['pid'];
			$comma = ',';
		}
	}

	$resultarray = [
		'redirect' => "forum.php?mod=viewthread&tid={$_G['tid']}&page=$page",
		'reasonpm' => ($sendreasonpm ? ['data' => $posts, 'var' => 'post', 'item' => 'reason_warn_post', 'notictype' => 'post'] : []),
		'reasonvar' => ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason,
			'warningexpiration' => $_G['setting']['warningexpiration'], 'warninglimit' => $_G['setting']['warninglimit'],
			'authorwarnings' => $authorwarnings],
		'modtids' => 0,
		'modlog' => $thread
	];

}

