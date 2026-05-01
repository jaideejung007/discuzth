<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_GET['pid']) {
	showmessage('undefined_action');
}

$_GET['tid'] = dintval($_GET['tid']);
$_GET['pid'] = dintval($_GET['pid']);

if($_GET['showratetip']) {
	include template('forum/rate');
	exit();
}

if(!$_G['inajax']) {
	showmessage('undefined_action');
}
if(!$_G['group']['raterange']) {
	showmessage('group_nopermission', NULL, ['grouptitle' => $_G['group']['grouptitle']], ['login' => 1]);
} elseif($_G['setting']['modratelimit'] && $_G['adminid'] == 3 && !$_G['forum']['ismoderator']) {
	showmessage('thread_rate_moderator_invalid', NULL);
}
$reasonpmcheck = $_G['group']['reasonpm'] == 2 || $_G['group']['reasonpm'] == 3 ? 'checked="checked" disabled' : '';
if(($_G['group']['reasonpm'] == 2 || $_G['group']['reasonpm'] == 3) || !empty($_GET['sendreasonpm'])) {
	$forumname = strip_tags($_G['forum']['name']);
	$sendreasonpm = 1;
} else {
	$sendreasonpm = 0;
}

$post = table_forum_post::t()->fetch_post('tid:'.$_G['tid'], $_GET['pid']);
if($post['invisible'] != 0 || $post['authorid'] == 0) {
	$post = [];
}

if(!$post || $post['tid'] != $thread['tid'] || !$post['authorid']) {
	showmessage('rate_post_error');
} elseif(!$_G['forum']['ismoderator'] && $_G['setting']['karmaratelimit'] && TIMESTAMP - $post['dateline'] > $_G['setting']['karmaratelimit'] * 3600) {
	showmessage('thread_rate_timelimit', NULL, ['karmaratelimit' => $_G['setting']['karmaratelimit']]);
} elseif($post['authorid'] == $_G['uid'] || $post['tid'] != $_G['tid']) {
	showmessage('thread_rate_member_invalid', NULL);
} elseif($post['anonymous']) {
	showmessage('thread_rate_anonymous', NULL);
} elseif($post['status'] & 1) {
	showmessage('thread_rate_banned', NULL);
}

$allowrate = TRUE;
if(!$_G['setting']['dupkarmarate']) {
	if(table_forum_ratelog::t()->count_by_uid_pid($_G['uid'], $_GET['pid'])) {
		showmessage('thread_rate_duplicate', NULL);
	}
}

$page = intval($_GET['page']);

require_once libfile('function/misc');

$maxratetoday = getratingleft($_G['group']['raterange']);

if(!submitcheck('ratesubmit')) {
	$referer = $_G['siteurl'].'forum.php?mod=viewthread&tid='.$_G['tid'].'&page='.$page.($_GET['from'] ? '&from='.$_GET['from'] : '').'#pid'.$_GET['pid'];
	$ratelist = getratelist($_G['group']['raterange']);
	include template('forum/rate');

} else {
	if($_G['setting']['submitlock'] && discuz_process::islocked('ratelock_'.$_G['uid'].'_'.$_GET['pid'], 0, 1)) {
		showmessage('thread_rate_locked');
	}

	$reason = checkreasonpm();
	$rate = $ratetimes = 0;
	$creditsarray = $sub_self_credit = [];
	getuserprofile('extcredits1');
	foreach($_G['group']['raterange'] as $id => $rating) {
		$score = intval($_GET['score'.$id]);
		if(isset($_G['setting']['extcredits'][$id]) && !empty($score)) {
			if($rating['isself'] && (intval($_G['member']['extcredits'.$id]) - $score < 0)) {
				showmessage('thread_rate_range_self_invalid', '', ['extcreditstitle' => $_G['setting']['extcredits'][$id]['title']]);
			}
			if(abs($score) <= $maxratetoday[$id]) {
				if($score > $rating['max'] || $score < $rating['min']) {
					showmessage('thread_rate_range_invalid');
				} else {
					$creditsarray[$id] = $score;
					if($rating['isself']) {
						$sub_self_credit[$id] = -abs($score);
					}
					$rate += $score;
					$ratetimes += ceil(max(abs($rating['min']), abs($rating['max'])) / 5);
				}
			} else {
				showmessage('thread_rate_ctrl');
			}
		}
	}

	if(!$creditsarray) {
		showmessage('thread_rate_range_invalid', NULL);
	}

	updatemembercount($post['authorid'], $creditsarray, 1, 'PRC', $_GET['pid']);

	if(!empty($sub_self_credit)) {
		updatemembercount($_G['uid'], $sub_self_credit, 1, 'RSC', $_GET['pid']);
	}
	table_forum_post::t()->increase_rate_by_pid('tid:'.$_G['tid'], $_GET['pid'], $rate, $ratetimes);
	if($post['first']) {
		$threadrate = intval((abs($post['rate'] + $rate) ? (($post['rate'] + $rate) / abs($post['rate'] + $rate)) : 0));
		table_forum_thread::t()->update($_G['tid'], ['rate' => $threadrate]);

	}

	require_once libfile('function/discuzcode');
	$sqlvalues = $comma = '';
	$sqlreason = censor(trim($_GET['reason']));
	$sqlreason = cutstr(dhtmlspecialchars($sqlreason), 40);
	foreach($creditsarray as $id => $addcredits) {
		$insertarr = [
			'pid' => $_GET['pid'],
			'uid' => $_G['uid'],
			'username' => $_G['username'],
			'extcredits' => $id,
			'dateline' => $_G['timestamp'],
			'score' => $addcredits,
			'reason' => $sqlreason
		];
		table_forum_ratelog::t()->insert($insertarr);
	}

	include_once libfile('function/post');
	$_G['forum']['threadcaches'] && @deletethreadcaches($_G['tid']);

	$reason = dhtmlspecialchars(censor(trim($reason)));
	if($sendreasonpm) {
		$ratescore = $slash = '';
		foreach($creditsarray as $id => $addcredits) {
			$ratescore .= $slash.$_G['setting']['extcredits'][$id]['title'].' '.($addcredits > 0 ? '+'.$addcredits : $addcredits).' '.$_G['setting']['extcredits'][$id]['unit'];
			$slash = ' / ';
		}
		sendreasonpm($post, 'rate_reason', [
			'tid' => $thread['tid'],
			'pid' => $_GET['pid'],
			'subject' => $thread['subject'],
			'ratescore' => $ratescore,
			'reason' => $reason,
			'from_id' => 0,
			'from_idtype' => 'rate'
		], 'rate', 0);
	}
	if($_G['setting']['log']['rate']) {
		foreach($creditsarray as $id => $addcredits) {
			$errorlog = [
				'timestamp' => TIMESTAMP,
				'operator_username' => $_G['member']['username'],
				'operator_adminid' => $_G['adminid'],
				'member_username' => $post['author'],
				'extcredits' => $id,
				'diff' => $addcredits,
				'tid' => $_G['tid'],
				'subject' => $thread['subject'],
				'reason' => $reason,
				'd' => '',
			];
			$member_log = table_common_member::t()->fetch_by_username($post['author']);
			logger('rate', $member_log, $_G['member']['uid'], $errorlog);

		}
	}

	update_threadpartake($post['tid']);
	table_forum_postcache::t()->delete($_GET['pid']);

	showmessage('thread_rate_succeed', dreferer());
}
	