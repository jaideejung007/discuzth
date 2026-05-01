<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('announcesubmit')) {

	shownav('extended', 'announce', 'admin');
	showsubmenu('announce', [
		['admin', 'announce', 1],
		['add', 'announce&operation=add', 0]
	]);
	showtips('announce_tips');
	showformheader('announce');
	showtableheader();
	showsubtitle(['del', 'display_order', 'author', 'subject', 'message', 'announce_type', 'start_time', 'end_time', '']);

	$announce_type = [0 => $lang['announce_words'], 1 => $lang['announce_url']];
	$annlist = table_forum_announcement::t()->fetch_all_by_displayorder();
	foreach($annlist as $announce) {
		$disabled = $_G['adminid'] != 1 && $announce['author'] != $_G['member']['username'] ? 'disabled' : NULL;
		$announce['starttime'] = $announce['starttime'] ? dgmdate($announce['starttime'], 'Y-n-j H:i') : $lang['unlimited'];
		$announce['endtime'] = $announce['endtime'] ? dgmdate($announce['endtime'], 'Y-n-j H:i') : $lang['unlimited'];
		showtablerow('', ['class="td25"', 'class="td28"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$announce['id']}\" $disabled>",
			"<input type=\"text\" class=\"txt\" name=\"displayordernew[{$announce['id']}]\" value=\"{$announce['displayorder']}\" size=\"2\" $disabled>",
			"<a href=\"./home.php?mod=space&username=".rawurlencode($announce['author'])."\" target=\"_blank\">{$announce['author']}</a>",
			$announce['subject'],
			cutstr(strip_tags($announce['message']), 20),
			$announce_type[$announce['type']],
			$announce['starttime'],
			$announce['endtime'],
			"<a href=\"".ADMINSCRIPT."?action=announce&operation=edit&announceid={$announce['id']}\" $disabled>{$lang['edit']}</a>"
		]);
	}
	showsubmit('announcesubmit', 'submit', 'select_all');
	showtablefooter();
	showformfooter();

} else {

	if(is_array($_GET['delete'])) {
		table_forum_announcement::t()->delete_by_id_username($_GET['delete'], $_G['username'], $_G['adminid']);
	}

	if(is_array($_GET['displayordernew'])) {
		foreach($_GET['displayordernew'] as $id => $displayorder) {
			table_forum_announcement::t()->update_displayorder_by_id_username($id, $displayorder, $_G['username'], $_G['adminid']);
		}
	}

	updatecache(['announcements', 'announcements_forum']);
	cpmsg('announce_update_succeed', 'action=announce', 'succeed');

}
	