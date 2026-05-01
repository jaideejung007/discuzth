<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// 分享内容重新审核

$nextlink = "action=remoderate&current=$next&pertask=$pertask&sharesubmit=yes";
$processed = 0;

$censor = &discuz_censor::instance();

foreach(table_home_share::t()->fetch_all_by_status(0, $current, $pertask) as $share) {
	$processed = 1;
	$sharebody = $share['body_general'];
	$sharebody_result = empty($sharebody) ? 0 : $censor->check($sharebody);
	if($sharebody_result) {
		if($sharebody_result == 3) {
			if(strcmp($share['body_general'], $sharebody)) {
				table_home_share::t()->update($share['sid'], ['body_general' => $sharebody]);
			}
		} else {
			table_home_share::t()->update($share['sid'], ['status' => 1]);
			updatemoderate('sid', $share['sid']);
		}
	}
}

if($processed) {
	cpmsg("{$lang['remoderate_share']}: ".cplang('remoderate_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('remoderate_share_succeed', 'action=remoderate', 'succeed');
}
	