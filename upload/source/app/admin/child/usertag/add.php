<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('submit')) {
	$uids = $tagarray = [];
	if($_GET['usernames']) {
		$_GET['usernames'] = trim(preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/", "\r\n", $_GET['usernames']));
		$_GET['usernames'] = explode("\r\n", $_GET['usernames']);
		$uids = table_common_member::t()->fetch_all_uid_by_username($_GET['usernames']);
	}
	if(empty($_GET['usernames']) || $uids) {
		$class_tag = new tag();
		$tagarray = $class_tag->add_tag($_GET['tags'], 0, 'uid', 1);
	}

	if($uids && $tagarray) {
		foreach($uids as $uid) {
			if(empty($uid)) continue;
			foreach($tagarray as $tagid => $tagname) {
				table_common_tagitem::t()->insert(['tagid' => $tagid, 'itemid' => $uid, 'idtype' => 'uid', 'created_at' => TIMESTAMP], 0, 1);
				helper_forumperm::clear_cache($uid);
			}
		}
		foreach($tagarray as $tagid => $tagname) {
			$count = table_common_tagitem::t()->count_by_tagid($tagid);
			$updates[$tagid] = $count;
		}
		$nums = renum($updates);
		foreach($nums[0] as $count) {
			table_common_tag::t()->update($nums[1][$count], ['related_count' => $count]);
		}
		cpmsg('usertag_add_succeed', 'action=usertag&operation=add', 'succeed');
	} else {
		if($tagarray && empty($_GET['usernames'])) {
			cpmsg('usertag_add_tag_succeed', 'action=usertag&operation=add', 'succeed');
		} else {
			cpmsg('usertag_add_error', 'action=usertag&operation=add', 'error');
		}
	}

}
/*search={"usertag":"action=usertag"}*/
showsubmenu('usertag', [
	['usertag_list', 'usertag', 0],
	['usertag_add', 'usertag&operation=add', 1],
]);
showtips('usertag_add_tips');
showformheader('usertag&operation=add');
showtableheader();
showsetting('usertag_add_tags', 'tags', '', 'text');
showsetting('usertag_add_usernames', 'usernames', '', 'textarea');
showsubmit('submit', 'submit');
showtablefooter();
showformfooter();
/*search*/
	