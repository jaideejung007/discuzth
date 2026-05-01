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
if(empty($activity) || $thread['special'] != 4) {
	showmessage('activity_is_not_exists');
}

if(!submitcheck('applylistsubmit')) {
	$applylist = [];
	$activity['ufield'] = $activity['ufield'] ? dunserialize($activity['ufield']) : [];
	$query = table_forum_activityapply::t()->fetch_all_for_thread($_G['tid'], 0, 500, $_GET['uid'], $isactivitymaster);
	foreach($query as $activityapplies) {
		$ufielddata = '';
		$activityapplies['dateline'] = dgmdate($activityapplies['dateline'], 'u');
		$activityapplies['ufielddata'] = !empty($activityapplies['ufielddata']) ? dunserialize($activityapplies['ufielddata']) : [];
		if($activityapplies['ufielddata']) {
			if($activityapplies['ufielddata']['userfield']) {
				require_once libfile('function/profile');
				loadcache('profilesetting');
				$data = '';
				foreach($activity['ufield']['userfield'] as $fieldid) {
					if($fieldid == 'qq') {
						$fieldid = 'qqnumber';
					}
					$data = profile_show($fieldid, $activityapplies['ufielddata']['userfield']);
					$ufielddata .= '<li>'.$_G['cache']['profilesetting'][$fieldid]['title'].'&nbsp;&nbsp;:&nbsp;&nbsp;';
					if(empty($data)) {
						$ufielddata .= '</li>';
						continue;
					}
					if($_G['cache']['profilesetting'][$fieldid]['formtype'] != 'file') {
						$ufielddata .= $data;
					} else {
						$ufielddata .= '<a href="'.$data.'" target="_blank" onclick="zoom(this, this.href, 0, 0, 0); return false;">'.lang('forum/misc', 'activity_viewimg').'</a>';
					}
					$ufielddata .= '</li>';
				}
			}
			if($activityapplies['ufielddata']['extfield']) {
				foreach($activity['ufield']['extfield'] as $name) {
					$ufielddata .= '<li>'.$name.'&nbsp;&nbsp;:&nbsp;&nbsp;'.$activityapplies['ufielddata']['extfield'][$name].'</li>';
				}
			}
		}
		$activityapplies['ufielddata'] = $ufielddata;
		$applylist[] = $activityapplies;
	}

	$activity['starttimefrom'] = dgmdate($activity['starttimefrom'], 'u');
	$activity['starttimeto'] = $activity['starttimeto'] ? dgmdate($activity['starttimeto'], 'u') : 0;
	$activity['expiration'] = $activity['expiration'] ? dgmdate($activity['expiration'], 'u') : 0;

	include template('forum/activity_applylist');
} else {
	if(empty($_GET['applyidarray'])) {
		showmessage('activity_choice_applicant');
	} else {
		$reason = cutstr(dhtmlspecialchars($_GET['reason']), 200);
		$tempuid = $uidarray = $unverified = [];
		$query = table_forum_activityapply::t()->fetch_all($_GET['applyidarray']);
		foreach($query as $row) {
			if($row['tid'] == $_G['tid']) {
				$tempusers[$row['uid']] = $row;
			}
		}
		$query = table_common_member::t()->fetch_all(array_keys($tempusers));
		foreach($query as $user) {
			$uidarray[] = $user['uid'];
			if(is_array($tempusers[$user['uid']]) && $tempusers[$user['uid']]['verified'] != 1) {
				$unverified[] = $user['uid'];
			}
		}
		$activity_subject = $thread['subject'];

		if($_GET['operation'] == 'notification') {
			if(empty($uidarray)) {
				showmessage('activity_notification_user');
			}
			if(empty($reason)) {
				showmessage('activity_notification_reason');
			}
			if($uidarray) {
				foreach($uidarray as $uid) {
					notification_add($uid, 'activity', 'activity_notification', ['tid' => $_G['tid'], 'subject' => $activity_subject, 'msg' => $reason]);
				}
				showmessage('activity_notification_success', "forum.php?mod=viewthread&tid={$_G['tid']}&do=viewapplylist".($_GET['from'] ? '&from='.$_GET['from'] : ''), [], ['showdialog' => 1, 'closetime' => true]);
			}
		} elseif($_GET['operation'] == 'delete') {
			if($uidarray) {
				table_forum_activityapply::t()->delete_for_thread($_G['tid'], $_GET['applyidarray']);
				foreach($uidarray as $uid) {
					notification_add($uid, 'activity', 'activity_delete', [
						'tid' => $_G['tid'],
						'subject' => $activity_subject,
						'reason' => $reason,
					]);
				}
			}
			$applynumber = table_forum_activityapply::t()->fetch_count_for_thread($_G['tid']);
			table_forum_activity::t()->update($_G['tid'], ['applynumber' => $applynumber]);
			showmessage('activity_delete_completion', "forum.php?mod=viewthread&tid={$_G['tid']}&do=viewapplylist".($_GET['from'] ? '&from='.$_GET['from'] : ''), [], ['showdialog' => 1, 'closetime' => true]);
		} else {
			if($unverified) {
				$verified = $_GET['operation'] == 'replenish' ? 2 : 1;

				table_forum_activityapply::t()->update_verified_for_thread($verified, $_G['tid'], $_GET['applyidarray']);
				$notification_lang = $verified == 1 ? 'activity_apply' : 'activity_replenish';
				foreach($unverified as $uid) {
					notification_add($uid, 'activity', $notification_lang, [
						'tid' => $_G['tid'],
						'subject' => $activity_subject,
						'reason' => $reason,
					]);
				}
			}
			$applynumber = table_forum_activityapply::t()->fetch_count_for_thread($_G['tid']);
			table_forum_activity::t()->update($_G['tid'], ['applynumber' => $applynumber]);

			showmessage('activity_auditing_completion', "forum.php?mod=viewthread&tid={$_G['tid']}&do=viewapplylist".($_GET['from'] ? '&from='.$_GET['from'] : ''), [], ['showdialog' => 1, 'closetime' => true]);
		}
	}
}
	