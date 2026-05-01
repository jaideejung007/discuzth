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

shownav('topic', 'nav_doing');
showsubmenu('nav_doing', [
	['setting_home_base', 'doing&operation=base', false],
	['newlist', 'doing', !empty($newlist)],
	['search', 'doing&search=true', empty($newlist)],
]);
empty($newlist) && showsubmenusteps('', [
	['doing_search', !$searchsubmit],
	['nav_doing', $searchsubmit]
]);
/*search={"nav_doing":"action=doing"}*/
if(empty($newlist)) {
	$search_tips = 1;
	showtips('doing_tips');
}
$staticurl = STATICURL;
/*search*/
echo <<<EOT
<script type="text/javascript" src="{$staticurl}js/calendar.js"></script>
<script type="text/JavaScript">
function page(number) {
	$('doingforum').page.value=number;
	$('doingforum').searchsubmit.click();
}
</script>
EOT;
showtagheader('div', 'searchposts', !$searchsubmit && empty($newlist));
/*search={"nav_doing":"action=doing","search":"action=doing&search=true"}*/
showformheader('doing'.(!empty($_GET['search']) ? '&search=true' : ''), '', 'doingforum');
showhiddenfields(['page' => $page, 'pp' => $_GET['pp'] ? $_GET['pp'] : $_GET['perpage']]);
showtableheader();
showsetting('doing_search_detail', 'detail', $detail, 'radio');
showsetting('doing_search_perpage', '', $_GET['perpage'], "<select name='perpage'><option value='20'>{$lang['perpage_20']}</option><option value='50'>{$lang['perpage_50']}</option><option value='100'>{$lang['perpage_100']}</option></select>");
showsetting('doing_search_user', 'users', $users, 'text');
showsetting('doing_search_ip', 'userip', $userip, 'text');
showsetting('doing_search_keyword', 'keywords', $keywords, 'text');
showsetting('doing_search_lengthlimit', 'lengthlimit', $lengthlimit, 'text');
showsetting('doing_search_time', ['starttime', 'endtime'], [$starttime, $endtime], 'daterange');
echo '<input type="hidden" name="fromumanage" value="'.$fromumanage.'">';
showsubmit('searchsubmit');
showtablefooter();
showformfooter();
showtagfooter('div');
/*search*/
	