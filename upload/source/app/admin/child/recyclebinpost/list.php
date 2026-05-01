<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

shownav('topic', 'nav_recyclebinpost');
showsubmenu('nav_recyclebinpost', [
	['recyclebinpost_list', 'recyclebinpost', 1],
	['search', 'recyclebinpost&operation=search', 0],
	['clean', 'recyclebinpost&operation=clean', 0]
]);
showtagheader('div', 'postlist', 1);
showformheader('recyclebinpost', '', 'rbform');
showhiddenfields(['posttableid' => $posttableid]);
$checklpp = [];
$checklpp[$lpp] = 'selected="selected"';
showtableheader($lang['recyclebinpost_list'].
	'&nbsp<select onchange="if(this.options[this.selectedIndex].value != \'\') {window.location=\''.ADMINSCRIPT.'?action=recyclebinpost&lpp=\'+this.options[this.selectedIndex].value }">
				<option value="20" '.$checklpp[20].'> '.$lang['perpage_20'].' </option><option value="50" '.$checklpp[50].'>'.$lang['perpage_50'].'</option><option value="100" '.$checklpp[100].'>'.$lang['perpage_100'].'</option></select>');

$postlistcount = table_forum_post::t()->count_by_invisible($posttableid, '-5');

if($postlistcount && recyclebinpostshowpostlist(null, null, null, null, null, $start_limit, $lpp)) {
	$multi = multi($postlistcount, $lpp, $page, ADMINSCRIPT."?action=recyclebinpost&lpp=$lpp");
}
showsubmit('rbsubmit', 'submit', '', '<a href="#rb" onclick="checkAll(\'option\', $(\'rbform\'), \'delete\')">'.cplang('recyclebin_all_delete').'</a> &nbsp;<a href="#rb" onclick="checkAll(\'option\', $(\'rbform\'), \'undelete\')">'.cplang('recyclebin_all_undelete').'</a> &nbsp;<a href="#rb" onclick="checkAll(\'option\', $(\'rbform\'), \'ignore\')">'.cplang('recyclebin_all_ignore').'</a> &nbsp;', $multi);
showtablefooter();
showformfooter();
echo '<iframe name="rbframe" style="display:none"></iframe>';
showtagfooter('div');
	