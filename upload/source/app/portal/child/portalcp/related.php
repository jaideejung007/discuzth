<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$op = in_array($_GET['op'], ['manual', 'search', 'add', 'get']) ? $_GET['op'] : '';
$aid = intval($_GET['aid']);
$catid = intval($_GET['catid']);
if($aid) {
	check_articleperm($catid, $aid);
} else {
	check_articleperm($catid);
}

$wherearr = $articlelist = $relatedarr = [];

if($op == 'manual') {
	$manualid = intval($_GET['manualid']);
	$ra = [];
	if($manualid) {
		$ra = table_portal_article_title::t()->fetch($manualid);
	}
} elseif($op == 'get') {
	$id = trim($_GET['id']);
	$getidarr = explode(',', $id);
	$getidarr = array_map('intval', $getidarr);
	$getidarr = array_unique($getidarr);
	$getidarr = array_filter($getidarr);
	if($getidarr) {
		$list = [];
		$query = table_portal_article_title::t()->fetch_all($getidarr);
		foreach($query as $value) {
			$list[$value['aid']] = $value;
		}
		foreach($getidarr as $getid) {
			if($list[$getid]) {
				$articlelist[] = $list[$getid];
			}
		}
	}
} elseif($op == 'search') {

	$catids = [];
	$searchkey = addslashes(stripsearchkey($_GET['searchkey']));
	$searchcate = intval($_GET['searchcate']);
	$catids = category_get_childids('portal', $searchcate);
	$catids[] = $searchcate;
	if($searchkey) {
		$wherearr[] = "title LIKE '%$searchkey%'";
	}
	$searchkey = dhtmlspecialchars($searchkey);
	if($searchcate) {
		$wherearr[] = 'catid IN  ('.dimplode($catids).')';
	}
	$wheresql = implode(' AND ', $wherearr);
	$count = table_portal_article_title::t()->fetch_all_by_sql($wheresql, '', 0, 0, 1);
	if($count) {
		$query = table_portal_article_title::t()->fetch_all_by_sql($wheresql, 'ORDER BY dateline DESC', 0, 50);
		foreach($query as $value) {
			$articlelist[] = $value;
		}
	}

} elseif($op == 'add') {
	$relatedid = trim($_GET['relatedid']);
	$relatedarr = explode(',', $relatedid);
	$relatedarr = array_map('intval', $relatedarr);
	$relatedarr = array_unique($relatedarr);
	$relatedarr = array_filter($relatedarr);
	if($relatedarr) {
		$query = table_portal_article_title::t()->fetch_all($relatedarr);
		$list = [];
		foreach($query as $value) {
			$list[$value['aid']] = $value;
		}
		foreach($relatedarr as $relateid) {
			if($list[$relateid]) {
				$articlelist[] = $list[$relateid];
			}
		}
	}

	if($_GET['update'] && $aid) {
		addrelatedarticle($aid, $relatedarr);
	}
} else {
	$count = 0;
	$query = table_portal_article_title::t()->range_title(0, 50);
	foreach($query as $value) {
		$articlelist[] = $value;
		$count++;
	}
}
$category = category_showselect('portal', 'searchcate', false, $_GET['searchcate']);
include_once template('portal/portalcp_related_article');
