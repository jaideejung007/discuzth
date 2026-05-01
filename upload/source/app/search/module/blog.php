<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
const NOROBOT = true;

require_once libfile('function/home');

if(!$_G['setting']['search']['blog']['status']) {
	showmessage('search_blog_closed');
}

if($_G['adminid'] != 1 && !($_G['group']['allowsearch'] & 4)) {
	showmessage('group_nopermission', NULL, ['grouptitle' => $_G['group']['grouptitle']], ['login' => 1]);
}

$_G['setting']['search']['blog']['searchctrl'] = intval($_G['setting']['search']['blog']['searchctrl']);

$srchmod = 3;

$cachelife_time = 300;                
$cachelife_text = 3600;                

$srchtype = empty($_GET['srchtype']) ? '' : trim($_GET['srchtype']);
$searchid = isset($_GET['searchid']) ? intval($_GET['searchid']) : 0;


$srchtxt = $_GET['srchtxt'];

$keyword = isset($srchtxt) ? dhtmlspecialchars(trim($srchtxt)) : '';

if(!submitcheck('searchsubmit', 1)) {

	include template('search/blog');

} else {

	$orderby = in_array($_GET['orderby'], ['dateline', 'replies', 'views']) ? $_GET['orderby'] : 'lastpost';
	$ascdesc = isset($_GET['ascdesc']) && $_GET['ascdesc'] == 'asc' ? 'asc' : 'desc';

	if(!empty($searchid)) {

		$page = max(1, intval($_GET['page']));
		$start_limit = ($page - 1) * $_G['tpp'];

		$index = table_common_searchindex::t()->fetch_by_searchid_srchmod($searchid, $srchmod);
		if(!$index) {
			showmessage('search_id_invalid');
		}

		$keyword = dhtmlspecialchars($index['keywords']);
		$keyword = $keyword != '' ? str_replace('+', ' ', $keyword) : '';

		$index['keywords'] = rawurlencode($index['keywords']);
		$result = [];
		$bloglist = [];
		$pricount = 0;
		$blogidarray = explode(',', $index['ids']);
		$data_blog = table_home_blog::t()->fetch_all_blog($blogidarray, 'dateline', 'DESC', $start_limit, $_G['tpp']);
		$data_blogfield = table_home_blogfield::t()->fetch_all($blogidarray);

		foreach($data_blog as $curblogid => $value) {
			$result = array_merge($result, (array)$data_blogfield[$curblogid]);
			if(ckfriend($value['uid'], $value['friend'], $value['target_ids']) && ($value['status'] == 0 || $value['uid'] == $_G['uid'] || $_G['adminid'] == 1)) {
				if($value['friend'] == 4) {
					$value['message'] = $value['pic'] = '';
				} else {
					$value['message'] = bat_highlight($value['message'], $keyword);
					$value['message'] = getstr($value['message'], 255, 0, 0, 0, -1);
				}
				$value['subject'] = bat_highlight($value['subject'], $keyword);
				$value['dateline'] = dgmdate($value['dateline']);
				$value['pic'] = pic_cover_get($value['pic'], $value['picflag']);
				$bloglist[] = $value;
			} else {
				$pricount++;
			}
		}
		$multipage = multi($index['num'], $_G['tpp'], $page, "search.php?mod=blog&searchid=$searchid&orderby=$orderby&ascdesc=$ascdesc&searchsubmit=yes");

		$url_forward = 'search.php?mod=blog&'.$_SERVER['QUERY_STRING'];

		include template('search/blog');

	} else {

		$searchstring = 'blog|title|'.addslashes($srchtxt);
		$searchindex = ['id' => 0, 'dateline' => '0'];

		foreach(table_common_searchindex::t()->fetch_all_search($_G['setting']['search']['blog']['searchctrl'], $_G['clientip'], $_G['uid'], $_G['timestamp'], $searchstring, $srchmod) as $index) {
			if($index['indexvalid'] && $index['dateline'] > $searchindex['dateline']) {
				$searchindex = ['id' => $index['searchid'], 'dateline' => $index['dateline']];
				break;
			} elseif($_G['adminid'] != '1' && $index['flood']) {
				showmessage('search_ctrl', 'search.php?mod=blog', ['searchctrl' => $_G['setting']['search']['blog']['searchctrl']]);
			}
		}

		if($searchindex['id']) {

			$searchid = $searchindex['id'];

		} else {

			!($_G['group']['exempt'] & 2) && checklowerlimit('search');

			if(!$srchtxt && !$srchuid && !$srchuname) {
				dheader('Location: search.php?mod=blog');
			}

			if($_G['adminid'] != '1' && $_G['setting']['search']['blog']['maxspm']) {
				if(table_common_searchindex::t()->count_by_dateline($_G['timestamp'], $srchmod) >= $_G['setting']['search']['blog']['maxspm']) {
					showmessage('search_toomany', 'search.php?mod=blog', ['maxspm' => $_G['setting']['search']['blog']['maxspm']]);
				}
			}

			$num = $ids = 0;
			$_G['setting']['search']['blog']['maxsearchresults'] = $_G['setting']['search']['blog']['maxsearchresults'] ? intval($_G['setting']['search']['blog']['maxsearchresults']) : 500;
			[$srchtxt, $srchtxtsql] = searchkey($keyword, "subject LIKE '%{text}%'", true);
			$query = table_home_blog::t()->fetch_blogid_by_subject($keyword, $_G['setting']['search']['blog']['maxsearchresults']);
			foreach($query as $blog) {
				$ids .= ','.$blog['blogid'];
				$num++;
			}
			unset($query);

			$keywords = str_replace('%', '+', $srchtxt);
			$expiration = TIMESTAMP + $cachelife_text;

			$searchid = table_common_searchindex::t()->insert([
				'srchmod' => $srchmod,
				'keywords' => $keywords,
				'searchstring' => $searchstring,
				'useip' => $_G['clientip'],
				'uid' => $_G['uid'],
				'dateline' => $_G['timestamp'],
				'expiration' => $expiration,
				'num' => $num,
				'ids' => $ids
			], true);

			!($_G['group']['exempt'] & 2) && updatecreditbyaction('search');
		}

		dheader("location: search.php?mod=blog&searchid=$searchid&searchsubmit=yes&kw=".urlencode($keyword));

	}

}

