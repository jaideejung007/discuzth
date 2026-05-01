<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$nextlink = "action=counter&current=$next&pertask=$pertask&friendnum=yes";
if(space_friendnum_stat($current, $pertask)) {
	cpmsg("{$lang['counter_friendnum']}: ".cplang('counter_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('counter_friendnum_succeed', 'action=counter', 'succeed');
}
	