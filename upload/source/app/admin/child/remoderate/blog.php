<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// 日志标题及内容重新审核

$nextlink = "action=remoderate&current=$next&pertask=$pertask&blogsubmit=yes";
$processed = 0;

$censor = &discuz_censor::instance();

foreach(table_home_blog::t()->range($current, $pertask, 'ASC', 'dateline', null, 0) as $blog) {
	$processed = 1;
	$post = table_home_blogfield::t()->fetch($blog['blogid']);
	$subject = $blog['subject'];
	$message = $post['message'];
	$subject_result = empty($subject) ? 0 : $censor->check($subject);
	$message_result = (in_array($subject_result, [1, 2]) || empty($message)) ? 0 : $censor->check($message);
	if($subject_result) {
		if($subject_result == 3) {
			if(strcmp($blog['subject'], $subject)) {
				table_home_blog::t()->update($blog['blogid'], ['subject' => $subject]);
			}
		} else {
			table_home_blog::t()->update($blog['blogid'], ['status' => 1]);
			updatemoderate('blogid', $blog['blogid']);
		}
	}
	if($message_result) {
		if($message_result == 3) {
			if(strcmp($post['message'], $message)) {
				table_home_blogfield::t()->update($blog['blogid'], ['message' => $message]);
			}
		} else {
			table_home_blog::t()->update($blog['blogid'], ['status' => 1]);
			updatemoderate('blogid', $blog['blogid']);
		}
	}
}

if($processed) {
	cpmsg("{$lang['remoderate_blog']}: ".cplang('remoderate_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('remoderate_blog_succeed', 'action=remoderate', 'succeed');
}
	