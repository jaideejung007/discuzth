<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/threadsort');

threadsort_checkoption($sortid);
$forum_optionlist = getsortedoptionlist();

loadcache(['threadsort_option_'.$sortid, 'threadsort_template_'.$sortid]);
$sqlarr = [];
foreach($_G['cache']['threadsort_option_'.$sortid] as $key => $val) {
	if($val['profile']) {
		$sqlarr[] = $val['profile'];
	}
}
if($sqlarr) {
	$member_profile = [];
	$_member_profile = table_common_member_profile::t()->fetch($_G['uid']);
	foreach($sqlarr as $val) {
		$member_profile[$val] = $_member_profile[$val];
	}
	unset($_member_profile);
}
threadsort_optiondata($pid, $sortid, $_G['cache']['threadsort_option_'.$sortid], $_G['cache']['threadsort_template_'.$sortid]);


