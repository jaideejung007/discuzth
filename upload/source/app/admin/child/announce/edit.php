<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($_GET['announceid'])) {
	cpmsg('undefined_action');
}

$announce = table_forum_announcement::t()->fetch_by_id_username($_GET['announceid'], $_G['username'], $_G['adminid']);
if(!$announce) {
	cpmsg('announce_nonexistence', '', 'error');
}

if(!submitcheck('editsubmit')) {

	$announce['starttime'] = $announce['starttime'] ? dgmdate($announce['starttime'], 'Y-n-j H:i') : '';
	$announce['endtime'] = $announce['endtime'] ? dgmdate($announce['endtime'], 'Y-n-j H:i') : '';
	$b = $i = $u = $colorselect = $colorcheck = '';
	if(preg_match('/<b>(.*?)<\/b>/i', $announce['subject'])) {
		$b = 'class="a"';
	}
	if(preg_match('/<i>(.*?)<\/i>/i', $announce['subject'])) {
		$i = 'class="a"';
	}
	if(preg_match('/<u>(.*?)<\/u>/i', $announce['subject'])) {
		$u = 'class="a"';
	}
	$colorselect = preg_replace('/<font color=(.*?)>(.*?)<\/font>/i', '$1', $announce['subject']);
	$colorselect = strip_tags($colorselect);
	$_G['forum_colorarray'] = [1 => '#EE1B2E', 2 => '#EE5023', 3 => '#996600', 4 => '#3C9D40', 5 => '#2897C5', 6 => '#2B65B7', 7 => '#8F2A90', 8 => '#EC1282'];
	if(in_array($colorselect, $_G['forum_colorarray'])) {
		$colorcheck = "style=\"background: $colorselect\"";
	}

	shownav('extended', 'announce');
	showchildmenu([['announce', 'announce']], $announce['subject']);
	showformheader("announce&operation=edit&announceid={$_GET['announceid']}");
	showtableheader();
	/*search={"announce":"action=announce"}*/
	showtitle('announce_edit');
	showsetting($lang['subject'], 'newsubject', $announce['subject'], 'htmltext');
	showsetting('start_time', 'starttimenew', $announce['starttime'], 'calendar', '', 0, '', 1);
	showsetting('end_time', 'endtimenew', $announce['endtime'], 'calendar', '', 0, '', 1);
	showsetting('announce_type', ['typenew', [
		[0, $lang['announce_words']],
		[1, $lang['announce_url']]
	]], $announce['type'], 'mradio');
	showsetting('announce_message', 'messagenew', $announce['message'], 'textarea');
	showsubmit('editsubmit');
	showtablefooter();
	/*search*/
	showformfooter();

} else {

	if(strpos($_GET['starttimenew'], '-')) {
		$starttimenew = strtotime($_GET['starttimenew']);
	} else {
		$starttimenew = 0;
	}
	if(strpos($_GET['endtimenew'], '-')) {
		$endtimenew = strtotime($_GET['endtimenew']);
	} else {
		$endtimenew = 0;
	}
	$subjectnew = trim($_GET['newsubject']);
	$messagenew = trim($_GET['messagenew']);
	if(!$starttimenew || ($endtimenew && $endtimenew <= TIMESTAMP) || $endtimenew && $starttimenew > $endtimenew) {
		cpmsg('announce_time_invalid', '', 'error');
	} elseif(!$subjectnew || !$messagenew) {
		cpmsg('announce_invalid', '', 'error');
	} else {
		$messagenew = $_GET['typenew'] == 1 ? explode("\n", $messagenew) : [0 => $messagenew];
		table_forum_announcement::t()->update_by_id_username($_GET['announceid'], [
			'subject' => strip_tags($subjectnew, '<u><i><b><font>'),
			'type' => $_GET['typenew'],
			'starttime' => $starttimenew,
			'endtime' => $endtimenew,
			'message' => $messagenew[0],
		], $_G['username'], $_G['adminid']);

		updatecache(['announcements', 'announcements_forum']);
		cpmsg('announce_succeed', 'action=announce', 'succeed');
	}
}
	