<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// 文章标题及内容重新审核

$nextlink = "action=remoderate&current=$next&pertask=$pertask&articlesubmit=yes";
$processed = 0;

$censor = &discuz_censor::instance();

foreach(table_portal_article_title::t()->fetch_all_by_sql('`status` = 0', '', $current, $pertask) as $article) {
	$processed = 1;
	$subject = $article['subject'];
	$subject_result = empty($subject) ? 0 : $censor->check($subject);
	if($subject_result) {
		if($subject_result == 3) {
			if(strcmp($article['subject'], $subject)) {
				table_portal_article_title::t()->update($article['aid'], ['message' => $subject]);
			}
		} else {
			table_portal_article_title::t()->update($article['aid'], ['status' => 1]);
			updatemoderate('aid', $article['aid']);
		}
	}
	if(in_array($subject_result, [0, 3])) {
		foreach(table_portal_article_content::t()->fetch_all($article['aid']) as $post) {
			$content = $post['content'];
			$content_result = empty($content) ? 0 : $censor->check($content);
			if($content_result) {
				if($content_result == 3) {
					if(strcmp($post['content'], $content)) {
						table_portal_article_content::t()->update($post['cid'], ['content' => $content]);
					}
				} else {
					table_portal_article_title::t()->update($article['aid'], ['status' => 1]);
					updatemoderate('aid', $article['aid']);
					break;
				}
			}
		}
	}
}

if($processed) {
	cpmsg("{$lang['remoderate_article']}: ".cplang('remoderate_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('remoderate_article_succeed', 'action=remoderate', 'succeed');
}
	