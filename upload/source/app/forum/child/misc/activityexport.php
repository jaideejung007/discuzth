<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$isactivitymaster = $thread['authorid'] == $_G['uid'] ||
	(in_array($_G['group']['radminid'], [1, 2]) || ($_G['group']['radminid'] == 3 && $_G['forum']['ismoderator'])
		&& $_G['group']['alloweditactivity']);
if(!$isactivitymaster) {
	showmessage('activity_is_not_manager');
}

$activity = table_forum_activity::t()->fetch($_G['tid']);
$postinfo = table_forum_post::t()->fetch_threadpost_by_tid_invisible($_G['tid']);
$activity['message'] = $postinfo['message'];
if(empty($activity) || $thread['special'] != 4) {
	showmessage('activity_is_not_exists');
}
$ufield = '';
if($activity['ufield']) {
	$activity['ufield'] = dunserialize($activity['ufield']);
	if($activity['ufield']['userfield']) {
		loadcache('profilesetting');
		foreach($activity['ufield']['userfield'] as $fieldid) {
			$ufield .= ','.$_G['cache']['profilesetting'][$fieldid]['title'];
		}
	}
	if($activity['ufield']['extfield']) {
		foreach($activity['ufield']['extfield'] as $extname) {
			$ufield .= ','.$extname;
		}
	}
}
$activity['starttimefrom'] = dgmdate($activity['starttimefrom'], 'dt');
$activity['starttimeto'] = $activity['starttimeto'] ? dgmdate($activity['starttimeto'], 'dt') : 0;
$activity['expiration'] = $activity['expiration'] ? dgmdate($activity['expiration'], 'dt') : 0;
$activity['message'] = trim(preg_replace('/\[.+?\]/', '', $activity['message']));
$applynumbers = table_forum_activityapply::t()->fetch_count_for_thread($_G['tid']);

$applylist = [];
$query = table_forum_activityapply::t()->fetch_all_for_thread($_G['tid'], 0, 2000, 0, 1);
foreach($query as $apply) {
	$apply = str_replace(',', lang('forum/thread', 't_comma'), $apply);
	$apply['dateline'] = dgmdate($apply['dateline'], 'dt');
	$apply['ufielddata'] = !empty($apply['ufielddata']) ? dunserialize($apply['ufielddata']) : [];
	$ufielddata = '';
	if($apply['ufielddata'] && $activity['ufield']) {
		if($apply['ufielddata']['userfield'] && $activity['ufield']['userfield']) {
			require_once libfile('function/profile');
			loadcache('profilesetting');
			foreach($activity['ufield']['userfield'] as $fieldid) {
				if($fieldid == 'qq') {
					$fieldid = 'qqnumber';
				}
				$data = profile_show($fieldid, $apply['ufielddata']['userfield']);
				if(strlen($data) > 11 && is_numeric($data)) {
					$data = '['.$data.']';
				}
				$ufielddata .= ','.strip_tags(str_replace('&nbsp;', ' ', $data));
			}
		}
		if($activity['ufield']['extfield']) {
			foreach($activity['ufield']['extfield'] as $extname) {
				if(strlen($apply['ufielddata']['extfield'][$extname]) > 11 && is_numeric($apply['ufielddata']['extfield'][$extname])) {
					$apply['ufielddata']['extfield'][$extname] = '['.$apply['ufielddata']['extfield'][$extname].']';
				}
				$ufielddata .= ','.strip_tags(str_replace('&nbsp;', ' ', $apply['ufielddata']['extfield'][$extname]));
			}
		}
	}
	$apply['fielddata'] = $ufielddata;
	if(strlen($apply['message']) > 11 && is_numeric($apply['message'])) {
		$apply['message'] = '['.$apply['message'].']';
	}
	$applylist[] = $apply;
}
$filename = "activity_{$_G['tid']}.csv";

include template('forum/activity_export');
$csvstr = ob_get_contents();
ob_end_clean();
header('Content-Encoding: none');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');
header('Expires: 0');
if($_G['charset'] != 'gbk') {
	$csvstr = diconv($csvstr, $_G['charset'], 'GBK');
}
echo $csvstr;
	