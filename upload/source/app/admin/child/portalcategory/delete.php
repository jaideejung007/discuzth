<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$_GET['catid'] = max(0, intval($_GET['catid']));
if(!$_GET['catid'] || !$portalcategory[$_GET['catid']]) {
	cpmsg('portalcategory_catgory_not_found', '', 'error');
}
$catechildren = $portalcategory[$_GET['catid']]['children'];
include_once libfile('function/cache');
if(!submitcheck('deletesubmit')) {
	$article_count = table_portal_article_title::t()->fetch_count_for_cat($_GET['catid']);
	if(!$article_count && empty($catechildren)) {

		if($portalcategory[$_GET['catid']]['foldername']) delportalcategoryfolder($_GET['catid']);

		deleteportalcategory($_GET['catid']);
		updatecache(['portalcategory', 'diytemplatename']);
		cpmsg('portalcategory_delete_succeed', 'action=portalcategory', 'succeed');
	}

	shownav('portal', 'portalcategory');
	showchildmenu([['portalcategory', 'portalcategory']], cplang('delete'));

	showformheader('portalcategory&operation=delete&catid='.$_GET['catid']);
	showtableheader();
	if($portalcategory[$_GET['catid']]['children']) {
		showsetting('portalcategory_subcategory_moveto', '', '',
			'<div class="nofloat"><input class="radio" type="radio" name="subcat_op" value="trash" id="subcat_op_trash" checked="checked" />'.
			'&nbsp;<label for="subcat_op_trash" />'.cplang('portalcategory_subcategory_moveto_trash').'</label>'.
			'&nbsp;&nbsp;&nbsp;<input class="radio" type="radio" name="subcat_op" value="parent" id="subcat_op_parent" checked="checked" />'.
			'&nbsp;<label for="subcat_op_parent" />'.cplang('portalcategory_subcategory_moveto_parent').'</label></div>'
		);
	}
	include_once libfile('function/portalcp');
	echo "<tr><td colspan=\"2\" class=\"td27\">".cplang('portalcategory_article').":</td></tr>
				<tr class=\"noborder\">
					<td class=\"vtop rowform\">
						<ul class=\"nofloat\" onmouseover=\"altStyle(this);\">
						<li class=\"checked\"><input class=\"radio\" type=\"radio\" name=\"article_op\" value=\"move\" checked />&nbsp;".cplang('portalcategory_article_moveto').'&nbsp;&nbsp;&nbsp;'.category_showselect('portal', 'tocatid', false, $portalcategory[$_GET['catid']]['upid'])."</li>
						<li><input class=\"radio\" type=\"radio\" name=\"article_op\" value=\"delete\" />&nbsp;".cplang('portalcategory_article_delete')."</li>
						</ul></td>
					<td class=\"vtop tips2\"></td>
				</tr>";

	showsubmit('deletesubmit', 'portalcategory_delete');
	showtablefooter();
	showformfooter();

} else {

	if($_POST['article_op'] == 'delete') {
		if(!$_GET['confirmed']) {
			cpmsg('portal_delete_confirm', "action=portalcategory&operation=delete&catid={$_GET['catid']}", 'form', [],
				'<input type="hidden" class="btn" id="deletesubmit" name="deletesubmit" value="1" /><input type="hidden" class="btn" id="subcat_op" name="subcat_op" value="'.$_POST['subcat_op'].'" />
					<input type="hidden" class="btn" id="article_op" name="article_op" value="delete" /><input type="hidden" class="btn" id="tocatid" name="tocatid" value="'.$_POST['tocatid'].'" />');
		}
	}

	if($_POST['article_op'] == 'move') {
		if($_POST['tocatid'] == $_GET['catid'] || empty($portalcategory[$_POST['tocatid']])) {
			cpmsg('portalcategory_move_category_failed', 'action=portalcategory', 'error');
		}
	}

	$delids = [$_GET['catid']];
	$updatecategoryfile = [];
	if($catechildren) {
		if($_POST['subcat_op'] == 'parent') {
			$upid = intval($portalcategory[$_GET['catid']]['upid']);
			if(!empty($portalcategory[$upid]['foldername']) || ($portalcategory[$_GET['catid']]['level'] == '0' && $portalcategory[$_GET['catid']]['foldername'])) {
				$parentdir = DISCUZ_ROOT_STATIC.'/'.getportalcategoryfulldir($upid);
				foreach($catechildren as $subcatid) {
					if($portalcategory[$subcatid]['foldername']) {
						$olddir = DISCUZ_ROOT_STATIC.'/'.getportalcategoryfulldir($subcatid);
						rename($olddir, $parentdir.$portalcategory[$subcatid]['foldername']);
						$updatecategoryfile[] = $subcatid;
					}
				}
			}
			table_portal_category::t()->update($catechildren, ['upid' => $upid]);
			require_once libfile('class/blockpermission');
			require_once libfile('class/portalcategory');
			$tplpermission = &template_permission::instance();
			$tplpermission->delete_perm_by_inheritedtpl('portal/list_'.$_GET['catid']);
			$categorypermission = &portal_category::instance();
			$categorypermission->delete_perm_by_inheritedcatid($_GET['catid']);

		} else {
			$delids = array_merge($delids, $catechildren);
			foreach($catechildren as $id) {
				$value = $portalcategory[$id];
				if($value['children']) {
					$delids = array_merge($delids, $value['children']);
				}
			}
			if($_POST['article_op'] == 'move') {
				if(!$portalcategory[$_POST['tocatid']] || in_array($_POST['tocatid'], $delids)) {
					cpmsg('portalcategory_move_category_failed', 'action=portalcategory', 'error');
				}
			}
		}
	}

	if($delids) {
		deleteportalcategory($delids);
		if($_POST['article_op'] == 'delete') {
			require_once libfile('function/delete');
			$aidarr = [];
			$query = table_portal_article_title::t()->fetch_all_for_cat($delids);
			foreach($query as $value) {
				$aidarr[] = $value['aid'];
			}
			if($aidarr) {
				deletearticle($aidarr, '0');
			}
		} else {
			table_portal_article_title::t()->update_for_cat($delids, ['catid' => $_POST['tocatid']]);
			$num = table_portal_article_title::t()->fetch_count_for_cat($_POST['tocatid']);
			table_portal_category::t()->update($_POST['tocatid'], ['articles' => dintval($num)]);
		}
	}

	if($portalcategory[$_GET['catid']]['foldername']) delportalcategoryfolder($_GET['catid']);
	updatecache(['portalcategory', 'diytemplatename']);
	loadcache('portalcategory', true);
	remakecategoryfile($updatecategoryfile);
	cpmsg('portalcategory_delete_succeed', 'action=portalcategory', 'succeed');
}
	