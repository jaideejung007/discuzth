<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// 记录内容重新审核

$nextlink = "action=remoderate&current=$next&pertask=$pertask&doingsubmit=yes";
$processed = 0;

$censor = &discuz_censor::instance();

foreach(table_home_doing::t()->fetch_all_by_status(0, $current, $pertask) as $doing) {
	$processed = 1;
	$message = $doing['message'];
	$message_result = empty($message) ? 0 : $censor->check($message);
	if($message_result) {
		if($message_result == 3) {
			if(strcmp($doing['message'], $message)) {
				table_home_doing::t()->update($doing['doid'], ['message' => $message]);
			}
		} else {
			table_home_doing::t()->update($doing['doid'], ['status' => 1]);
			updatemoderate('doid', $doing['doid']);
		}
	}
}

if($processed) {
	cpmsg("{$lang['remoderate_doing']}: ".cplang('remoderate_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('remoderate_doing_succeed', 'action=remoderate', 'succeed');
}
	