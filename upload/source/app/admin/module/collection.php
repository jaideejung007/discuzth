<?php
/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
cpheader();
$operation = in_array($operation, ['admin', 'comment', 'recommend']) ? $operation : 'admin';
$current = [$operation => 1];
$fromumanage = $_GET['fromumanage'] ? 1 : 0;
shownav('global', 'collection');
showsubmenu('collection', [
	['collection_admin', 'collection&operation=admin', $current['admin']],
	['collection_comment', 'collection&operation=comment', $current['comment']],
	['collection_recommend', 'collection&operation=recommend', $current['recommend']]
]);

echo '<script src="'.STATICURL.'js/calendar.js"></script>';

$file = childfile('collection/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;

function removeNonExistsCollection($collectionrecommend) {
	$tmpcollection = table_forum_collection::t()->fetch_all(array_keys($collectionrecommend));
	foreach($collectionrecommend as $ctid => $setcollection) {
		if(!$tmpcollection[$ctid]) {
			unset($collectionrecommend[$ctid]);
		}
	}
	return $collectionrecommend;
}

