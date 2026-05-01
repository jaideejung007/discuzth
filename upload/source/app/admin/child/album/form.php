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

shownav('topic', 'nav_album');
showsubmenu('nav_album', [
	['newlist', 'album', !empty($newlist)],
	['search', 'album&search=true', empty($newlist)],
]);
empty($newlist) && showsubmenusteps('', [
	['album_search', !$searchsubmit],
	['nav_album', $searchsubmit]
]);
/*search={"nav_album":"action=album","newlist":"action=album"}*/
if($muticondition) {
	showtips('album_tips');
}
/*search*/
$staticurl = STATICURL;
echo <<<EOT
<script type="text/javascript" src="{$staticurl}js/calendar.js"></script>
<script type="text/JavaScript">
function page(number) {
	$('albumforum').page.value=number;
	$('albumforum').searchsubmit.click();
}
</script>
EOT;
showtagheader('div', 'searchposts', !$searchsubmit && empty($newlist));
/*search={"nav_album":"action=album","search":"action=album&search=true"}*/
showformheader('album'.(!empty($_GET['search']) ? '&search=true' : ''), '', 'albumforum');
showhiddenfields(['page' => $page, 'pp' => $_GET['pp'] ? $_GET['pp'] : $_GET['perpage']]);
showtableheader();
showsetting('album_search_detail', 'detail', $detail, 'radio');
showsetting('album_search_perpage', '', $_GET['perpage'], "<select name='perpage'><option value='20'>{$lang['perpage_20']}</option><option value='50'>{$lang['perpage_50']}</option><option value='100'>{$lang['perpage_100']}</option></select>");
showsetting('resultsort', '', $orderby, "<select name='orderby'><option value=''>{$lang['defaultsort']}</option><option value='dateline'>{$lang['topic_dateline']}</option><option value='updatetime'>{$lang['updatetime']}</option><option value='picnum'>{$lang['pic_num']}</option></select> ");
showsetting('', '', $ordersc, "<select name='ordersc'><option value='desc'>{$lang['orderdesc']}</option><option value='asc'>{$lang['orderasc']}</option></select>", '', 0, '', '', '', true);
showsetting('album_search_albumname', 'albumname', $albumname, 'text');
showsetting('album_search_albumid', 'albumid', $albumid, 'text');
showsetting('album_search_uid', 'uid', $uid, 'text');
showsetting('album_search_user', 'users', $users, 'text');
showsetting('blog_search_friend', '', $friend, "<select name='friend'><option value='0'>{$lang['setting_home_privacy_alluser']}</option><option value='1'>{$lang['setting_home_privacy_friend']}</option><option value='2'>{$lang['setting_home_privacy_specified_friend']}</option><option value='3'>{$lang['setting_home_privacy_self']}</option><option value='4'>{$lang['setting_home_privacy_password']}</option></select>");
showsetting('album_search_time', ['starttime', 'endtime'], [$starttime, $endtime], 'daterange');
echo '<input type="hidden" name="fromumanage" value="'.$fromumanage.'">';
showsubmit('searchsubmit');
showtablefooter();
showformfooter();
showtagfooter('div');
/*search*/
	