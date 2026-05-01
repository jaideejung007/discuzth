<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once libfile('function/forumlist');

if($_G['adminid'] == 1 || $_G['adminid'] == 2) {
	$forumselect = '<select name="forums"><option value="">&nbsp;&nbsp;> '.$lang['select'].'</option>'.
		'<option value="">&nbsp;</option>'.forumselect(FALSE, 0, 0, TRUE).'</select>';

	if($_GET['forums']) {
		$forumselect = preg_replace("/(\<option value=\"{$_GET['forums']}\")(\>)/", "\\1 selected=\"selected\" \\2", $forumselect);
	}
} else {
	$forumselect = $comma = '';
	$mfids = [];
	foreach(table_forum_moderator::t()->fetch_all_by_uid($_G['uid']) as $row) {
		$mfids[] = $row['fid'];
	}
	$query = table_forum_forum::t()->fetch_all_by_fid($mfids);
	foreach($query as $forum) {
		$forumselect .= $comma.$forum['name'];
		$comma = ', ';
	}
	$forumselect = $forumselect ? $forumselect : $lang['none'];
}

if($fromumanage) {
	$starttime = !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $_GET['starttime']) ? '' : $_GET['starttime'];
	$endtime = $_G['adminid'] == 3 || !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $_GET['endtime']) ? '' : $_GET['endtime'];
} else {
	$starttime = !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $_GET['starttime']) ? dgmdate(TIMESTAMP - 86400 * 7, 'Y-n-j') : $_GET['starttime'];
	$endtime = $_G['adminid'] == 3 || !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $_GET['endtime']) ? dgmdate(TIMESTAMP, 'Y-n-j') : $_GET['endtime'];
}

shownav('topic', 'nav_prune'.($operation ? '_'.$operation : ''));
showsubmenusteps('nav_prune'.($operation ? '_'.$operation : ''), [
	['prune_search', !$searchsubmit],
	['nav_prune', $searchsubmit]
]);
/*search={"nav_prune":"action=prune"}*/
showtips('prune_tips');
echo <<<EOT
<script type="text/javascript" src="static/js/calendar.js"></script>
<script type="text/JavaScript">
function page(number) {
	$('pruneforum').page.value=number;
	$('pruneforum').searchsubmit.click();
}
</script>
EOT;

$posttableselect = getposttableselect_admin();
showtagheader('div', 'searchposts', !$searchsubmit);
showformheader('prune'.($operation ? '&operation='.$operation : ''), '', 'pruneforum');
showhiddenfields(['page' => $page, 'pp' => $_GET['pp'] ? $_GET['pp'] : $_GET['perpage']]);
showtableheader();
showsetting('prune_search_detail', 'detail', $_GET['detail'], 'radio');
if($posttableselect) {
	showsetting('prune_search_select_postsplit', '', '', $posttableselect);
}
if($operation != 'group') {
	showsetting('prune_search_forum', '', '', $forumselect);
}
showsetting('prune_search_perpage', '', $_GET['perpage'], "<select name='perpage'><option value='20'>{$lang['perpage_20']}</option><option value='50'>{$lang['perpage_50']}</option><option value='100'>{$lang['perpage_100']}</option></select>");
if(!$fromumanage) {
	empty($_GET['starttime']) && $_GET['starttime'] = dgmdate(TIMESTAMP - 86400 * 7, 'Y-n-j');
}
echo '<input type="hidden" name="fromumanage" value="'.$fromumanage.'">';
showsetting('prune_search_time', ['starttime', 'endtime'], [$_GET['starttime'], $_GET['endtime']], 'daterange');
showsetting('prune_search_user', 'users', $_GET['users'], 'text');
showsetting('prune_search_ip', 'useip', $_GET['useip'], 'text');
showsetting('prune_search_keyword', 'keywords', $_GET['keywords'], 'text');
showsetting('prune_search_lengthlimit', 'lengthlimit', $_GET['lengthlimit'], 'text');
showsubmit('searchsubmit');
showtablefooter();
showformfooter();
showtagfooter('div');
/*search*/
	