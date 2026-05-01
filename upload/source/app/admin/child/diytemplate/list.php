<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$searchctrl = '<span style="float: right; padding-right: 40px;">'
	.'<a href="javascript:;" onclick="$(\'tb_search\').style.display=\'\';$(\'a_search_show\').style.display=\'none\';$(\'a_search_hide\').style.display=\'\';" id="a_search_show" style="display:none">'.cplang('show_search').'</a>'
	.'<a href="javascript:;" onclick="$(\'tb_search\').style.display=\'none\';$(\'a_search_show\').style.display=\'\';$(\'a_search_hide\').style.display=\'none\';" id="a_search_hide">'.cplang('hide_search').'</a>'
	.'</span>';
showsubmenu('diytemplate', [
	['list', 'diytemplate', 1],
], $searchctrl);

$intkeys = ['uid', 'closed'];
$strkeys = [];
$randkeys = [];
$likekeys = ['targettplname', 'primaltplname', 'username', 'name'];
$results = getwheres($intkeys, $strkeys, $randkeys, $likekeys);
foreach($likekeys as $k) {
	$_GET[$k] = dhtmlspecialchars($_GET[$k]);
}
$wherearr = $results['wherearr'];
$mpurl = ADMINSCRIPT.'?action=diytemplate';
$mpurl .= '&'.implode('&', $results['urls']);
$wherearr[] = " primaltplname NOT LIKE 'portal/list%' ";
$wherearr[] = " primaltplname NOT LIKE 'portal/portal_topic_content%' ";

if($_GET['permname']) {
	$tpls = '';
	$member = table_common_member::t()->fetch_by_username($_GET['permname']);
	if($member && $member['adminid'] != 1) {
		$tpls = array_keys(table_common_template_permission::t()->fetch_all_by_uid($member['uid']));
		if(($tpls = dimplode($tpls))) {
			$wherearr[] = 'targettplname IN ('.$tpls.')';
		} else {
			cpmsg_error($_GET['permname'].cplang('diytemplate_the_username_has_not_template'));
		}
	}
	$mpurl .= '&permname='.$_GET['permname'];
}

$wheresql = empty($wherearr) ? '' : implode(' AND ', $wherearr);

$orders = getorders(['dateline', 'targettplname'], 'dateline');
$ordersql = $orders['sql'];
if($orders['urls']) $mpurl .= '&'.implode('&', $orders['urls']);
$orderby = [$_GET['orderby'] => ' selected'];
$ordersc = [$_GET['ordersc'] => ' selected'];

$perpage = empty($_GET['perpage']) ? 0 : intval($_GET['perpage']);
if(!in_array($perpage, [10, 20, 50, 100])) $perpage = 20;
$perpages = [$perpage => ' selected'];

$searchlang = [];
$keys = ['search', 'likesupport', 'resultsort', 'defaultsort', 'orderdesc', 'orderasc', 'perpage_10', 'perpage_20', 'perpage_50', 'perpage_100',
	'diytemplate_name', 'diytemplate_dateline', 'diytemplate_targettplname', 'diytemplate_primaltplname', 'diytemplate_uid', 'diytemplate_username',
	'nolimit', 'no', 'yes', 'diytemplate_permname', 'diytemplate_permname_tips'];
foreach($keys as $key) {
	$searchlang[$key] = cplang($key);
}

$adminscript = ADMINSCRIPT;
echo <<<SEARCH
	<form method="post" autocomplete="off" action="$adminscript" id="tb_search">
		<table cellspacing="3" cellpadding="3" class="tb tb2">
			<tr>
				<td>{$searchlang['diytemplate_name']}*</td><td><input type="text" class="txt" name="name" value="{$_GET['name']}"></td>
				<td>{$searchlang['diytemplate_targettplname']}*</td><td><input type="text" class="txt" name="targettplname" value="{$_GET['targettplname']}"></td>
				<td>{$searchlang['diytemplate_primaltplname']}*</td><td><input type="text" class="txt" name="primaltplname" value="{$_GET['primaltplname']}"> *{$searchlang['likesupport']}</td>
			</tr>
			<tr>
				<td>{$searchlang['diytemplate_uid']}</td><td><input type="text" class="txt" name="uid" value="{$_GET['uid']}"></td>
				<td>{$searchlang['diytemplate_username']}*</td><td><input type="text" class="txt" name="username" value="{$_GET['username']}" colspan="2"></td>
			</tr>
			<tr>
				<td>{$searchlang['resultsort']}</td>
				<td colspan="3">
					<select name="orderby">
					<option value="">{$searchlang['defaultsort']}</option>
					<option value="dateline"{$orderby['dateline']}>{$searchlang['diytemplate_dateline']}</option>
					<option value="targettplname"{$orderby['targettplname']}>{$searchlang['diytemplate_targettplname']}</option>
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
					<input type="hidden" name="action" value="diytemplate">
				</td>
				<td>{$searchlang['diytemplate_permname']}</td>
				<td><input type="text" class="txt" name="permname" value="{$_GET['permname']}"> {$searchlang['diytemplate_permname_tips']}
					<input type="submit" name="searchsubmit" value="{$searchlang['search']}" class="btn"></td>
			</tr>
		</table>
	</form>
SEARCH;

$start = ($page - 1) * $perpage;

$mpurl .= '&perpage='.$perpage;
$perpages = [$perpage => ' selected'];

showformheader('diytemplate');
showtableheader('diytemplate_list');
showsubtitle(['diytemplate_name', 'diytemplate_targettplname', 'diytemplate_primaltplname', 'username', 'diytemplate_dateline', 'operation']);

$multipage = '';
if(($count = table_common_diy_data::t()->count_by_where($wheresql))) {
	loadcache('diytemplatename');
	require_once libfile('function/block');
	foreach(table_common_diy_data::t()->fetch_all_by_where($wheresql, $ordersql, $start, $perpage) as $value) {
		$value['name'] = $_G['cache']['diytemplatename'][$value['targettplname']];
		$value['dateline'] = $value['dateline'] ? dgmdate($value['dateline']) : '';
		$diyurl = block_getdiyurl($value['targettplname']);
		$diytitle = cplang($diyurl['flag'] ? 'diytemplate_share' : 'diytemplate_alone');
		showtablerow('', ['class=""', 'class=""', 'class="td28"'], [
			"<a href=\"{$diyurl['url']}\" title=\"$diytitle\" target=\"_blank\">{$value['name']}</a>",
			'<span title="'.cplang('diytemplate_path').'./data/diy/'.$value['targettplname'].'.htm">'.$value['targettplname'].'</span>',
			'<span title="'.cplang('diytemplate_path').$_G['style']['tpldir'].'/'.$value['primaltplname'].'.htm">'.$value['primaltplname'].'</span>',
			"<a href=\"home.php?mod=space&uid={$value['uid']}&do=profile\" target=\"_blank\">{$value['username']}</a>",
			$value['dateline'],
			'<a href="'.ADMINSCRIPT.'?action=diytemplate&operation=edit&targettplname='.$value['targettplname'].'&tpldirectory='.$value['tpldirectory'].'">'.cplang('edit').'</a> '.
			'<a href="'.ADMINSCRIPT.'?action=diytemplate&operation=perm&targettplname='.$value['targettplname'].'&tpldirectory='.$value['tpldirectory'].'">'.cplang('diytemplate_perm').'</a>',
		]);
	}
	$multipage = multi($count, $perpage, $page, $mpurl);
}

showsubmit('', '', '', '', $multipage);
showtablefooter();
showformfooter();
	