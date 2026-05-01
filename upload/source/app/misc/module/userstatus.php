<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$output = [];
$output['uid'] = $_G['uid'];
$tablename = '';
if($_GET['type'] === 'topic' && $typeid = intval($_GET['typeid'])) {
	$tablename = 'portal_topic';
	table_portal_topic::t()->increase($typeid, ['viewnum' => 1]);
} elseif($_GET['type'] === 'article' && $typeid = intval($_GET['typeid'])) {
	table_portal_article_count::t()->increase($typeid, ['viewnum' => 1]);
	$tablename = 'portal_article_count';
}
if($tablename) {
	$dynamicdata = C::t($tablename)->fetch($typeid);
	$output['viewnum'] = $dynamicdata['viewnum'];
	$output['commentnum'] = $dynamicdata['commentnum'];
}
if($output['uid']) {
	$_G['style']['tplfile'] = 'misc/userstatus';
	if(check_diy_perm($topic)) {
		require template('common/header_diynav');
		echo $diynav;
	}
	$output['diynav'] = str_replace(["\r", "\n"], '', ob_get_contents());
	ob_end_clean();
	$_G['gzipcompress'] ? ob_start('ob_gzhandler') : ob_start();

	require template('common/header_userstatus');
	$output['userstatus'] = str_replace(["\r", "\n"], '', ob_get_contents());
	ob_end_clean();
	$_G['gzipcompress'] ? ob_start('ob_gzhandler') : ob_start();

	require template('common/header_qmenu');
	$output['qmenu'] = str_replace(["\r", "\n"], '', ob_get_contents());
	ob_end_clean();
	$_G['gzipcompress'] ? ob_start('ob_gzhandler') : ob_start();
}

header('Content-Type: application/json');
echo helper_json::encode($output);

