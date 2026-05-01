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

	shownav('portal', 'albumcategory');
	showsubmenu('albumcategory', [
		['list', 'albumcategory', 1]
	]);

	/*search={"albumcategory":"action=albumcategory"}*/
	showformheader('albumcategory');
	showtableheader('', 'nobottom');
	showsetting('system_category_stat', 'settingnew[albumcategorystat]', $_G['setting']['albumcategorystat'], 'radio', '', 1);
	showsetting('system_category_required', 'settingnew[albumcategoryrequired]', $_G['setting']['albumcategoryrequired'], 'radio', '');
	echo '<tr><td colspan="2">';
	showtableheader();
	showsubtitle(['order', 'albumcategory_name', 'albumcategory_num', 'operation']);
	foreach($category as $key => $value) {
		if($value['level'] == 0) {
			echo showcategoryrow($key, 0, '');
		}
	}
	echo '<tr><td class="td25">&nbsp;</td><td colspan="3"><div><a class="addtr" onclick="addrow(this, 0, 0)" href="###">'.cplang('albumcategory_addcategory').'</a></div></td></tr>';
	showtablefooter();
	echo '</td></tr>';
	showtablefooter();
	/*search*/

	showtableheader('', 'notop');
	showsubmit('editsubmit');
	showtablefooter();
	showformfooter();

	$langs = [];
	$keys = ['albumcategory_addcategory', 'albumcategory_addsubcategory', 'albumcategory_addthirdcategory'];
	foreach($keys as $key) {
		$langs[$key] = cplang($key);
	}
	echo <<<SCRIPT
<script type="text/Javascript">
var rowtypedata = [
	[[1,'<input type="text" class="txt" name="neworder[{1}][]" value="0" />', 'td25'], [3, '<div class="parentboard"><input type="text" class="txt" value="{$lang['albumcategory_addcategory']}" name="newname[{1}][]"/></div>']],
	[[1,'<input type="text" class="txt" name="neworder[{1}][]" value="0" />', 'td25'], [3, '<div class="board"><input type="text" class="txt" value="{$lang['albumcategory_addsubcategory']}" name="newname[{1}][]"/></div>']],
	[[1,'<input type="text" class="txt" name="neworder[{1}][]" value="0" />', 'td25'], [3, '<div class="childboard"><input type="text" class="txt" value="{$lang['albumcategory_addthirdcategory']}" name="newname[{1}][]"/></div>']],
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
				table_home_album_category::t()->update($key, $sets);
			}
		}
	}
	if($_POST['newname']) {
		foreach($_POST['newname'] as $upid => $names) {
			foreach($names as $nameid => $name) {
				table_home_album_category::t()->insert(['upid' => $upid, 'catname' => trim($name), 'displayorder' => intval($_POST['neworder'][$upid][$nameid])]);
			}
		}
	}

	if($_POST['settingnew']) {
		$_POST['settingnew'] = array_map('intval', $_POST['settingnew']);
		table_common_setting::t()->update_batch($_POST['settingnew']);
		updatecache('setting');
	}

	include_once libfile('function/cache');
	updatecache('albumcategory');

	cpmsg('albumcategory_update_succeed', 'action=albumcategory', 'succeed');
}
	