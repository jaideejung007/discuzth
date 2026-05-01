<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$subexists = 0;
foreach($_G['cache']['forums'] as $sub) {
	if($sub['type'] == 'sub' && $sub['fup'] == $_G['fid'] && (!$_G['setting']['hideprivate'] || !$sub['viewperm'] || forumperm($sub['viewperm']) || strstr($sub['users'], "\t{$_G['uid']}\t"))) {
		if(!$sub['status']) {
			continue;
		}
		$subexists = 1;
		$sublist = [];
		$query = table_forum_forum::t()->fetch_all_info_by_fids(0, 'available', 0, $_G['fid'], 1, 0, 0, 'sub');

		if(!empty($_G['member']['accessmasks'])) {
			$fids = array_keys($query);
			$accesslist = table_forum_access::t()->fetch_all_by_fid_uid($fids, $_G['uid']);
			foreach($query as $key => $val) {
				$query[$key]['allowview'] = $accesslist[$key];
			}
		}
		foreach($query as $sub) {
			$sub['extra'] = dunserialize($sub['extra']);
			if(!is_array($sub['extra'])) {
				$sub['extra'] = [];
			}
			if(forum($sub)) {
				$sub['orderid'] = count($sublist);
				$sublist[] = $sub;
			}
		}
		break;
	}
}
