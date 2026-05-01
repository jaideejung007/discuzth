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
if(!$_G['forum']['ismoderator'] || !$_G['group']['raterange']) {
	showmessage('no_privilege_removerate');
}

$reasonpmcheck = $_G['group']['reasonpm'] == 2 || $_G['group']['reasonpm'] == 3 ? 'checked="checked" disabled' : '';
if(($_G['group']['reasonpm'] == 2 || $_G['group']['reasonpm'] == 3) || !empty($_GET['sendreasonpm'])) {
	$forumname = strip_tags($_G['forum']['name']);
	$sendreasonpm = 1;
} else {
	$sendreasonpm = 0;
}

foreach($_G['group']['raterange'] as $id => $rating) {
	$maxratetoday[$id] = $rating['mrpd'];
}
$post = table_forum_post::t()->fetch_post('tid:'.$_G['tid'], $_GET['pid']);
if($post['invisible'] != 0 || $post['authorid'] == 0) {
	$post = [];
}

if(!$post || $post['tid'] != $thread['tid'] || !$post['authorid']) {
	showmessage('rate_post_error');
}

require_once libfile('function/misc');

if(!submitcheck('ratesubmit')) {

	$referer = $_G['siteurl'].'forum.php?mod=viewthread&tid='.$_G['tid'].'&page='.$page.($_GET['from'] ? '&from='.$_GET['from'] : '').'#pid'.$_GET['pid'];
	$ratelogs = [];

	foreach(table_forum_ratelog::t()->fetch_all_by_pid($_GET['pid'], 'ASC') as $ratelog) {
		$ratelog['dbdateline'] = $ratelog['dateline'];
		$ratelog['dateline'] = dgmdate($ratelog['dateline'], 'u');
		$ratelog['scoreview'] = $ratelog['score'] > 0 ? '+'.$ratelog['score'] : $ratelog['score'];
		$ratelogs[] = $ratelog;
	}

	include template('forum/rate');

} else {

	$reason = checkreasonpm();

	if(!empty($_GET['logidarray'])) {
		if($sendreasonpm) {
			$ratescore = $slash = '';
		}

		$rate = $ratetimes = 0;
		$logs = [];
		foreach(table_forum_ratelog::t()->fetch_all_by_pid($_GET['pid']) as $ratelog) {
			if(in_array($ratelog['uid'].' '.$ratelog['extcredits'].' '.$ratelog['dateline'], $_GET['logidarray'])) {
				$rate += $ratelog['score'] = -$ratelog['score'];
				$ratetimes += ceil(max(abs($rating['min']), abs($rating['max'])) / 5);
				updatemembercount($post['authorid'], [$ratelog['extcredits'] => $ratelog['score']]);
				table_common_credit_log::t()->delete_by_uid_operation_relatedid($post['authorid'], 'PRC', $_GET['pid']);
				table_forum_ratelog::t()->delete_by_pid_uid_extcredits_dateline($_GET['pid'], $ratelog['uid'], $ratelog['extcredits'], $ratelog['dateline']);
				if($_G['setting']['log']['rate']) {
					$errorlog = [
						'timestamp' => TIMESTAMP,
						'operator_username' => $_G['member']['username'],
						'operator_adminid' => $_G['adminid'],
						'member_username' => $ratelog['username'],
						'extcredits' => $ratelog['extcredits'],
						'diff' => $ratelog['score'],
						'tid' => $_G['tid'],
						'subject' => $thread['subject'],
						'reason' => $reason,
						'd' => 'D',
					];
					$member_log = table_common_member::t()->fetch_by_username($ratelog['username']);
					logger('rate', $member_log, $_G['member']['uid'], $errorlog);
				}
				if($sendreasonpm) {
					$ratescore .= $slash.$_G['setting']['extcredits'][$ratelog['extcredits']]['title'].' '.($ratelog['score'] > 0 ? '+'.$ratelog['score'] : $ratelog['score']).' '.$_G['setting']['extcredits'][$ratelog['extcredits']]['unit'];
					$slash = ' / ';
				}
			}
		}
		table_forum_postcache::t()->delete($_GET['pid']);

		if($sendreasonpm) {
			sendreasonpm($post, 'rate_removereason', [
				'tid' => $thread['tid'],
				'pid' => $_GET['pid'],
				'subject' => $thread['subject'],
				'ratescore' => $ratescore,
				'reason' => $reason,
				'from_id' => 0,
				'from_idtype' => 'removerate'
			]);
		}
		table_forum_post::t()->increase_rate_by_pid('tid:'.$_G['tid'], $_GET['pid'], $rate, $ratetimes);
		if($post['first']) {
			$threadrate = intval((abs($post['rate'] + $rate) ? (($post['rate'] + $rate) / abs($post['rate'] + $rate)) : 0));
			table_forum_thread::t()->update($_G['tid'], ['rate' => $threadrate]);
		}

	}

	showmessage('thread_rate_removesucceed', dreferer());

}
	