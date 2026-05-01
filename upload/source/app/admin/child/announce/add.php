<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('addsubmit')) {

	$newstarttime = dgmdate(TIMESTAMP, 'Y-n-j H:i');
	$newendtime = dgmdate(TIMESTAMP + 86400 * 7, 'Y-n-j H:i');

	shownav('extended', 'announce', 'add');
	showsubmenu('announce', [
		['admin', 'announce', 0],
		['add', 'announce&operation=add', 1]
	]);
	showformheader('announce&operation=add');
	showtableheader('announce_add');
	showsetting($lang['subject'], 'newsubject', '', 'htmltext');
	showsetting($lang['start_time'], 'newstarttime', $newstarttime, 'calendar', '', 0, '', 1);
	showsetting($lang['end_time'], 'newendtime', $newendtime, 'calendar', '', 0, '', 1);
	showsetting('announce_type', ['newtype', [
		[0, $lang['announce_words']],
		[1, $lang['announce_url']]]], 0, 'mradio');
	showsetting('announce_message', 'newmessage', '', 'textarea');
	showsubmit('addsubmit');
	showtablefooter();
	showformfooter();

} else {

	$newstarttime = $_GET['newstarttime'] ? strtotime($_GET['newstarttime']) : 0;
	$newendtime = $_GET['newendtime'] ? strtotime($_GET['newendtime']) : 0;
	if($newendtime && $newstarttime > $newendtime) {
		cpmsg('announce_time_invalid', '', 'error');
	}
	$newsubject = trim($_GET['newsubject']);
	$newmessage = trim($_GET['newmessage']);
	if(!$newstarttime) {
		cpmsg('announce_time_invalid', '', 'error');
	} elseif(!$newsubject || !$newmessage) {
		cpmsg('announce_invalid', '', 'error');
	} else {
		$newmessage = $_GET['newtype'] == 1 ? explode("\n", $_GET['newmessage']) : [0 => $_GET['newmessage']];
		$data = [
			'author' => $_G['username'],
			'subject' => strip_tags($newsubject, '<u><i><b><font>'),
			'type' => $_GET['newtype'],
			'starttime' => $newstarttime,
			'endtime' => $newendtime,
			'message' => $newmessage[0],
		];
		table_forum_announcement::t()->insert($data);
		updatecache(['announcements', 'announcements_forum']);
		cpmsg('announce_succeed', 'action=announce', 'succeed');
	}

}
	