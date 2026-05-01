<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/forumlist');

$gid = intval(getgpc('gid'));
$showoldetails = get_index_online_details();

if(!$_G['uid'] && !$gid && $_G['setting']['cacheindexlife'] && !defined('IN_ARCHIVER') && !defined('IN_MOBILE') && !IS_ROBOT) {
	get_index_page_guest_cache();
}

$newthreads = round((TIMESTAMP - $_G['member']['lastvisit'] + 600) / 1000) * 1000;

$catlist = $forumlist = $sublist = $forumname = $collapse = $favforumlist = [];
$threads = $posts = $todayposts = $announcepm = 0;
$postdata = $_G['cache']['historyposts'] ? explode("\t", $_G['cache']['historyposts']) : [0, 0];
$postdata[0] = intval($postdata[0]);
$postdata[1] = intval($postdata[1]);
list($navtitle, $metadescription, $metakeywords) = get_seosetting('forum');
if(!$navtitle) {
	$navtitle = $_G['setting']['navs'][2]['navname'];
	$nobbname = false;
} else {
	$nobbname = true;
}
if(!$metadescription) {
	$metadescription = $navtitle;
}
if(!$metakeywords) {
	$metakeywords = $navtitle;
}

if($_G['setting']['indexhot']['status'] && $_G['cache']['heats']['expiration'] < TIMESTAMP) {
	require_once libfile('function/cache');
	updatecache('heats');
}

if($_G['uid'] && empty($_G['cookie']['nofavfid'])) {
	require_once childfile('favorite');
}


if(!$gid && $_G['setting']['collectionstatus'] && ($_G['setting']['collectionrecommendnum'] || !$_G['setting']['hidefollowcollection'])) {
	require_once childfile('collection');
}

if(empty($gid) && empty($_G['member']['accessmasks']) && empty($showoldetails) && !IS_ROBOT) {
	$memindex = get_index_memory_by_groupid($_G['member']['groupid']);
	extract($memindex);
	if(defined('FORUM_INDEX_PAGE_MEMORY') && FORUM_INDEX_PAGE_MEMORY) {
		categorycollapse();
		if(!defined('IN_ARCHIVER')) {
			include template('diy:forum/discuz');
		} else {
			include loadarchiver('forum/discuz');
		}
		dexit();
	}
}

$grids = [];
if($_G['setting']['grid']['showgrid']) {
	require_once childfile('grid');
}

if(!$gid && (!defined('FORUM_INDEX_PAGE_MEMORY') || !FORUM_INDEX_PAGE_MEMORY)) {
	require_once childfile('forum');
} else {
	require_once childfile('category');
}

if(defined('IN_ARCHIVER')) {
	include loadarchiver('forum/discuz');
	exit();
}
categorycollapse();

if($gid && !empty($catlist)) {
	$_G['category'] = $catlist[$gid];
	$forumseoset = [
		'seotitle' => $catlist[$gid]['seotitle'],
		'seokeywords' => $catlist[$gid]['keywords'],
		'seodescription' => $catlist[$gid]['seodescription']
	];
	$seodata = ['fgroup' => $catlist[$gid]['name']];
	list($navtitle, $metadescription, $metakeywords) = get_seosetting('threadlist', $seodata, $forumseoset);
	if(empty($navtitle)) {
		$navtitle = $navtitle_g;
		$nobbname = false;
	} else {
		$nobbname = true;
	}
	$_G['fid'] = $gid;
}

include template('diy:forum/discuz:'.$gid);

function get_index_announcements() {
	global $_G;
	$announcements = '';
	if($_G['cache']['announcements']) {
		$readapmids = !empty($_G['cookie']['readapmid']) ? explode('D', $_G['cookie']['readapmid']) : [];
		foreach($_G['cache']['announcements'] as $announcement) {
			if(!$announcement['endtime'] || $announcement['endtime'] > TIMESTAMP && (empty($announcement['groups']) || in_array($_G['member']['groupid'], $announcement['groups']))) {
				if(empty($announcement['type'])) {
					$announcements .= '<li><span><a href="forum.php?mod=announcement&id='.$announcement['id'].'" target="_blank" class="xi2">'.$announcement['subject'].
						'</a></span><em>('.dgmdate($announcement['starttime'], 'd').')</em></li>';
				} elseif($announcement['type'] == 1) {
					$announcements .= '<li><span><a href="'.$announcement['message'].'" target="_blank" class="xi2">'.$announcement['subject'].
						'</a></span><em>('.dgmdate($announcement['starttime'], 'd').')</em></li>';
				}
			}
		}
	}
	return $announcements;
}

function get_index_page_guest_cache() {
	global $_G;
	$indexcache = getcacheinfo(0);
	if(TIMESTAMP - $indexcache['filemtime'] > $_G['setting']['cacheindexlife']) {
		@unlink($indexcache['filename']);
		define('CACHE_FILE', $indexcache['filename']);
	} elseif($indexcache['filename']) {
		$start_time = microtime(TRUE);
		$filemtime = $indexcache['filemtime'];
		ob_start(function($input) use (&$filemtime) {
			return replace_formhash($filemtime, $input);
		});
		readfile($indexcache['filename']);
		$updatetime = dgmdate($filemtime, 'Y-m-d H:i:s');
		$debuginfo = ", Updated at $updatetime";
		if(getglobal('setting/debug')) {
			$gzip = $_G['gzipcompress'] ? ', Gzip On' : '';
			$debuginfo .= ', Processed in '.sprintf('%0.6f', microtime(TRUE) - $start_time).' second(s)'.$gzip;
		}
		echo '<script type="text/javascript">$("debuginfo") ? $("debuginfo").innerHTML = "'.$debuginfo.'." : "";</script></body></html>';
		ob_end_flush();
		exit();
	}
}

function get_index_memory_by_groupid($key) {
	$enable = getglobal('setting/memory/forumindex');
	if($enable !== null && memory('check')) {
		if(IS_ROBOT) {
			$key = 'for_robot';
		}
		$ret = memory('get', 'forum_index_page_'.$key);
		define('FORUM_INDEX_PAGE_MEMORY', $ret ? 1 : 0);
		if($ret) {
			return $ret;
		}
	}
	return ['none' => null];
}

function get_index_online_details() {
	$showoldetails = getgpc('showoldetails');
	switch($showoldetails) {
		case 'no':
			dsetcookie('onlineindex', '');
			break;
		case 'yes':
			dsetcookie('onlineindex', 1, 86400 * 365);
			break;
	}
	return $showoldetails;
}

function do_forum_bind_domains() {
	global $_G;
	if($_G['setting']['binddomains'] && $_G['setting']['forumdomains']) {
		$loadforum = isset($_G['setting']['binddomains'][$_SERVER['HTTP_HOST']]) ? max(0, intval($_G['setting']['binddomains'][$_SERVER['HTTP_HOST']])) : 0;
		if($loadforum) {
			dheader('Location: '.$_G['setting']['siteurl'].'/forum.php?mod=forumdisplay&fid='.$loadforum);
		}
	}
}

function categorycollapse() {
	global $_G, $collapse, $catlist;
	if(!$_G['uid']) {
		return;
	}
	foreach($catlist as $fid => $forum) {
		if(!isset($_G['cookie']['collapse']) || !str_contains($_G['cookie']['collapse'], '_category_'.$fid.'_')) {
			$catlist[$fid]['collapseimg'] = 'collapsed_no.gif';
			$catlist[$fid]['collapseicon'] = '_no';
			$collapse['category_'.$fid] = '';
		} else {
			$catlist[$fid]['collapseimg'] = 'collapsed_yes.gif';
			$catlist[$fid]['collapseicon'] = '_yes';
			$collapse['category_'.$fid] = 'display: none';
		}
	}

	for($i = -2; $i <= 0; $i++) {
		if(!isset($_G['cookie']['collapse']) || !str_contains($_G['cookie']['collapse'], '_category_'.$i.'_')) {
			$collapse['collapseimg_'.$i] = 'collapsed_no.gif';
			$collapse['collapseicon_'.$i] = '_no';
			$collapse['category_'.$i] = '';
		} else {
			$collapse['collapseimg_'.$i] = 'collapsed_yes.gif';
			$collapse['collapseicon_'.$i] = '_yes';
			$collapse['category_'.$i] = 'display: none';
		}
	}
}

