<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function getmycollection($uid) {
	$collections = table_forum_collection::t()->fetch_all_by_uid($uid);
	$collectionteamworker = table_forum_collectionteamworker::t()->fetch_all_by_uid($uid);
	return $collections + $collectionteamworker;
}

function getHotCollection($number = 500, $pK = true) {
	$collection = [];
	if($number > 0) {
		$collection = table_forum_collection::t()->range_collection(0, $number, 10, $pK);
		if(!$collection || count($collection) < $number) {
			$collection += table_forum_collection::t()->range_collection(0, $number, null, $pK);
		}
	}
	return $collection;
}

function checkcollectionperm($collection, $uid, $allowteamworker = false) {
	global $_G;
	if($_G['group']['allowmanagecollection'] == 1) {
		return true;
	}
	if($collection['uid'] == $uid) {
		return true;
	}
	if($allowteamworker) {
		$collectionteamworker = table_forum_collectionteamworker::t()->fetch_all_by_ctid($collection['ctid']);
		$collectionteamworker = array_keys($collectionteamworker);

		if(in_array($uid, $collectionteamworker)) {
			return true;
		}
	}
	return false;
}

function processCollectionData($collection, $tf = [], $orderby = '') {
	if(count($collection) <= 0) {
		return [];
	}
	require_once libfile('function/discuzcode');

	foreach($collection as $ctid => &$curvalue) {
		$curvalue['updated'] = ($curvalue['lastupdate'] > $tf[$ctid]['lastvisit']) ? 1 : 0;
		$curvalue['tflastvisit'] = $tf[$ctid]['lastvisit'];
		$curvalue['lastupdate'] = dgmdate($curvalue['lastupdate']);
		$curvalue['dateline'] = dgmdate($curvalue['dateline']);
		$curvalue['lastposttime'] = dgmdate($curvalue['lastposttime']);
		$curvalue['avgrate'] = number_format($curvalue['rate'], 1);
		$curvalue['star'] = imgdisplayrate($curvalue['rate']);
		$curvalue['lastposterhtml'] = rawurlencode($curvalue['lastposter']);
		$curvalue['shortdesc'] = cutstr(strip_tags(discuzcode($curvalue['desc'])), 50);

		$curvalue['arraykeyword'] = parse_keyword($curvalue['keyword'], false, false);
		if($curvalue['arraykeyword']) {
			foreach($curvalue['arraykeyword'] as $kid => $s_keyword) {
				$curvalue['urlkeyword'][$kid] = rawurlencode($s_keyword);
			}
		}

		if($orderby == 'commentnum') {
			$curvalue['displaynum'] = $curvalue['commentnum'];
		} elseif($orderby == 'follownum') {
			$curvalue['displaynum'] = $curvalue['follownum'];
		} else {
			$curvalue['displaynum'] = $curvalue['threadnum'];
		}
	}
	return $collection;
}

function collectionThread(&$threadlist, $foruminfo = false, $lastvisit = null, &$collectiontids = null) {
	global $todaytime;

	if($foruminfo) {
		foreach($threadlist as $thread) {
			$fids[$thread['fid']] = $thread['fid'];
		}
		$foruminfo = table_forum_forum::t()->fetch_all($fids);
	}

	foreach($threadlist as $curtid => &$curvalue) {
		if($lastvisit) {
			$curvalue['reason'] = &$collectiontids[$curtid]['reason'];
			$curvalue['updatedthread'] = $lastvisit !== null && $lastvisit < $curvalue['dateline'] ? 1 : 0;
		}
		if($foruminfo) {
			$curvalue['forumname'] = $foruminfo[$curvalue['fid']]['name'];
		}
		$curvalue['istoday'] = $curvalue['dateline'] > $todaytime ? 1 : 0;
		$curvalue['dbdateline'] = $curvalue['dateline'];
		$curvalue['htmlsubject'] = dhtmlspecialchars($curvalue['subject']);
		$curvalue['cutsubject'] = $curvalue['subject'];
		$curvalue['dateline'] = dgmdate($curvalue['dateline'], 'u', '9999', getglobal('setting/dateformat'));
		$curvalue['dblastpost'] = $curvalue['lastpost'];
		$curvalue['lastpost'] = dgmdate($curvalue['lastpost'], 'u');
		$curvalue['lastposterenc'] = rawurlencode($curvalue['lastposter']);
	}
	if($collectiontids) {
		foreach($collectiontids as $curkey => &$curthread) {
			if(!$threadlist[$curthread['tid']]) {
				unset($collectiontids[$curkey]);
			} else {
				$curthread = $threadlist[$curthread['tid']] + $curthread;
			}
		}
	}
}

function imgdisplayrate($rate) {
	$roundscore = floor($rate);
	return $roundscore;
}

function parse_keyword($keywords, $string = false, $filter = true) {
	if($keywords == '') {
		return $string === true ? '' : [];
	}

	$return = [];

	if($filter === true) {
		$keywords = str_replace([chr(0xa3).chr(0xac), chr(0xa1).chr(0x41), chr(0xef).chr(0xbc).chr(0x8c)], ',', censor($keywords));
	}

	if(strexists($keywords, ',')) {
		$tagarray = array_unique(explode(',', $keywords));
	} else {
		$langcore = lang('core');
		$keywords = str_replace($langcore['fullblankspace'], ' ', $keywords);
		$tagarray = array_unique(explode(' ', $keywords));
	}
	$tagcount = 0;
	foreach($tagarray as $tagname) {
		$tagname = trim($tagname);
		if(preg_match('/^([\x7f-\xff_-]|\w|\s){3,20}$/', $tagname)) {
			$tagcount++;
			$return[] = $tagname;
			if($tagcount > 4) {
				unset($tagarray);
				break;
			}
		}
	}
	if($string === true) {
		$return = implode(',', $return);
	}
	return $return;
}

function uploadCollectionImg($type, $ctid, $width, $height) {
	global $_G;

	if(empty($_FILES[$type]) || $_FILES[$type]['error'] || !$_FILES[$type]['size']) {
		return -1;
	}

	[$w, $h] = getimagesize($_FILES[$type]['tmp_name']);
	if(!$w || !$h) {
		return 0;
	}

	$imgfile = getCollectionImgDir($type, $ctid);
	dmkdir($_G['setting']['attachdir'].dirname($imgfile));

	require_once libfile('class/image');
	$image = new image();
	if(!$image->Thumb($_FILES[$type]['tmp_name'], $imgfile, $width, $height, 2)) {
		return 0;
	}

	if(ftpperm('jpg', filesize($_G['setting']['attachdir'].$imgfile))) {
		if(ftpcmd('upload', $imgfile)) {
			@unlink($_G['setting']['attachdir'].$imgfile);
		}
	}
	return 1;
}

function getCollectionImgDir($type, $ctid) {
	return 'forum/collection/'.$type.'/'.substr(md5($ctid), 0, 2).'/'.substr(md5($ctid), 2, 2).'/'.$ctid.'.jpg';
}

function getCollectionImgUrl($type, $ctid) {
	global $_G;

	return $_G['setting']['attachurl'].getCollectionImgDir($type, $ctid);
}