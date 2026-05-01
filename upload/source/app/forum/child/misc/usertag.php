<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_G['tid'] && $_G['group']['alloweditusertag']) {
	if(!submitcheck('addusertag')) {
		$recent_use_tag = $lastlog = $polloptions = [];
		$i = 0;
		$query = table_common_tagitem::t()->select(0, 0, 'uid', 'tagid', 'DESC', 200);
		foreach($query as $result) {
			if($i > 4) {
				break;
			}
			if($recent_use_tag[$result['tagid']] == '') {
				$i++;
			}
			$recent_use_tag[$result['tagid']] = 1;
		}
		if($recent_use_tag) {
			$query = table_common_tag::t()->fetch_all(array_keys($recent_use_tag));
			foreach($query as $result) {
				$recent_use_tag[$result['tagid']] = $result['tagname'];
			}
		}
		foreach(table_forum_threadmod::t()->fetch_all_by_tid($_G['tid'], 'AUT', 3) as $row) {
			$row['dateline'] = dgmdate($row['dateline'], 'u');
			$lastlog[] = $row;
		}
		if($_G['thread']['special'] == 1) {
			$query = table_forum_polloption::t()->fetch_all_by_tid($_G['tid']);
			foreach($query as $polloption) {
				if($polloption['votes'] > 0) {
					$polloptions[] = $polloption;
				}

			}
			if(empty($polloptions)) {
				showmessage('thread_poll_voter_isnull', '', ['haserror' => 1]);
			}
		} elseif($_G['thread']['special'] == 4) {
			$activityapplys = table_forum_activityapply::t()->fetch_all_for_thread($_G['tid'], 0, 1);
			if(empty($activityapplys)) {
				showmessage('thread_activityapply_isnull', '', ['haserror' => 1]);
			}
		}
	} else {
		$class_tag = new tag();
		$tagarray = $class_tag->add_tag($_GET['tags'], 0, 'uid', 1);
		if($tagarray) {
			$uids = [];
			if($_G['thread']['special'] == 1) {
				if($_GET['polloptions']) {
					$query = table_forum_polloption::t()->fetch_all($_GET['polloptions']);
				} else {
					$query = table_forum_polloption::t()->fetch_all_by_tid($_G['tid']);
				}
				$uids = '';
				foreach($query as $row) {
					$uids .= $row['voterids'];
				}
				if($uids) {
					$uids = explode("\t", trim($uids));
				}
			} elseif($_G['thread']['special'] == 4) {
				$query = table_forum_activityapply::t()->fetch_all_for_thread($_G['tid'], 0, 2000);
				foreach($query as $row) {
					$uids[] = $row['uid'];
				}
			} else {
				foreach(table_forum_post::t()->fetch_all_by_tid('tid:'.$_G['tid'], $_G['tid'], false) as $author) {
					$uids[] = $author['authorid'];
				}
			}

			$uids = is_array($uids) ? array_unique($uids) : [];
			$count = count($uids);
			$limit = intval($_GET['limit']);
			$per = 200;
			$uids = array_slice($uids, $limit, $per);
			if($uids) {
				foreach($uids as $uid) {
					if(empty($uid)) continue;
					foreach($tagarray as $tagid => $tagname) {
						table_common_tagitem::t()->insert(['tagid' => $tagid, 'itemid' => $uid, 'idtype' => 'uid'], 0, 1);
					}
				}
				updatemodlog($_G['tid'], 'AUT', 0, 0, implode(',', $tagarray));
				showmessage('forum_usertag_set_continue', '', ['limit' => $limit, 'next' => min($limit + $per, $count), 'count' => $count], ['alert' => 'right']);
			}
			showmessage('forum_usertag_succeed', '', [], ['alert' => 'right']);
		} else {
			showmessage('parameters_error', '', ['haserror' => 1]);
		}
	}

} else {
	showmessage('parameters_error', '', ['haserror' => 1]);
}
include_once template('forum/usertag');
	