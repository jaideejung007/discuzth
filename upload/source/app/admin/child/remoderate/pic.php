<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// 图片标题重新审核

$nextlink = "action=remoderate&current=$next&pertask=$pertask&picsubmit=yes";
$processed = 0;

$censor = &discuz_censor::instance();

foreach(table_home_pic::t()->fetch_all_by_sql('`status` = 0', 'picid', $current, $pertask, 0, 0) as $pic) {
	$processed = 1;
	$title = $pic['title'];
	$title_result = empty($title) ? 0 : $censor->check($title);
	if($title_result) {
		if($title_result == 3) {
			if(strcmp($pic['title'], $title)) {
				table_home_pic::t()->update($pic['picid'], ['title' => $title]);
			}
		} else {
			table_home_pic::t()->update($pic['picid'], ['status' => 1]);
			updatemoderate('picid', $pic['picid']);
		}
	}
}

if($processed) {
	cpmsg("{$lang['remoderate_pic']}: ".cplang('remoderate_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('remoderate_pic_succeed', 'action=remoderate', 'succeed');
}
	