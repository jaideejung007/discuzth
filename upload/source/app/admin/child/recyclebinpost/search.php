<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$inforum = $_GET['inforum'];
$authors = $_GET['authors'];
$keywords = $_GET['keywords'];
$pstarttime = $_GET['pstarttime'];
$pendtime = $_GET['pendtime'];

$searchsubmit = $_GET['searchsubmit'];

require_once libfile('function/forumlist');

$forumselect = '<select name="inforum"><option value="">&nbsp;&nbsp;> '.$lang['allthread'].'</option>'.
	'<option value="">&nbsp;</option>'.forumselect(FALSE, 0, 0, TRUE).'</select>';

if($inforum) {
	$forumselect = preg_replace("/(\<option value=\"$inforum\")(\>)/", "\\1 selected=\"selected\" \\2", $forumselect);
}

shownav('topic', 'nav_recyclebinpost');
showsubmenu('nav_recyclebinpost', [
	['recyclebinpost_list', 'recyclebinpost', 0],
	['search', 'recyclebinpost&operation=search', 1],
	['clean', 'recyclebinpost&operation=clean', 0]
]);
/*search={"nav_recyclebinpost":"action=recyclebinpost","search":"action=recyclebinpost&operation=search"}*/
$staticurl = STATICURL;
echo <<<EOT
<script type="text/javascript" src="{$staticurl}js/calendar.js"></script>
<script type="text/JavaScript">
function page(number) {
	$('rbsearchform').page.value=number;
	$('rbsearchform').searchsubmit.click();
}
</script>
EOT;
showtagheader('div', 'postsearch', !$searchsubmit);
showformheader('recyclebinpost&operation=search', '', 'rbsearchform');
showhiddenfields(['page' => $page]);
showtableheader('recyclebinpost_search');
showsetting('recyclebinpost_search_forum', '', '', $forumselect);
showsetting('recyclebinpost_search_author', 'authors', $authors, 'text');
showsetting('recyclebinpost_search_keyword', 'keywords', $keywords, 'text');
showsetting('recyclebin_search_post_time', ['pstarttime', 'pendtime'], [$pstarttime, $pendtime], 'daterange');
showsetting('postsplit', '', '', getposttableselect_admin());
showsubmit('searchsubmit');
showtablefooter();
showformfooter();
showtagfooter('div');
/*search*/

if(submitcheck('searchsubmit')) {

	$postlistcount = table_forum_post::t()->count_by_search($posttableid, null, $keywords, -5, $inforum, null, ($authors ? explode(',', str_replace(' ', '', $authors)) : null), strtotime($pstarttime), strtotime($pendtime));

	showtagheader('div', 'postlist', $searchsubmit);
	showformheader('recyclebinpost&operation=search&frame=no', 'target="rbframe"', 'rbform');
	showtableheader(cplang('recyclebinpost_result').' '.$postlistcount.' <a href="#" onclick="$(\'postlist\').style.display=\'none\';$(\'postsearch\').style.display=\'\';" class="act lightlink normal">'.cplang('research').'</a>', 'fixpadding');

	if($postlistcount && recyclebinpostshowpostlist($inforum, $authors, $pstarttime, $pendtime, $keywords, $start_limit, $lpp)) {
		$multi = multi($postlistcount, $lpp, $page, ADMINSCRIPT.'?action=recyclebinpost');
		$multi = preg_replace("/href=\"".ADMINSCRIPT."\?action=recyclebinpost&amp;page=(\d+)\"/", "href=\"javascript:page(\\1)\"", $multi);
		$multi = str_replace("window.location='".ADMINSCRIPT."?action=recyclebinpost&amp;page='+this.value", 'page(this.value)', $multi);
	}

	showsubmit('rbsubmit', 'submit', '', '<a href="#rb" onclick="checkAll(\'option\', $(\'rbform\'), \'delete\')">'.cplang('recyclebin_all_delete').'</a> &nbsp;<a href="#rb" onclick="checkAll(\'option\', $(\'rbform\'), \'undelete\')">'.cplang('recyclebin_all_undelete').'</a> &nbsp;<a href="#rb" onclick="checkAll(\'option\', $(\'rbform\'), \'ignore\')">'.cplang('recyclebin_all_ignore').'</a> &nbsp;', $multi);
	showtablefooter();
	showformfooter();
	echo '<iframe name="rbframe" style="display:none"></iframe>';
	showtagfooter('div');
}
	