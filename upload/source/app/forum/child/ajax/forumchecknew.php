<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(empty($_GET['fid']) || empty($_GET['time'])) {
	include template('common/header_ajax');
	echo 0;
	include template('common/footer_ajax');
	exit;
}

$fid = intval($_GET['fid']);
$time = intval($_GET['time']);

if(!getgpc('uncheck')) {
	$foruminfo = table_forum_forum::t()->fetch($fid);
	$lastpost_str = $foruminfo['lastpost'];
	if($lastpost_str) {
		$lastpost = explode("\t", $lastpost_str);
		unset($lastpost_str);
	}
	include template('common/header_ajax');
	echo $lastpost['2'] > $time ? 1 : 0;
	include template('common/footer_ajax');
	exit;
} else {
	$_G['forum_colorarray'] = ['', '#EE1B2E', '#EE5023', '#996600', '#3C9D40', '#2897C5', '#2B65B7', '#8F2A90', '#EC1282'];
	$query = table_forum_forumfield::t()->fetch($fid);
	$forum_field['threadtypes'] = dunserialize($query['threadtypes']);
	$forum_field['threadsorts'] = dunserialize($query['threadsorts']);

	if($forum_field['threadtypes']['types']) {
		safefilter($forum_field['threadtypes']['types']);
	}
	if($forum_field['threadtypes']['options']['name']) {
		safefilter($forum_field['threadtypes']['options']['name']);
	}
	if($forum_field['threadsorts']['types']) {
		safefilter($forum_field['threadsorts']['types']);
	}

	unset($query);
	$forum_field = daddslashes($forum_field);
	$todaytime = strtotime(dgmdate(TIMESTAMP, 'Ymd'));
	foreach(table_forum_thread::t()->fetch_all_by_fid_lastpost($fid, $time, TIMESTAMP) as $thread) {
		$thread['icontid'] = $thread['forumstick'] || !$thread['moved'] && $thread['isgroup'] != 1 ? $thread['tid'] : $thread['closed'];
		if(!$thread['forumstick'] && ($thread['isgroup'] == 1 || $thread['fid'] != $_G['fid'])) {
			$thread['icontid'] = $thread['closed'] > 1 ? $thread['closed'] : $thread['tid'];
		}
		list($thread['subject'], $thread['author'], $thread['lastposter']) = daddslashes([$thread['subject'], $thread['author'], $thread['lastposter']]);
		$thread['dateline'] = $thread['dateline'] > $todaytime ? "<span class=\"xi1\">".dgmdate($thread['dateline'], 'd').'</span>' : '<span>'.dgmdate($thread['dateline'], 'd').'</span>';
		$thread['lastpost'] = dgmdate($thread['lastpost']);
		if(isset($forum_field['threadtypes']['prefix'])) {
			if($forum_field['threadtypes']['prefix'] == 1) {
				$thread['threadtype'] = $forum_field['threadtypes']['types'][$thread['typeid']] ? '<em>[<a href="forum.php?mod=forumdisplay&fid='.$fid.'&filter=typeid&typeid='.$thread['typeid'].'">'.$forum_field['threadtypes']['types'][$thread['typeid']].'</a>]</em> ' : '';
			} elseif($forum_field['threadtypes']['prefix'] == 2) {
				$thread['threadtype'] = $forum_field['threadtypes']['icons'][$thread['typeid']] ? '<em><a href="forum.php?mod=forumdisplay&fid='.$fid.'&filter=typeid&typeid='.$thread['typeid'].'"><img src="'.$forum_field['threadtypes']['icons'][$thread['typeid']].'"/></a></em> ' : '';
			}
		}
		if(isset($forum_field['threadsorts']['prefix'])) {
			$thread['threadsort'] = $forum_field['threadsorts']['types'][$thread['sortid']] ? '<em>[<a href="forum.php?mod=forumdisplay&fid='.$fid.'&filter=sortid&typeid='.$thread['sortid'].'">'.$forum_field['threadsorts']['types'][$thread['sortid']].'</a>]</em>' : '';
		}
		if($thread['highlight']) {
			$string = sprintf('%02d', $thread['highlight']);
			$stylestr = sprintf('%03b', $string[0]);

			$thread['highlight'] = ' style="';
			$thread['highlight'] .= $stylestr[0] ? 'font-weight: bold;' : '';
			$thread['highlight'] .= $stylestr[1] ? 'font-style: italic;' : '';
			$thread['highlight'] .= $stylestr[2] ? 'text-decoration: underline;' : '';
			$thread['highlight'] .= $string[1] ? 'color: '.$_G['forum_colorarray'][$string[1]].';' : '';
			if($thread['bgcolor']) {
				$thread['highlight'] .= "background-color: $thread[bgcolor];";
			}
			$thread['highlight'] .= '"';
		} else {
			$thread['highlight'] = '';
		}
		$target = $thread['isgroup'] == 1 || $thread['forumstick'] ? ' target="_blank"' : ' onclick="atarget(this)"';
		if(rewriterulecheck('forum_viewthread')) {
			$thread['threadurl'] = '<a href="'.rewriteoutput('forum_viewthread', 1, '', $thread['tid'], 1, '', '').'"'.$thread['highlight'].$target.'class="s xst">'.$thread['subject'].'</a>';
		} else {
			$thread['threadurl'] = '<a href="forum.php?mod=viewthread&amp;tid='.$thread['tid'].'"'.$thread['highlight'].$target.'class="s xst">'.$thread['subject'].'</a>';
		}
		if(rewriterulecheck() && in_array($thread['displayorder'], [1, 2, 3, 4])) {
			$thread['id'] = 'stickthread_'.$thread['tid'];
		} else {
			$thread['id'] = 'normalthread_'.$thread['tid'];
		}
		$thread['threadurl'] = $thread['threadtype'].$thread['threadsort'].$thread['threadurl'];
		if(rewriterulecheck('home_space')) {
			$thread['authorurl'] = '<a href="'.rewriteoutput('home_space', 1, '', $thread['authorid'], '', '').'">'.$thread['author'].'</a>';
			$thread['lastposterurl'] = '<a href="'.rewriteoutput('home_space', 1, '', '', rawurlencode($thread['lastposter']), '').'">'.$thread['lastposter'].'</a>';
		} else {
			$thread['authorurl'] = '<a href="home.php?mod=space&uid='.$thread['authorid'].'">'.$thread['author'].'</a>';
			$thread['lastposterurl'] = '<a href="home.php?mod=space&username='.rawurlencode($thread['lastposter']).'">'.$thread['lastposter'].'</a>';
		}
		$threadlist[] = $thread;
	}
	if($threadlist) {
		krsort($threadlist);
	}
	include template('forum/ajax_threadlist');

}
	