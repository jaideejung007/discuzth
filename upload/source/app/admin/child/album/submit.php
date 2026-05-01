<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($_GET['albumids']) {
	$albumids = authcode($_GET['albumids'], 'DECODE');
	$albumidsadd = $albumids ? explode(',', $albumids) : $_GET['delete'];
	include_once libfile('function/delete');
	$deletecount = count(deletealbums($albumidsadd));
	$cpmsg = cplang('album_succeed', ['deletecount' => $deletecount]);
} else {
	$albums = $catids = [];
	$selectalbumids = !empty($_GET['ids']) && is_array($_GET['ids']) ? $_GET['ids'] : [];
	if($selectalbumids) {
		$query = table_home_album::t()->fetch_all_album($selectalbumids);
		foreach($query as $value) {
			$albums[$value['albumid']] = $value;
			$catids[] = intval($value['catid']);
		}
	}
	if($albums) {
		$selectalbumids = array_keys($albums);
		if($_POST['optype'] == 'delete') {
			include_once libfile('function/delete');
			$deletecount = count(deletealbums($selectalbumids));
			$cpmsg = cplang('album_succeed', ['deletecount' => $deletecount]);
		} elseif($_POST['optype'] == 'move') {
			$tocatid = intval($_POST['tocatid']);
			$catids[] = $tocatid;
			$catids = array_merge($catids);
			table_home_album::t()->update($selectalbumids, ['catid' => $tocatid]);
			foreach($catids as $catid) {
				$catid = intval($catid);
				$cnt = table_home_album::t()->count_by_catid($catid);
				table_home_album_category::t()->update($catid, ['num' => intval($cnt)]);
			}
			$cpmsg = cplang('album_move_succeed');
		} else {
			$cpmsg = cplang('album_choose_at_least_one_operation');
		}
	} else {
		$cpmsg = cplang('album_choose_at_least_one_album');
	}
}

echo '<script type="text/JavaScript">alert(\''.$cpmsg.'\');parent.$(\'albumforum\').searchsubmit.click();</script>';
	