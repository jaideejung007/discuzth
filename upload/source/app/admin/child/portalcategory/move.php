<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$_GET['catid'] = intval($_GET['catid']);
if(!$_GET['catid'] || !$portalcategory[$_GET['catid']]) {
	cpmsg('portalcategory_catgory_not_found', '', 'error');
}
if(!submitcheck('movesubmit')) {
	$article_count = table_portal_article_title::t()->fetch_count_for_cat($_GET['catid']);
	if(!$article_count) {
		cpmsg('portalcategory_move_empty_error', 'action=portalcategory', 'succeed');
	}

	shownav('portal', 'portalcategory');
	showchildmenu([['portalcategory', 'portalcategory']], cplang('portalcategory_move'));

	showformheader('portalcategory&operation=move&catid='.$_GET['catid']);
	showtableheader();
	include_once libfile('function/portalcp');
	showsetting('portalcategory_article_moveto', '', '', category_showselect('portal', 'tocatid', false, $portalcategory[$_GET['catid']]['upid']));
	showsubmit('movesubmit', 'portalcategory_move');
	showtablefooter();
	showformfooter();

} else {

	if($_POST['tocatid'] == $_GET['catid'] || empty($portalcategory[$_POST['tocatid']])) {
		cpmsg('portalcategory_move_category_failed', 'action=portalcategory', 'error');
	}

	table_portal_article_title::t()->update_for_cat($_GET['catid'], ['catid' => $_POST['tocatid']]);
	table_portal_category::t()->update($_GET['catid'], ['articles' => 0]);
	$num = table_portal_article_title::t()->fetch_count_for_cat($_POST['tocatid']);
	table_portal_category::t()->update($_POST['tocatid'], ['articles' => $num]);
	updatecache('portalcategory');

	cpmsg('portalcategory_move_succeed', 'action=portalcategory', 'succeed');
}
	