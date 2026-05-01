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

if(!$_G['setting']['search']['album']['status']) {
	showmessage('search_album_closed');
}

if($_G['adminid'] != 1 && !($_G['group']['allowsearch'] & 8)) {
	showmessage('group_nopermission', NULL, ['grouptitle' => $_G['group']['grouptitle']], ['login' => 1]);
}

$_G['setting']['search']['album']['searchctrl'] = intval($_G['setting']['search']['album']['searchctrl']);

$srchmod = 4;

$cachelife_time = 300;                
$cachelife_text = 3600;                

$srchtype = empty($_GET['srchtype']) ? '' : trim($_GET['srchtype']);
$searchid = isset($_GET['searchid']) ? intval($_GET['searchid']) : 0;

$srchtxt = $_GET['srchtxt'];
$keyword = isset($srchtxt) ? dhtmlspecialchars(trim($srchtxt)) : '';

if(!submitcheck('searchsubmit', 1)) {

	include template('search/album');

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

		$albumlist = [];
		$maxalbum = $nowalbum = 0;
		$query = table_home_album::t()->fetch_all_album(explode(',', $index['ids']), 'updatetime', $start_limit, $_G['tpp']);
		foreach($query as $value) {
			if($value['friend'] != 4 && ckfriend($value['uid'], $value['friend'], $value['target_ids'])) {
				$value['pic'] = pic_cover_get($value['pic'], $value['picflag']);
			} elseif($value['picnum']) {
				$value['pic'] = STATICURL.'image/common/nopublish.jpg';
			} else {
				$value['pic'] = '';
			}
			$value['albumname'] = bat_highlight($value['albumname'], $keyword);
			$albumlist[$value['albumid']] = $value;
		}

		$multipage = multi($index['num'], $_G['tpp'], $page, "search.php?mod=album&searchid=$searchid&orderby=$orderby&ascdesc=$ascdesc&searchsubmit=yes");

		$url_forward = 'search.php?mod=album&'.$_SERVER['QUERY_STRING'];

		include template('search/album');

	} else {

		$searchstring = 'album|title|'.addslashes($srchtxt);
		$searchindex = ['id' => 0, 'dateline' => '0'];

		foreach(table_common_searchindex::t()->fetch_all_search($_G['setting']['search']['album']['searchctrl'], $_G['clientip'], $_G['uid'], $_G['timestamp'], $searchstring, $srchmod) as $index) {
			if($index['indexvalid'] && $index['dateline'] > $searchindex['dateline']) {
				$searchindex = ['id' => $index['searchid'], 'dateline' => $index['dateline']];
				break;
			} elseif($_G['adminid'] != '1' && $index['flood']) {
				showmessage('search_ctrl', 'search.php?mod=album', ['searchctrl' => $_G['setting']['search']['album']['searchctrl']]);
			}
		}

		if($searchindex['id']) {

			$searchid = $searchindex['id'];

		} else {

			!($_G['group']['exempt'] & 2) && checklowerlimit('search');

			if(!$srchtxt && !$srchuid && !$srchuname) {
				dheader('Location: search.php?mod=album');
			}

			if($_G['adminid'] != '1' && $_G['setting']['search']['album']['maxspm']) {
				if(table_common_searchindex::t()->count_by_dateline($_G['timestamp'], $srchmod) >= $_G['setting']['search']['album']['maxspm']) {
					showmessage('search_toomany', 'search.php?mod=album', ['maxspm' => $_G['setting']['search']['album']['maxspm']]);
				}
			}

			$num = $ids = 0;
			$_G['setting']['search']['album']['maxsearchresults'] = $_G['setting']['search']['album']['maxsearchresults'] ? intval($_G['setting']['search']['album']['maxsearchresults']) : 500;
			[$srchtxt, $srchtxtsql] = searchkey($keyword, "albumname LIKE '%{text}%'", true);
			$query = table_home_album::t()->fetch_albumid_by_searchkey($srchtxtsql, $_G['setting']['search']['album']['maxsearchresults']);
			foreach($query as $album) {
				$ids .= ','.$album['albumid'];
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

		dheader("location: search.php?mod=album&searchid=$searchid&searchsubmit=yes&kw=".urlencode($keyword));

	}

}

