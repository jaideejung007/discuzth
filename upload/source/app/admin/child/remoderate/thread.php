<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// 主题/帖子标题及内容重新审核

$nextlink = "action=remoderate&current=$next&pertask=$pertask&threadsubmit=yes";
$processed = 0;

$censor = &discuz_censor::instance();

foreach(table_forum_thread::t()->fetch_all_by_displayorder(0, '>=', $current, $pertask) as $thread) {
	$processed = 1;
	foreach(table_forum_post::t()->fetch_all_visiblepost_by_tid($thread['posttableid'], $thread['tid']) as $post) {
		$subject = $post['subject'];
		$message = $post['message'];
		$subject_result = empty($subject) ? 0 : $censor->check($subject);
		$message_result = (in_array($subject_result, [1, 2]) || empty($message)) ? 0 : $censor->check($message);
		if($subject_result) {
			if($subject_result == 3) {
				if(strcmp($post['subject'], $subject)) {
					table_forum_post::t()->update($thread['posttableid'], $post['pid'], ['subject' => $subject], false, false, null, -2, null, 0);
				}
			} else {
				if($post['first'] == 1) {
					table_forum_thread::t()->update($thread['tid'], ['displayorder' => -2]);
					updatemoderate('tid', $thread['tid']);
				} else {
					updatemoderate('pid', $post['pid']);
				}
			}
		}
		if($message_result) {
			if($message_result == 3) {
				if(strcmp($post['message'], $message)) {
					table_forum_post::t()->update($thread['posttableid'], $post['pid'], ['message' => $message], false, false, null, -2, null, 0);
				}
			} else {
				if($post['first'] == 1) {
					table_forum_thread::t()->update($thread['tid'], ['displayorder' => -2]);
					updatemoderate('tid', $thread['tid']);
				} else {
					updatemoderate('pid', $post['pid']);
				}
			}
		}
	}
}

if($processed) {
	cpmsg("{$lang['remoderate_thread']}: ".cplang('remoderate_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('remoderate_thread_succeed', 'action=remoderate', 'succeed');
}
	