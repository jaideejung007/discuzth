<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($portalcategory) && table_portal_category::t()->count()) {
	updatecache('portalcategory');
	loadcache('portalcategory', true);
	$portalcategory = $_G['cache']['portalcategory'];
}
if(!submitcheck('editsubmit')) {

	shownav('portal', 'portalcategory');
	showsubmenu('portalcategory', [
		['list', 'portalcategory', 1]
	]);

	$tdstyle = ['width="25"', 'width="80"', '', 'width="50"', 'width="65"', 'width="35"', 'width="35"', 'width="35"', 'width="215"', 'width="110"'];
	showformheader('portalcategory');
	echo '<div class="forumheader"><a href="javascript:;" onclick="show_all()">'.cplang('show_all').'</a> | <a href="javascript:;" onclick="hide_all()">'.cplang('hide_all').'</a>&nbsp;&nbsp;&nbsp;<input type="text" id="srchforumipt" class="txt" /> <input type="submit" class="btn" value="'.cplang('search').'" onclick="return srchforum()" /></div>';
	showtableheader('', '', 'id="portalcategory_header" style="min-width:900px;*width:900px;"');
	showsubtitle(['', '', 'portalcategory_name', 'portalcategory_articles', 'portalcategory_allowpublish', 'portalcategory_allowcomment', 'portalcategory_is_closed', 'setindex', 'operation', 'portalcategory_article_op'], 'header tbm', $tdstyle);
	showtablefooter();
	showtableheader('', '', 'id="portalcategorytable" style="min-width:900px;*width:900px;"');
	showsubtitle(['', '', 'portalcategory_name', 'portalcategory_articles', 'portalcategory_allowpublish', 'portalcategory_allowcomment', 'portalcategory_is_closed', 'setindex', 'operation', 'portalcategory_article_op'], 'header', $tdstyle);
	foreach($portalcategory as $key => $value) {
		if($value['level'] == 0) {
			echo showcategoryrow($key, 0, '');
		}
	}
	echo '<tbody><tr><td>&nbsp;</td><td colspan="6"><div><a class="addtr" href="'.ADMINSCRIPT.'?action=portalcategory&operation=add&upid=0">'.cplang('portalcategory_addcategory').'</a></div></td><td colspan="3">&nbsp;</td></tr></tbody>';
	showsubmit('editsubmit');
	showtablefooter();
	showformfooter();
	echo '<script type="text/javascript">floatbottom(\'portalcategory_header\');$(\'portalcategory_header\').style.width = $(\'portalcategorytable\').offsetWidth + \'px\';</script>';

	$langs = [];
	$keys = ['portalcategory_addcategory', 'portalcategory_addsubcategory', 'portalcategory_addthirdcategory'];
	foreach($keys as $key) {
		$langs[$key] = cplang($key);
	}
	echo <<<SCRIPT
<script type="text/Javascript">
var rowtypedata = [
	[[1,'', ''], [4, '<div class="parentboard"><input type="text" class="txt" value="{$lang['portalcategory_addcategory']}" name="newname[{1}][]"/></div>']],
	[[1,'<input type="text" class="txt" name="neworder[{1}][]" value="0" />', 'td25'], [4, '<div class="board"><input type="text" class="txt" value="{$lang['portalcategory_addsubcategory']}" name="newname[{1}][]"/>  <input type="checkbox" name="newinheritance[{1}][]" value="1" checked>{$lang['portalcategory_inheritance']}</div>']],
	[[1,'<input type="text" class="txt" name="neworder[{1}][]" value="0" />', 'td25'], [4, '<div class="childboard"><input type="text" class="txt" value="{$lang['portalcategory_addthirdcategory']}" name="newname[{1}][]"/> <input type="checkbox" name="newinheritance[{1}][]" value="1" checked>{$lang['portalcategory_inheritance']}</div>']],
];
</script>
SCRIPT;

} else {
	$cachearr = ['portalcategory'];
	if($_POST['name']) {
		$openarr = $closearr = [];
		foreach($_POST['name'] as $key => $value) {
			$sets = [];
			$value = trim($value);
			if($portalcategory[$key] && $portalcategory[$key]['catname'] != $value) {
				$sets['catname'] = $value;
			}
			if($portalcategory[$key] && $portalcategory[$key]['displayorder'] != $_POST['neworder'][$key]) {
				$sets['displayorder'] = $_POST['neworder'][$key];
			}
			if($sets) {
				table_portal_category::t()->update($key, $sets);
				table_common_diy_data::t()->update_diy('portal/list_'.$key, getdiydirectory($portalcategory[$key]['primaltplname']), ['name' => $value]);
				table_common_diy_data::t()->update_diy('portal/view_'.$key, getdiydirectory($portalcategory[$key]['articleprimaltplname']), ['name' => $value]);
				$cachearr[] = 'diytemplatename';
			}
		}
	}

	if($_GET['newsetindex']) {
		table_common_setting::t()->update_setting('defaultindex', $portalcategory[$_GET['newsetindex']]['caturl']);
		$cachearr[] = 'setting';
	}
	include_once libfile('function/cache');
	updatecache($cachearr);

	cpmsg('portalcategory_update_succeed', 'action=portalcategory', 'succeed');
}
	