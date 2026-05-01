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

shownav('topic', 'nav_share');
showsubmenu('nav_share', [
	['newlist', 'share', !empty($newlist)],
	['search', 'share&search=true', empty($newlist)],
]);
empty($newlist) && showsubmenusteps('', [
	['share_search', !$searchsubmit],
	['nav_share', $searchsubmit]
]);
/*search={"nav_share":"action=share"}*/
showtips('share_tips');
/*search*/
$staticurl = STATICURL;
echo <<<EOT
<script type="text/javascript" src="{$staticurl}js/calendar.js"></script>
<script type="text/JavaScript">
function page(number) {
	$('shareforum').page.value=number;
	$('shareforum').searchsubmit.click();
}
</script>
EOT;
showtagheader('div', 'searchposts', !$searchsubmit && empty($newlist));
/*search={"nav_share":"action=share","search":"action=share&search=true"}*/
showformheader('share'.(!empty($_GET['search']) ? '&search=true' : ''), '', 'shareforum');
showhiddenfields(['page' => $page, 'pp' => $_GET['pp'] ? $_GET['pp'] : $_GET['perpage']]);
showtableheader();
showsetting('share_search_detail', 'detail', $detail, 'radio');
showsetting('share_search_perpage', '', $_GET['perpage'], "<select name='perpage'><option value='20'>{$lang['perpage_20']}</option><option value='50'>{$lang['perpage_50']}</option><option value='100'>{$lang['perpage_100']}</option></select>");
$selected[$type] = $type ? 'selected="selected"' : '';
showsetting('share_search_icon', '', $type, "<select name='type'><option value=''>{$lang['all']}</option><option value='link' {$selected['link']}>{$lang['link']}</option>
			<option value='video' {$selected['video']}>{$lang['video']}</option><option value='music' {$selected['music']}>{$lang['music']}</option><option value='flash' {$selected['flash']}}>Flash</option>
			<option value='blog' {$selected['blog']}>{$lang['blogs']}</option><option value='album' {$selected['album']}>{$lang['albums']}</option><option value='pic' {$selected['pic']}>{$lang['pics']}</option>
			<option value='space' {$selected['space']}>{$lang['members']}</option><option value='thread' {$selected['thread']}>{$lang['thread']}</option></select>");
showsetting('share_search_uid', 'uid', $uid, 'text');
showsetting('share_search_user', 'users', $users, 'text');
showsetting('share_search_sid', 'sid', $sid, 'text');
showsetting('share_search_hot', ['hot1', 'hot2'], ['', ''], 'range');
showsetting('share_search_time', ['starttime', 'endtime'], [$starttime, $endtime], 'daterange');
echo '<input type="hidden" name="fromumanage" value="'.$fromumanage.'">';
showsubmit('searchsubmit');
showtablefooter();
showformfooter();
showtagfooter('div');
/*search*/
	