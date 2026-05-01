<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('deletesubmit')) {

	if($_POST['ids']) {
		table_common_block_item::t()->delete_by_bid($_POST['ids']);
		table_common_block::t()->delete($_POST['ids']);
		table_common_block_permission::t()->delete_by_bid_uid_inheritedtplname($_POST['ids']);
		cpmsg('block_delete_succeed', 'action=block&operation=jscall', 'succeed');
	} else {
		cpmsg('block_choose_at_least_one_block', 'action=block&operation=jscall', 'error');
	}

} elseif(submitcheck('clearsubmit')) {

	include_once libfile('function/block');
	block_clear();
	cpmsg('block_clear_unused_succeed', 'action=block', 'succeed');

} else {

	loadcache(['diytemplatename']);
	$searchctrl = '<span style="float: right; padding-right: 40px;">'
		.'<a href="javascript:;" onclick="$(\'tb_search\').style.display=\'\';$(\'a_search_show\').style.display=\'none\';$(\'a_search_hide\').style.display=\'\';" id="a_search_show" style="display:none">'.cplang('show_search').'</a>'
		.'<a href="javascript:;" onclick="$(\'tb_search\').style.display=\'none\';$(\'a_search_show\').style.display=\'\';$(\'a_search_hide\').style.display=\'none\';" id="a_search_hide">'.cplang('hide_search').'</a>'
		.'</span>';
	showsubmenu('block', [
		['block_list', 'block', $operation == 'list'],
		['block_jscall', 'block&operation=jscall', $operation == 'jscall']
	], $searchctrl);

	$mpurl = ADMINSCRIPT.'?action=block&operation='.$operation;

	$intkeys = ['bid'];
	$strkeys = ['blockclass'];
	$strkeys[] = 'targettplname';
	$randkeys = [];
	$likekeys = ['name'];
	$results = getwheres($intkeys, $strkeys, $randkeys, $likekeys);
	foreach($likekeys as $k) {
		$_GET[$k] = dhtmlspecialchars($_GET[$k]);
	}
	$wherearr = $results['wherearr'];
	$mpurl .= '&'.implode('&', $results['urls']);

	$wherearr[] = $operation == 'jscall' ? "blocktype='1'" : "blocktype='0'";
	if($_GET['permname']) {
		$bids = '';
		$uid = ($uid = table_common_member::t()->fetch_uid_by_username($_GET['permname'])) ? $uid : table_common_member_archive::t()->fetch_uid_by_username($_GET['permname']);
		if($uid) {
			$bids = array_keys(table_common_block_permission::t()->fetch_all_by_uid($uid));
		}
		if(($bids = dimplode($bids))) {
			$wherearr[] = 'bid IN ('.$bids.')';
		} else {
			cpmsg_error($_GET['permname'].cplang('block_the_username_has_not_block'));
		}
		$mpurl .= '&permname='.$_GET['permname'];
	}

	$wheresql = empty($wherearr) ? '1' : implode(' AND ', $wherearr);
	$wheresql = str_replace(['bid', 'blockclass', ' name', 'blocktype', 'targettplname'], ['b.bid', 'b.blockclass', ' b.name', 'b.blocktype', 'tb.targettplname'], $wheresql);

	$orders = getorders(['bid', 'dateline'], 'bid');
	$ordersql = $orders['sql'];
	if($orders['urls']) $mpurl .= '&'.implode('&', $orders['urls']);
	$orderby = [$_GET['orderby'] => ' selected'];
	$ordersc = [$_GET['ordersc'] => ' selected'];

	$perpage = empty($_GET['perpage']) ? 0 : intval($_GET['perpage']);
	if(!in_array($perpage, [10, 20, 50, 100])) $perpage = 20;
	$perpages = [$perpage => ' selected'];
	$mpurl .= '&perpage='.$perpage;

	$searchlang = [];
	$keys = ['search', 'likesupport', 'lengthabove1', 'resultsort', 'defaultsort', 'orderdesc', 'orderasc', 'perpage_10', 'perpage_20', 'perpage_50', 'perpage_100',
		'block_dateline', 'block_id', 'block_name', 'block_blockclass', 'block_add_jscall', 'block_choose_blockclass_to_add_jscall', 'block_diytemplate', 'block_permname', 'block_permname_tips'];
	foreach($keys as $key) {
		$searchlang[$key] = cplang($key);
	}
	$diytemplatename_sel = '<select name="targettplname" id="targettplname">';
	$diytemplatename_sel .= '<option value="">'.cplang('diytemplate_name').'</option>';
	foreach($_G['cache']['diytemplatename'] as $key => $value) {
		$selected = ($key == $_GET['targettplname'] ? ' selected' : '');
		$diytemplatename_sel .= "<option value=\"$key\"$selected>$value</option>";
	}
	$diytemplatename_sel .= '</select>';
	$blockclass_sel = '<select name="blockclass" id="blockclass">';
	$blockclass_sel .= '<option value="">'.cplang('blockstyle_blockclass_sel').'</option>';
	foreach($_G['cache']['blockclass'] as $key => $value) {
		foreach($value['subs'] as $subkey => $subvalue) {
			$selected = ($subkey == $_GET['blockclass'] ? ' selected' : '');
			$blockclass_sel .= "<option value=\"$subkey\"$selected>{$subvalue['name']}</option>";
		}
	}
	$blockclass_sel .= '</select>';
	$addjscall = $operation == 'jscall' ? '<input type="button" class="btn" onclick="addjscall()" value="'.$searchlang['block_add_jscall'].'" />' : '';
	$firstrow = "<td>{$searchlang['block_diytemplate']}</td><td>$diytemplatename_sel</td><td>{$searchlang['block_blockclass']}</td><td colspan=\"2\">$blockclass_sel $addjscall</td>";
	$adminscript = ADMINSCRIPT;
	echo <<<SEARCH
			<script>disallowfloat = '{$_G['setting']['disallowfloat']}';</script>
			<script type="text/javascript" src="{$_G['setting']['jspath']}portal.js?{$_G['style']['verhash']}"></script>
			<div id="ajaxwaitid"></div>
			<form method="get" autocomplete="off" action="$adminscript" id="tb_search">
				<table cellspacing="3" cellpadding="3" class="tb tb2">
					<tr>
						$firstrow
					</tr>
					<tr>
						<td>{$searchlang['block_id']}</td><td><input type="text" class="txt" name="bid" value="{$_GET['bid']}"></td>
						<td>{$searchlang['block_name']}*</td><td><input type="text" class="txt" name="name" value="{$_GET['name']}">{$searchlang['lengthabove1']}&nbsp;&nbsp; *{$searchlang['likesupport']}</td>
					</tr>
					<tr>
						<td>{$searchlang['resultsort']}</td>
						<td>
							<select name="orderby">
							<option value="">{$searchlang['defaultsort']}</option>
							<option value="dateline"{$orderby['dateline']}>{$searchlang['block_dateline']}</option>
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
							<input type="hidden" name="action" value="block">
							<input type="hidden" name="operation" value="$operation">
						</td>
						<td>{$searchlang['block_permname']}</td><td><input type="text" class="txt" name="permname" value="{$_GET['permname']}">{$searchlang['block_permname_tips']}
							<input type="submit" name="searchsubmit" value="{$searchlang['search']}" class="btn"></td>
					</tr>
				</table>
			</form>
			<script type="text/javascript">
			function addjscall() {
				var blockclass = $('blockclass').value;
				if(blockclass) {
					showWindow('blockclass', 'portal.php?mod=portalcp&ac=block&op=block&blocktype=1&from=cp&classname=' + blockclass);
				} else {
					alert('{$searchlang['block_choose_blockclass_to_add_jscall']}');
				}
			}
			</script>
SEARCH;

	$start = ($page - 1) * $perpage;

	showformheader('block&operation='.$operation);
	showtableheader();

	$list = $diypage = [];
	include_once libfile('function/block');
	if($operation == 'jscall') {
		showsubtitle(['', 'block_name', 'block_script', 'block_style', 'block_dateline', 'block_page', 'operation']);
		$multipage = '';
		if(($count = table_common_block::t()->count_by_admincpwhere($wheresql))) {
			foreach(table_common_block::t()->fetch_all_by_admincpwhere($wheresql, $ordersql, $start, $perpage) as $value) {
				if($value['targettplname']) {
					$diyurl = block_getdiyurl($value['targettplname']);
					$diyurl = $diyurl['url'];
					$tplname = $_G['cache']['diytemplatename'][$value['targettplname']] ?? $value['targettplname'];
					$diypage[$value['bid']][$value['targettplname']] = $diyurl ? '<a href="'.$diyurl.'" target="_blank">'.$tplname.'</a>' : $tplname;
				}
				$list[$value['bid']] = $value;
			}
			if($list) {
				foreach($list as $bid => $value) {
					$inpage = empty($diypage[$bid]) ? cplang('block_page_nopage') : implode('<br/>', $diypage[$bid]);
					$theclass = block_getclass($value['blockclass'], true);
					showtablerow('', ['class="td25"'], [
						"<input type=\"checkbox\" class=\"checkbox\" name=\"ids[]\" value=\"{$value['bid']}\">",
						!empty($value['name']) ? $value['name'] : cplang('block_name_null'),
						$theclass['script'][$value['script']],
						$value['styleid'] ? $theclass['style'][$value['styleid']]['name'] : lang('portalcp', 'blockstyle_diy'),
						!empty($value['dateline']) ? dgmdate($value['dateline']) : cplang('block_dateline_null'),
						$inpage,
						"<a href=\"portal.php?mod=portalcp&ac=block&op=block&bid={$value['bid']}&blocktype=1&from=cp\" target=\"_blank\" onclick=\"showWindow('showblock',this.href);return false;\">".cplang('block_setting').'</a> &nbsp;&nbsp'.
						"<a href=\"portal.php?mod=portalcp&ac=block&op=getblock&forceupdate=1&inajax=1&bid={$value['bid']}&from=cp\" onclick=\"ajaxget(this.href,'','','','',function(){location.reload();});return false;\">".cplang('block_update').'</a> &nbsp;&nbsp'.
						"<a href=\"portal.php?mod=portalcp&ac=block&op=data&bid={$value['bid']}&blocktype=1&from=cp\" target=\"_blank\" onclick=\"showWindow('showblock',this.href);return false;\">".cplang('block_data').'</a> &nbsp;&nbsp'.
						"<a href=\"javascript:;\" onclick=\"prompt('".cplang('block_copycode_message')."', '<!--{block/{$value['bid']}}-->')\">".cplang('block_copycode_inner').'</a> &nbsp;&nbsp'.
						"<a href=\"javascript:;\" onclick=\"prompt('".cplang('block_copycode_jsmessage')."', '&lt;script type=&quot;text/javascript&quot; src=&quot;{$_G['siteurl']}api.php?mod=js&bid={$value['bid']}&quot;&gt;&lt;/script&gt;')\">".cplang('block_copycode_outer')."</a>&nbsp;&nbsp;<a href=\"".ADMINSCRIPT."?action=block&operation=perm&bid={$value['bid']}\">".cplang('portalcategory_perm').'</a>'
					]);
				}
			}
			$multipage = multi($count, $perpage, $page, $mpurl);
		}

		showsubmit('', '', '', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'ids\')" /><label for="chkall">'.cplang('select_all').'</label>&nbsp;&nbsp;<input type="submit" class="btn" name="deletesubmit" value="'.cplang('block_delete').'" />', $multipage);
		showtablefooter();
		showformfooter();

	} else {

		showsubtitle(['block_name', 'block_script', 'block_style', 'block_dateline', 'block_page', 'operation']);
		$multipage = '';
		if(($count = table_common_block::t()->count_by_admincpwhere($wheresql))) {
			foreach(table_common_block::t()->fetch_all_by_admincpwhere($wheresql, $ordersql, $start, $perpage) as $value) {
				if($value['targettplname']) {
					$diyurl = block_getdiyurl($value['targettplname']);
					$diyurl = $diyurl['url'];
					$tplname = $_G['cache']['diytemplatename'][$value['targettplname']] ?? $value['targettplname'];
					$diypage[$value['bid']][$value['targettplname']] = $diyurl ? '<a href="'.$diyurl.'" target="_blank">'.$tplname.'</a>' : $tplname;
				}
				$list[$value['bid']] = $value;
			}
			if($list) {
				foreach($list as $bid => $value) {
					$inpage = empty($diypage[$bid]) ? cplang('block_page_unused') : implode('<br/>', $diypage[$bid]);
					$theclass = block_getclass($value['blockclass'], true);
					showtablerow('', '', [
						$value['name'] ? $value['name'] : cplang('block_name_null'),
						$theclass['script'][$value['script']],
						$value['styleid'] ? $theclass['style'][$value['styleid']]['name'] : lang('portalcp', 'blockstyle_diy'),
						!empty($value['dateline']) ? dgmdate($value['dateline']) : cplang('block_dateline_null'),
						$inpage,
						"<a href=\"portal.php?mod=portalcp&ac=block&op=block&bid={$value['bid']}&from=cp\" target=\"_blank\" onclick=\"showWindow('showblock',this.href);return false;\">".cplang('block_setting').'</a> &nbsp;&nbsp'
						."<a href=\"portal.php?mod=portalcp&ac=block&op=data&bid={$value['bid']}&from=cp\" target=\"_blank\" onclick=\"showWindow('showblock',this.href);return false;\">".cplang('block_data').'</a> &nbsp;&nbsp'
						.$diyop."&nbsp;&nbsp;<a href=\""
						.ADMINSCRIPT."?action=block&operation=perm&bid={$value['bid']}\">".cplang('portalcategory_perm').'</a>'
					]);
				}
			}
			$multipage = multi($count, $perpage, $page, $mpurl);
		}

		showsubmit('', '', '', '<input type="submit" class="btn" name="clearsubmit" value="'.cplang('block_clear_unused').'" />', $multipage);
		showtablefooter();
		showformfooter();
	}
}
	