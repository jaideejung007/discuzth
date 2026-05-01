<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// 专题评论内容重新审核

$nextlink = "action=remoderate&current=$next&pertask=$pertask&topiccommentsubmit=yes";
$processed = 0;

$censor = &discuz_censor::instance();

foreach(table_portal_comment::t()->fetch_all_by_idtype_status('topicid', 0, $current, $pertask) as $comment) {
	$processed = 1;
	$comment = $comment['message'];
	$comment_result = empty($comment) ? 0 : $censor->check($comment);
	if($comment_result) {
		if($comment_result == 3) {
			if(strcmp($comment['message'], $comment)) {
				table_portal_comment::t()->update($comment['cid'], ['message' => $comment]);
			}
		} else {
			table_portal_comment::t()->update($comment['cid'], ['status' => 1]);
			updatemoderate($comment['idtype'].'_cid', $comment['cid']);
		}
	}
}

if($processed) {
	cpmsg("{$lang['remoderate_topiccomment']}: ".cplang('remoderate_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('remoderate_topiccomment_succeed', 'action=remoderate', 'succeed');
}
	