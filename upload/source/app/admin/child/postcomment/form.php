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
	$starttime = dgmdate(TIMESTAMP - 86400 * 7, 'Y-n-j');
}

$starttime = !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $starttime) ? dgmdate(TIMESTAMP - 86400 * 7, 'Y-n-j') : $starttime;
$endtime = $_G['adminid'] == 3 || !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $endtime) ? dgmdate(TIMESTAMP, 'Y-n-j') : $endtime;

shownav('topic', 'nav_postcomment');
showsubmenu('nav_postcomment', [
	['newlist', 'postcomment', !empty($newlist)],
	['search', 'postcomment&search=true', empty($newlist)],
]);
empty($newlist) && showsubmenusteps('', [
	['postcomment_search', !$searchsubmit],
	['nav_postcomment', $searchsubmit]
]);
/*search={"nav_postcomment":"action=postcomment"}*/
if(empty($newlist)) {
	$search_tips = 1;
	showtips('postcomment_tips');
}
/*search*/
$staticurl = STATICURL;
echo <<<EOT
<script type="text/javascript" src="{$staticurl}js/calendar.js"></script>
<script type="text/JavaScript">
function page(number) {
	$('postcommentforum').page.value=number;
	$('postcommentforum').searchsubmit.click();
}
</script>
EOT;
showtagheader('div', 'searchposts', !$searchsubmit && empty($newlist));
/*search={"nav_postcomment":"action=postcomment","search":"action=postcomment&search=true"}*/
showformheader('postcomment'.(!empty($_GET['search']) ? '&search=true' : ''), '', 'postcommentforum');
showhiddenfields(['page' => $page, 'pp' => $_GET['pp'] ? $_GET['pp'] : $_GET['perpage']]);
showtableheader();
showsetting('postcomment_search_detail', 'detail', $detail, 'radio');
showsetting('comment_search_perpage', '', $_GET['perpage'], "<select name='perpage'><option value='20'>{$lang['perpage_20']}</option><option value='50'>{$lang['perpage_50']}</option><option value='100'>{$lang['perpage_100']}</option></select>");
showsetting('postcomment_content', 'message', $message, 'text');
showsetting('postcomment_search_tid', 'searchtid', $searchtid, 'text');
showsetting('postcomment_search_pid', 'searchpid', $searchpid, 'text');
showsetting('postcomment_search_author', 'author', $author, 'text');
showsetting('postcomment_search_authorid', 'authorid', $authorid, 'text');
showsetting('comment_search_ip', 'ip', $ip, 'text');
showsetting('postcomment_search_time', ['starttime', 'endtime'], [$starttime, $endtime], 'daterange');
showsubmit('searchsubmit');
showtablefooter();
showformfooter();
showtagfooter('div');
/*search*/
	