<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$_GET = $_GET + $_POST;
$searchctrl = '<span style="float: right; padding-right: 40px;">'
	.'<a href="javascript:;" onclick="$(\'tb_search\').style.display=\'\';$(\'a_search_show\').style.display=\'none\';$(\'a_search_hide\').style.display=\'\';" id="a_search_show" style="display:none">'.cplang('show_search').'</a>'
	.'<a href="javascript:;" onclick="$(\'tb_search\').style.display=\'none\';$(\'a_search_show\').style.display=\'\';$(\'a_search_hide\').style.display=\'none\';" id="a_search_hide">'.cplang('hide_search').'</a>'
	.'</span>';
showsubmenu('blockstyle', [
	['list', 'blockstyle', 1],
	['add', 'blockstyle&operation=add', 0]
], $searchctrl);

$mpurl = ADMINSCRIPT.'?action=blockstyle';
$intkeys = ['styleid'];
$strkeys = ['blockclass'];
$randkeys = [];
$likekeys = ['name', 'template'];
$results = getwheres($intkeys, $strkeys, $randkeys, $likekeys);
foreach($likekeys as $k) {
	$_GET[$k] = dhtmlspecialchars($_GET[$k]);
}
$wherearr = $results['wherearr'];
$mpurl .= '&'.implode('&', $results['urls']);

$wheresql = empty($wherearr) ? '1' : implode(' AND ', $wherearr);

$orders = getorders(['blockclass'], 'styleid');
$ordersql = $orders['sql'];
if($orders['urls']) $mpurl .= '&'.implode('&', $orders['urls']);
$orderby = [$_GET['orderby'] => ' selected'];
$ordersc = [$_GET['ordersc'] => ' selected'];

$perpage = empty($_GET['perpage']) ? 0 : intval($_GET['perpage']);
if(!in_array($perpage, [10, 20, 50, 100])) $perpage = 20;
$perpages = [$perpage => ' selected'];
$mpurl .= '&perpage='.$perpage;

$searchlang = [];
$keys = ['search', 'likesupport', 'resultsort', 'defaultsort', 'orderdesc', 'orderasc', 'perpage_10', 'perpage_20', 'perpage_50', 'perpage_100',
	'blockstyle_id', 'blockstyle_name', 'blockstyle_blockclass', 'blockstyle_template'];
foreach($keys as $key) {
	$searchlang[$key] = cplang($key);
}
$blockclass_sel = '<select name="blockclass">';
$blockclass_sel .= '<option value="">'.cplang('blockstyle_blockclass_sel').'</option>';
foreach($_G['cache']['blockclass'] as $key => $value) {
	foreach($value['subs'] as $subkey => $subvalue) {
		$selected = (!empty($_GET['blockclass']) && $subkey == $_GET['blockclass'] ? ' selected' : '');
		$blockclass_sel .= "<option value=\"$subkey\"$selected>{$subvalue['name']}</option>";
	}
}
$blockclass_sel .= '</select>';

$adminscript = ADMINSCRIPT;
echo <<<SEARCH
<form method="post" autocomplete="off" action="$adminscript" id="tb_search">
	<div class="dbox"><div class="boxbody">
		<table cellspacing="3" cellpadding="3" class="tb tb2">
			<tr>
				<td>{$searchlang['blockstyle_id']}</td><td><input type="text" class="txt" name="styleid" value="{$_GET['styleid']}"></td>
				<td>{$searchlang['blockstyle_name']}*</td><td><input type="text" class="txt" name="name" value="{$_GET['name']}">*{$searchlang['likesupport']}</td>
			</tr>
			<tr>
				<td>{$searchlang['blockstyle_blockclass']}</td><td>$blockclass_sel</td>
				<td>{$searchlang['blockstyle_template']}*</td><td><input type="text" name="template" value="{$_GET['template']}">*{$searchlang['likesupport']}</td>
			</tr>
			<tr>
				<td>{$searchlang['resultsort']}</td>
				<td colspan="3">
					<select name="orderby">
					<option value="styleid">{$searchlang['defaultsort']}</option>
					<option value="blockclass"{$orderby['blockclass']}>{$searchlang['blockstyle_blockclass']}</option>
					</select>
					<select name="ordersc">
					<option value="desc"{$ordersc['desc']}>{$searchlang['orderdesc']}</option>
					<option value="asc"{$ordersc['asc']}>{$searchlang['orderasc']}</option>
					</select>
					<select name="perpage">
					<option value="10"{$perpages[10]}>{$searchlang['perpage_10']}</option>
					<option value="20"{$perpages[20]}>{$searchlang['perpage_20']}</option>
					<option value="50"{$perpages[50]}>{$searchlang['perpage_50']}</option>
					<option value="100"{$perpages[100]}>{$searchlang['perpage_100']}</option>
					</select>
					<input type="hidden" name="action" value="blockstyle">
					<input type="submit" name="searchsubmit" value="{$searchlang['search']}" class="btn">
				</td>
			</tr>
		</table>
	</div></div>
</form>
SEARCH;

$start = ($page - 1) * $perpage;

showformheader('blockstyle');
showboxheader('blockstyle_list');
showtableheader();
showsubtitle(['blockstyle_name', 'blockstyle_blockclass', 'operation']);

$multipage = '';
if(($count = table_common_block_style::t()->count_by_where($wheresql))) {
	include_once libfile('function/block');
	foreach(table_common_block_style::t()->fetch_all_by_where($wheresql, $ordersql, $start, $perpage) as $value) {
		$theclass = block_getclass($value['blockclass']);
		list($c1, $c2) = explode('_', $value['blockclass']);
		showtablerow('', ['class=""', 'class=""', 'class="td28"'], [
			$value['name'],
			$theclass['name'],
			"<a href=\"".ADMINSCRIPT."?action=blockstyle&operation=edit&blockclass={$value['blockclass']}&styleid={$value['styleid']}\">".cplang('blockstyle_edit').'</a>&nbsp;&nbsp;'.
			"<a href=\"".ADMINSCRIPT."?action=blockstyle&operation=delete&styleid={$value['styleid']}\">".cplang('blockstyle_delete').'</a>'
		]);
	}
	$multipage = multi($count, $perpage, $page, $mpurl);
}

showsubmit('', '', '', '', $multipage);
showtablefooter();
showboxfooter();
showformfooter();
	