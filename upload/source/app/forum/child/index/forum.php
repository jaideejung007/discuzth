<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$announcements = get_index_announcements();

$forums = table_forum_forum::t()->fetch_all_by_status(1);
$fids = [];
foreach($forums as $forum) {
	$fids[$forum['fid']] = $forum['fid'];
}

$forum_access = [];
if(!empty($_G['member']['accessmasks'])) {
	$forum_access = table_forum_access::t()->fetch_all_by_fid_uid($fids, $_G['uid']);
}

$forum_fields = table_forum_forumfield::t()->fetch_all($fids);

foreach($forums as $forum) {
	if($forum_fields[$forum['fid']]['fid']) {
		$forum = (array_key_exists('fid', $forum) && array_key_exists($forum['fid'], $forum_fields)) ? array_merge($forum, $forum_fields[$forum['fid']]) : $forum;
	}
	if(!empty($forum_access['fid'])) {
		$forum = (array_key_exists('fid', $forum) && array_key_exists($forum['fid'], $forum_access)) ? array_merge($forum, $forum_access[$forum['fid']]) : $forum;
	}
	$forumname[$forum['fid']] = strip_tags($forum['name']);
	$forum['extra'] = empty($forum['extra']) ? [] : dunserialize($forum['extra']);
	if(!is_array($forum['extra'])) {
		$forum['extra'] = [];
	}

	if($forum['type'] != 'group') {

		$threads += $forum['threads'];
		$posts += $forum['posts'];
		$todayposts += $forum['todayposts'];

		if($forum['type'] == 'forum' && isset($catlist[$forum['fup']])) {
			if(forum($forum)) {
				$catlist[$forum['fup']]['forums'][] = $forum['fid'];
				$forum['orderid'] = $catlist[$forum['fup']]['forumscount']++;
				if(!defined('IN_RESTFUL_API')) {
					$forum['subforums'] = '';
				} else {
					$forum['subforums'] = [];
				}
				$forumlist[$forum['fid']] = $forum;
			}

		} elseif(isset($forumlist[$forum['fup']])) {

			$forumlist[$forum['fup']]['threads'] += $forum['threads'];
			$forumlist[$forum['fup']]['posts'] += $forum['posts'];
			$forumlist[$forum['fup']]['todayposts'] += $forum['todayposts'];
			if($_G['setting']['subforumsindex'] && $forumlist[$forum['fup']]['permission'] == 2 && !($forumlist[$forum['fup']]['simple'] & 16) || ($forumlist[$forum['fup']]['simple'] & 8)) {
				$forumurl = !empty($forum['domain']) && !empty($_G['setting']['domain']['root']['forum']) ? $_G['scheme'].'://'.$forum['domain'].'.'.$_G['setting']['domain']['root']['forum'] : 'forum.php?mod=forumdisplay&fid='.$forum['fid'];
				$forumlist[$forum['fup']]['subforums'] .= (empty($forumlist[$forum['fup']]['subforums']) ? '' : ', ').'<a href="'.$forumurl.'" '.(!empty($forum['extra']['namecolor']) ? ' style="color: '.$forum['extra']['namecolor'].';"' : '').'>'.$forum['name'].'</a>';
			}
			if(defined('IN_RESTFUL_API')) {
				$subforum = [];
				$subforum['fid'] = $forum['fid'];
				$subforum['fup'] = $forum['fup'];
				$subforum['type'] = $forum['type'];
				$subforum['name'] = $forum['name'];
				$subforum['displayorder'] = $forum['displayorder'];
				$subforum['threads'] = $forum['threads'];
				$subforum['posts'] = $forum['posts'];
				$subforum['todayposts'] = $forum['todayposts'];
				$subforum['yesterdayposts'] = $forum['yesterdayposts'];
				$subforum['iconUri'] = $forum['icon'];
				$forumlist[$forum['fup']]['subforums'][] = $subforum;
			}
		}

	} else {

		if($forum['moderators']) {
			$forum['moderators'] = moddisplay($forum['moderators'], 'flat');
		}
		$forum['forumscount'] = 0;
		$catlist[$forum['fid']] = $forum;

	}
}
unset($forum_access, $forum_fields);

foreach($catlist as $catid => $category) {
	$catlist[$catid]['collapseimg'] = 'collapsed_no.gif';
	$catlist[$catid]['collapseicon'] = '_no';
	if($catlist[$catid]['forumscount'] && $category['forumcolumns']) {
		$catlist[$catid]['forumcolwidth'] = (floor(100 / $category['forumcolumns']) - 0.1).'%';
		$catlist[$catid]['endrows'] = '';
		if($colspan = $category['forumscount'] % $category['forumcolumns']) {
			while(($category['forumcolumns'] - $colspan) > 0) {
				$catlist[$catid]['endrows'] .= '<td width="'.$catlist[$catid]['forumcolwidth'].'">&nbsp;</td>';
				$colspan++;
			}
		}
	} elseif(empty($category['forumscount'])) {
		unset($catlist[$catid]);
	}
}
unset($catid, $category);

if(isset($catlist[0]) && $catlist[0]['forumscount']) {
	$catlist[0]['fid'] = 0;
	$catlist[0]['type'] = 'group';
	$catlist[0]['name'] = $_G['setting']['bbname'];
	$catlist[0]['collapseimg'] = 'collapsed_no.gif';
	$catlist[0]['collapseicon'] = '_no';
} else {
	unset($catlist[0]);
}

if(!IS_ROBOT && ($_G['setting']['whosonlinestatus'] == 1 || $_G['setting']['whosonlinestatus'] == 3)) {
	$_G['setting']['whosonlinestatus'] = 1;

	$onlineinfo = $_G['cache']['onlinerecord'] ? explode("\t", $_G['cache']['onlinerecord']) : [0, TIMESTAMP];
	if(empty($_G['cookie']['onlineusernum'])) {
		$onlinenum = C::app()->session->count();
		if($onlinenum > $onlineinfo[0]) {
			$onlinerecord = "$onlinenum\t".TIMESTAMP;
			table_common_setting::t()->update_setting('onlinerecord', $onlinerecord);
			savecache('onlinerecord', $onlinerecord);
			$onlineinfo = [$onlinenum, TIMESTAMP];
		}
		dsetcookie('onlineusernum', intval($onlinenum), 300);
	} else {
		$onlinenum = intval($_G['cookie']['onlineusernum']);
	}
	$onlineinfo[1] = dgmdate($onlineinfo[1], 'd');

	$detailstatus = $showoldetails == 'yes' || (((!isset($_G['cookie']['onlineindex']) && !$_G['setting']['whosonline_contract']) || $_G['cookie']['onlineindex']) && $onlinenum < 500 && !$showoldetails);

	$guestcount = $membercount = $invisiblecount = 0;
	if(!empty($_G['setting']['sessionclose'])) {
		$detailstatus = false;
		$membercount = C::app()->session->count(1);
		$guestcount = $onlinenum - $membercount;
	}

	if($detailstatus) {
		$actioncode = lang('action');

		$_G['uid'] && updatesession();
		$whosonline = [];

		$_G['setting']['maxonlinelist'] = $_G['setting']['maxonlinelist'] ? $_G['setting']['maxonlinelist'] : 500;
		foreach(C::app()->session->fetch_member(1, 0, $_G['setting']['maxonlinelist']) as $online) {
			$membercount++;
			if($online['invisible']) {
				$invisiblecount++;
				continue;
			} else {
				$online['icon'] = !empty($_G['cache']['onlinelist'][$online['groupid']]) ? $_G['cache']['onlinelist'][$online['groupid']] : $_G['cache']['onlinelist'][0];
			}
			$online['lastactivity'] = dgmdate($online['lastactivity'], 't');
			$whosonline[] = $online;
		}
		if(isset($_G['cache']['onlinelist'][7]) && $_G['setting']['maxonlinelist'] > $membercount) {
			foreach(C::app()->session->fetch_member(2, 0, $_G['setting']['maxonlinelist'] - $membercount) as $online) {
				$online['icon'] = $_G['cache']['onlinelist'][7];
				$online['username'] = $_G['cache']['onlinelist']['guest'];
				$online['lastactivity'] = dgmdate($online['lastactivity'], 't');
				$whosonline[] = $online;
			}
		}
		unset($actioncode, $online);

		if($onlinenum > $_G['setting']['maxonlinelist']) {
			$membercount = C::app()->session->count(1);
			$invisiblecount = C::app()->session->count_invisible();
		}

		if($onlinenum < $membercount) {
			$onlinenum = C::app()->session->count();
			dsetcookie('onlineusernum', intval($onlinenum), 300);
		}

		$invisiblecount = intval($invisiblecount);
		$guestcount = $onlinenum - $membercount;

		unset($online);
	}

} else {
	$_G['setting']['whosonlinestatus'] = 0;
}

if(defined('FORUM_INDEX_PAGE_MEMORY') && !FORUM_INDEX_PAGE_MEMORY) {
	$key = !IS_ROBOT ? $_G['member']['groupid'] : 'for_robot';
	memory('set', 'forum_index_page_'.$key, [
		'catlist' => $catlist,
		'forumlist' => $forumlist,
		'sublist' => $sublist,
		'whosonline' => $whosonline,
		'onlinenum' => $onlinenum,
		'membercount' => $membercount,
		'guestcount' => $guestcount,
		'grids' => $grids,
		'announcements' => $announcements,
		'threads' => $threads,
		'posts' => $posts,
		'todayposts' => $todayposts,
		'onlineinfo' => $onlineinfo,
		'announcepm' => $announcepm], getglobal('setting/memory/forumindex'));
}
	