<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!$_GET['catid'] || !$category[$_GET['catid']]) {
	cpmsg('blogcategory_catgory_not_found', '', 'error');
}
if(!submitcheck('deletesubmit')) {
	$blog_count = table_home_blog::t()->count_by_catid($_GET['catid']);
	if(!$blog_count && empty($category[$_GET['catid']]['children'])) {
		table_home_blog_category::t()->delete($_GET['catid']);
		include_once libfile('function/cache');
		updatecache('blogcategory');
		cpmsg('blogcategory_delete_succeed', 'action=blogcategory', 'succeed');
	}

	shownav('portal', 'blogcategory');
	showsubmenu('blogcategory', [
		['list', 'blogcategory', 0],
		['delete', 'blogcategory&operation=delete&catid='.$_GET['catid'], 1]
	]);

	showformheader('blogcategory&operation=delete&catid='.$_GET['catid']);
	showtableheader();
	if($category[$_GET['catid']]['children']) {
		showsetting('blogcategory_subcategory_moveto', '', '',
			'<input type="radio" name="subcat_op" value="trash" id="subcat_op_trash" checked="checked" />'.
			'<label for="subcat_op_trash" />'.cplang('blogcategory_subcategory_moveto_trash').'</label>'.
			'<input type="radio" name="subcat_op" value="parent" id="subcat_op_parent" checked="checked" />'.
			'<label for="subcat_op_parent" />'.cplang('blogcategory_subcategory_moveto_parent').'</label>'
		);
	}
	include_once libfile('function/portalcp');
	showsetting('blogcategory_blog_moveto', '', '', category_showselect('blog', 'tocatid', false, $category[$_GET['catid']]['upid']));
	showsubmit('deletesubmit');
	showtablefooter();
	showformfooter();

} else {

	if($_POST['tocatid'] == $_GET['catid']) {
		cpmsg('blogcategory_move_category_failed', 'action=blogcategory', 'error');
	}
	$delids = [$_GET['catid']];
	if($category[$_GET['catid']]['children']) {
		if($_POST['subcat_op'] == 'parent') {
			$upid = intval($category[$_GET['catid']]['upid']);
			table_home_blog_category::t()->update($category[$_GET['catid']]['children'], ['upid' => $upid]);
		} else {
			$delids = array_merge($delids, $category[$_GET['catid']]['children']);
			foreach($category[$_GET['catid']]['children'] as $id) {
				$value = $category[$id];
				if($value['children']) {
					$delids = array_merge($delids, $value['children']);
				}
			}
			if(!$category[$_POST['tocatid']] || in_array($_POST['tocatid'], $delids)) {
				cpmsg('blogcategory_move_category_failed', 'action=blogcategory', 'error');
			}
		}
	}
	if($delids) {
		table_home_blog_category::t()->delete($delids);
		table_home_blog::t()->update_by_catid($delids, ['catid' => $_POST['tocatid']]);
		$num = table_home_blog::t()->count_by_catid($_POST['tocatid']);
		table_home_blog_category::t()->update_num_by_catid($num, $_POST['tocatid'], false);
	}

	include_once libfile('function/cache');
	updatecache('blogcategory');

	cpmsg('blogcategory_delete_succeed', 'action=blogcategory', 'succeed');
}
	