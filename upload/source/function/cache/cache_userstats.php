<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_userstats() {
	global $_G;
	$totalmembers = table_common_member::t()->count();
	$member = table_common_member::t()->range(0, 1, 'DESC');
	$member = current($member);
	$newsetuser = $member['username'];
	$data = ['totalmembers' => $totalmembers, 'newsetuser' => $newsetuser];
	if($_G['setting']['plugins']['func'][HOOKTYPE]['cacheuserstats']) {
		$_G['userstatdata'] = &$data;
		hookscript('cacheuserstats', 'global', 'funcs', [], 'cacheuserstats');
	}
	savecache('userstats', $data);
}

