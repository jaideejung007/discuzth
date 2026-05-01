<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['collectionstatus']) {
	showmessage('collection_status_off');
}

require_once libfile('function/collection');

$tpp = $_G['setting']['topicperpage']; 
$maxteamworkers = $_G['setting']['collectionteamworkernum'];

$action = trim($_GET['action']);
$ctid = $_GET['ctid'];
$page = $_GET['page'];
$tid = intval($_GET['tid']);
$op = trim($_GET['op']);
$page = $page ? $page : 1;

if(!is_array($ctid)) {
	$ctid = intval($ctid);
	$_G['collection'] = table_forum_collection::t()->fetch($ctid);
	if(!empty($_G['collection']['cover'])) {
		$_G['collection']['cover'] = getCollectionImgUrl('cover', $ctid);
	}
	if(!empty($_G['collection']['icon'])) {
		$_G['collection']['icon'] = getCollectionImgUrl('icon', $ctid);
	}
}

$allowaction = ['index', 'view', 'edit', 'follow', 'comment', 'mycollection', 'all'];

if(!in_array($action, $allowaction)) {
	$action = 'index';
}

require_once childfile($action);

