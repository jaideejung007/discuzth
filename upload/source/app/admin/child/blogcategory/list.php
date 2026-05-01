<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('editsubmit')) {

	shownav('portal', 'blogcategory');
	showsubmenu('blogcategory', [
		['list', 'blogcategory', 1]
	]);

	/*search={"blogcategory":"action=blogcategory"}*/
	showformheader('blogcategory');
	showtableheader('', 'nobottom');
	showsetting('system_category_stat', 'settingnew[blogcategorystat]', $_G['setting']['blogcategorystat'], 'radio', '', 1);
	showsetting('system_category_required', 'settingnew[blogcategoryrequired]', $_G['setting']['blogcategoryrequired'], 'radio', '');
	echo '<tr><td colspan="2">';
	showtableheader();
	showsubtitle(['order', 'blogcategory_name', 'blogcategory_num', 'operation']);
	foreach($category as $key => $value) {
		if($value['level'] == 0) {
			echo showcategoryrow($key, 0, '');
		}
	}
	echo '<tr><td class="td25">&nbsp;</td><td colspan="3"><div><a class="addtr" onclick="addrow(this, 0, 0)" href="###">'.cplang('blogcategory_addcategory').'</a></div></td></tr>';
	showtablefooter();
	echo '</td></tr>';
	showtablefooter();
	/*search*/

	showtableheader('', 'notop');
	showsubmit('editsubmit');
	showtablefooter();
	showformfooter();

	$langs = [];
	$keys = ['blogcategory_addcategory', 'blogcategory_addsubcategory', 'blogcategory_addthirdcategory'];
	foreach($keys as $key) {
		$langs[$key] = cplang($key);
	}
	echo <<<SCRIPT
<script type="text/JavaScript">
var rowtypedata = [
	[[1,'<input type="text" class="txt" name="neworder[{1}][]" value="0" />', 'td25'], [3, '<div class="parentboard"><input type="text" class="txt" value="{$lang['blogcategory_addcategory']}" name="newname[{1}][]"/></div>']],
	[[1,'<input type="text" class="txt" name="neworder[{1}][]" value="0" />', 'td25'], [3, '<div class="board"><input type="text" class="txt" value="{$lang['blogcategory_addsubcategory']}" name="newname[{1}][]"/></div>']],
	[[1,'<input type="text" class="txt" name="neworder[{1}][]" value="0" />', 'td25'], [3, '<div class="childboard"><input type="text" class="txt" value="{$lang['blogcategory_addthirdcategory']}" name="newname[{1}][]"/></div>']],
];
</script>
SCRIPT;

} else {

	if($_POST['name']) {
		foreach($_POST['name'] as $key => $value) {
			$sets = [];
			$value = trim($value);
			if($category[$key] && $category[$key]['catname'] != $value) {
				$sets['catname'] = $value;
			}
			if($category[$key] && $category[$key]['displayorder'] != $_POST['order'][$key]) {
				$sets['displayorder'] = $_POST['order'][$key] ? $_POST['order'][$key] : '0';
			}
			if($sets) {
				table_home_blog_category::t()->update($key, $sets);
			}
		}
	}
	if($_POST['newname']) {
		foreach($_POST['newname'] as $upid => $names) {
			foreach($names as $nameid => $name) {
				table_home_blog_category::t()->insert(['upid' => $upid, 'catname' => trim($name), 'displayorder' => intval($_POST['neworder'][$upid][$nameid])]);
			}
		}
	}

	if($_POST['settingnew']) {
		$_POST['settingnew'] = array_map('intval', $_POST['settingnew']);
		table_common_setting::t()->update_batch($_POST['settingnew']);
		updatecache('setting');
	}

	include_once libfile('function/cache');
	updatecache('blogcategory');

	cpmsg('blogcategory_update_succeed', 'action=blogcategory', 'succeed');
}
	