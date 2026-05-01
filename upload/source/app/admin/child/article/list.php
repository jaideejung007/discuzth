<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('articlesubmit')) {

	$perpage = intval($_GET['hiddenperpage']);
	$page = intval($_GET['hiddenpage']);
	$catid = intval($_GET['hiddencatid']);

	$articles = $catids = [];
	$aids = !empty($_GET['ids']) && is_array($_GET['ids']) ? $_GET['ids'] : [];
	if($aids) {
		$query = table_portal_article_title::t()->fetch_all($aids);
		foreach($query as $value) {
			$articles[$value['aid']] = ['aid' => $value['aid'], 'catid' => $value['catid']];
			$catids[] = intval($value['catid']);
		}
	}
	if(empty($articles)) {
		cpmsg('article_choose_at_least_one_article', 'action=article&catid='.$catid.'&perpage='.$perpage.'&page='.$page, 'error');
	}
	$aids = array_keys($articles);

	if($_POST['optype'] == 'trash') {
		require_once libfile('function/delete');
		deletearticle($aids, true);

		cpmsg('article_trash_succeed', 'action=article&catid='.$catid.'&perpage='.$perpage.'&page='.$page, 'succeed');

	} elseif($_POST['optype'] == 'move') {

		$tocatid = intval($_POST['tocatid']);
		$catids[] = $tocatid;
		$catids = array_merge($catids);
		table_portal_article_title::t()->update($aids, ['catid' => $tocatid]);
		foreach($catids as $catid) {
			$catid = intval($catid);
			$cnt = table_portal_article_title::t()->fetch_count_for_cat($catid);
			table_portal_category::t()->update($catid, ['articles' => dintval($cnt)]);
		}
		cpmsg('article_move_succeed', 'action=article&catid='.$catid.'&perpage='.$perpage.'&page='.$page, 'succeed');

	} else {
		cpmsg('article_choose_at_least_one_operation', 'action=article&catid='.$catid.'&perpage='.$perpage.'&page='.$page, 'error');
	}

} else {

	include_once libfile('function/portalcp');

	$mpurl = ADMINSCRIPT.'?action=article&operation='.$operation;

	$intkeys = ['aid', 'uid'];
	$strkeys = [];
	$randkeys = [];
	$likekeys = ['title', 'username'];
	$results = getwheres($intkeys, $strkeys, $randkeys, $likekeys);
	foreach($likekeys as $k) {
		$_GET[$k] = dhtmlspecialchars($_GET[$k]);
	}
	$wherearr = $results['wherearr'];
	$mpurl .= '&'.implode('&', $results['urls']);
	if(!empty($_GET['catid'])) {
		$catid = intval($_GET['catid']);
		$mpurl .= '&catid='.$catid;
		$catids = category_get_childids('portal', $_GET['catid']);
		$catids[] = $_GET['catid'];
		$wherearr[] = 'catid IN ('.dimplode($catids).')';
	}
	$wheresql = empty($wherearr) ? '1' : implode(' AND ', $wherearr);

	$orders = getorders(['dateline'], 'aid');
	$ordersql = $orders['sql'];
	if($orders['urls']) $mpurl .= '&'.implode('&', $orders['urls']);
	$orderby = [$_GET['orderby'] => ' selected'];
	$ordersc = [$_GET['ordersc'] => ' selected'];

	$perpage = empty($_GET['perpage']) ? 0 : intval($_GET['perpage']);
	if(!in_array($perpage, [10, 20, 50, 100])) $perpage = 10;

	$categoryselect = category_showselect('portal', 'catid', true, $_GET['catid']);
	$searchlang = [];
	$keys = ['search', 'likesupport', 'resultsort', 'defaultsort', 'orderdesc', 'orderasc', 'perpage_10', 'perpage_20', 'perpage_50', 'perpage_100',
		'article_dateline', 'article_id', 'article_title', 'article_uid', 'article_username', 'article_category'];
	foreach($keys as $key) {
		$searchlang[$key] = cplang($key);
	}

	$start = ($page - 1) * $perpage;

	$mpurl .= '&perpage='.$perpage;
	$perpages = [$perpage => ' selected'];

	$adminscript = ADMINSCRIPT;
	$staticurl = STATICURL;
	echo <<<SEARCH
		<form method="post" autocomplete="off" action="$adminscript" id="tb_search">
			<table cellspacing="3" cellpadding="3" class="tb tb2">
				<tr>
					<td>{$searchlang['article_id']}</td><td><input type="text" class="txt" name="aid" value="{$_GET['aid']}"></td>
					<td>{$searchlang['article_title']}*</td><td><input type="text" class="txt" name="title" value="{$_GET['title']}">*{$searchlang['likesupport']}</td>
				</tr>
				<tr>
					<td>{$searchlang['article_uid']}</td><td><input type="text" class="txt" name="uid" value="{$_GET['uid']}"></td>
					<td>{$searchlang['article_username']}*</td><td><input type="text" class="txt" name="username" value="{$_GET['username']}"></td>
				</tr>
				<tr>
					<td>{$searchlang['article_category']}</td><td>$categoryselect</td>
					<td>&nbsp;</td><td>&nbsp;</td>
				</tr>
				<tr>
					<td>{$searchlang['resultsort']}</td>
					<td colspan="3">
						<select name="orderby">
						<option value="">{$searchlang['defaultsort']}</option>
						<option value="dateline"{$orderby['dateline']}>{$searchlang['article_dateline']}</option>
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
						<input type="hidden" name="action" value="article">
						<input type="submit" name="searchsubmit" value="{$searchlang['search']}" class="btn">
					</td>
				</tr>
			</table>
		</form>
		<script src="{$staticurl}js/makehtml.js?1" type="text/javascript"></script>

SEARCH;

	$makehtmlflag = !empty($_G['setting']['makehtml']['flag']);
	showformheader('article&operation=list');
	showtableheader('article_list');
	$subtitle = ['', 'article_title', 'article_category', 'article_username', 'article_dateline'];
	if($makehtmlflag) {
		$subtitle[] = 'HTML';
	}
	$subtitle[] = 'operation';
	showsubtitle($subtitle);

	$multipage = '';
	$count = table_portal_article_title::t()->fetch_all_by_sql($wheresql, '', 0, 0, 1);
	if($count) {
		$repairs = [];
		$query = table_portal_article_title::t()->fetch_all_by_sql($wheresql, $ordersql, $start, $perpage);
		foreach($query as $value) {

			$htmlname = $value['htmldir'].$value['htmlname'].'.'.$_G['setting']['makehtml']['extendname'];
			if($makehtmlflag && $value['htmlmade'] && !file_exists(DISCUZ_ROOT.'./'.$htmlname)) {
				$value['htmlmade'] = 0;
				$repairs[$value['aid']] = $value['aid'];
			}

			$tablerow = [
				"<input type=\"checkbox\" class=\"checkbox\" name=\"ids[]\" value=\"{$value['aid']}\">",
				"<a href=\"portal.php?mod=view&aid={$value['aid']}\" target=\"_blank\">{$value['title']}</a>",
				'<a href="'.ADMINSCRIPT.'?action=article&operation=list&catid='.$value['catid'].'">'.$category[$value['catid']]['catname'].'</a>',
				"<a href=\"".ADMINSCRIPT."?action=article&uid={$value['uid']}\">{$value['username']}</a>",
				dgmdate($value['dateline']),
			];
			if($makehtmlflag) {
				$tablerow[] = "<span id='mkhtml_{$value['aid']}' style='color:".($value['htmlmade'] ? "blue;'>".cplang('setting_functions_makehtml_made') : "red;'>".cplang('setting_functions_makehtml_dismake')).'</span>';
			}
			$tablerow[] = ($makehtmlflag ? ($category[$value['catid']]['fullfoldername'] ? "<a href='javascript:void(0);' onclick=\"make_html('portal.php?mod=view&aid={$value['aid']}', $('mkhtml_{$value['aid']}'))\">".cplang('setting_functions_makehtml_make').'</a>' : cplang('setting_functions_makehtml_make_has_no_foldername')) : '')
				." <a href=\"portal.php?mod=portalcp&ac=article&aid={$value['aid']}\" target=\"_blank\">".cplang('edit').'</a>';
			showtablerow('', ['class="td25"', 'width="480"', 'class="td28"'], $tablerow);
		}
		$multipage = multi($count, $perpage, $page, $mpurl);
		if($repairs) {
			table_portal_article_title::t()->repair_htmlmade($repairs);
		}
	}

	$optypehtml = ''
		.'<input type="hidden" name="hiddenpage" id="hiddenpage" value="'.$page.'"/><input type="hidden" name="hiddencatid" id="hiddencatid" value="'.$catid.'"/><input type="hidden" name="hiddenperpage" id="hiddenperpage" value="'.$perpage.'"/><input type="radio" name="optype" id="optype_trash" value="trash" class="radio" /><label for="optype_trash">'.cplang('article_optrash').'</label>&nbsp;&nbsp;'
		.'<input type="radio" name="optype" id="optype_move" value="move" class="radio" /><label for="optype_move">'.cplang('article_opmove').'</label> '
		.category_showselect('portal', 'tocatid', false)
		.'&nbsp;&nbsp;';
	showsubmit('', '', '', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'ids\')" /><label for="chkall">'.cplang('select_all').'</label>&nbsp;&nbsp;'.$optypehtml.'<input type="submit" class="btn" name="articlesubmit" value="'.cplang('submit').'" />', $multipage);
	showtablefooter();
	showformfooter();
}
	