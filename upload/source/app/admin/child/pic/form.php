<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($_GET['search'])) {
	$newlist = 1;
	$detail = 1;
}

if($fromumanage) {
	$starttime = !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $starttime) ? '' : $starttime;
	$endtime = $_G['adminid'] == 3 || !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $endtime) ? '' : $endtime;
} else {
	$starttime = !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $starttime) ? dgmdate(TIMESTAMP - 86400 * 7, 'Y-n-j') : $starttime;
	$endtime = $_G['adminid'] == 3 || !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $endtime) ? dgmdate(TIMESTAMP, 'Y-n-j') : $endtime;
}

shownav('topic', 'nav_pic');
showsubmenu('nav_pic', [
	['newlist', 'pic', !empty($newlist)],
	['search', 'pic&search=true', empty($newlist)],
]);
empty($newlist) && showsubmenusteps('', [
	['pic_search', !$searchsubmit],
	['nav_pic', $searchsubmit]
]);
/*search={"nav_pic":"action=pic"}*/
if($muticondition) {
	showtips('pic_tips');
}
/*search*/
$staticurl = STATICURL;
echo <<<EOT
<script type="text/javascript" src="{$staticurl}js/calendar.js"></script>
<script type="text/JavaScript">
function page(number) {
	$('picforum').page.value=number;
	$('picforum').searchsubmit.click();
}
</script>
EOT;
showtagheader('div', 'searchposts', !$searchsubmit && empty($newlist));
/*search={"nav_pic":"action=pic","search":"action=pic&search=true"}*/
showformheader('pic'.(!empty($_GET['search']) ? '&search=true' : ''), '', 'picforum');
showhiddenfields(['page' => $page, 'pp' => $_GET['pp'] ? $_GET['pp'] : $_GET['perpage']]);
showtableheader();
showsetting('pic_search_detail', 'detail', $detail, 'radio');
showsetting('pic_search_perpage', '', $_GET['perpage'], "<select name='perpage'><option value='20'>{$lang['perpage_20']}</option><option value='50'>{$lang['perpage_50']}</option><option value='100'>{$lang['perpage_100']}</option></select>");
showsetting('resultsort', '', $orderby, "<select name='orderby'><option value=''>{$lang['defaultsort']}</option><option value='dateline'>{$lang['pic_search_createtime']}</option><option value='size'>{$lang['pic_size']}</option><option value='hot'>{$lang['pic_search_hot']}</option></select> ");
showsetting('', '', $ordersc, "<select name='ordersc'><option value='desc'>{$lang['orderdesc']}</option><option value='asc'>{$lang['orderasc']}</option></select>", '', 0, '', '', '', true);
showsetting('pic_search_albumid', 'albumid', $albumid, 'text');
showsetting('pic_search_user', 'users', $users, 'text');
showsetting('pic_search_picid', 'picid', $picid, 'text');
showsetting('pic_search_title', 'title', $title, 'text');
showsetting('pic_search_ip', 'postip', $postip, 'text');
showsetting('pic_search_hot', ['hot1', 'hot2'], ['', ''], 'range');
showsetting('pic_search_time', ['starttime', 'endtime'], [$starttime, $endtime], 'daterange');
echo '<input type="hidden" name="fromumanage" value="'.$fromumanage.'">';
showsubmit('searchsubmit');
showtablefooter();
showformfooter();
showtagfooter('div');
/*search*/
	