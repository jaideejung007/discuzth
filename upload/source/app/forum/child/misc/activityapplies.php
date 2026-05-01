<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['uid']) {
	showmessage('not_loggedin', NULL, [], ['login' => 1]);
}

if(submitcheck('activitysubmit')) {
	$activity = table_forum_activity::t()->fetch($_G['tid']);
	if($activity['expiration'] && $activity['expiration'] < TIMESTAMP) {
		showmessage('activity_stop', NULL, [], ['login' => 1]);
	}
	$applyinfo = [];
	$applyinfo = table_forum_activityapply::t()->fetch_info_for_user($_G['uid'], $_G['tid']);
	if($applyinfo && $applyinfo['verified'] < 2) {
		showmessage('activity_repeat_apply', NULL, [], ['login' => 1]);
	}
	$payvalue = intval($_GET['payvalue']);
	$payment = $_GET['payment'] ? $payvalue : -1;
	$message = cutstr(dhtmlspecialchars($_GET['message']), 200);
	$verified = $thread['authorid'] == $_G['uid'] ? 1 : 0;
	if($activity['ufield']) {
		$ufielddata = [];
		$activity['ufield'] = dunserialize($activity['ufield']);
		if(!empty($activity['ufield']['userfield'])) {
			$censor = discuz_censor::instance();
			loadcache('profilesetting');
			foreach($activity['ufield']['userfield'] as $filedname) {
				$value = $_POST[$filedname];
				if(is_array($value)) {
					$value = implode(',', $value);
				}
				$value = cutstr(dhtmlspecialchars(trim($value)), 100, '.');
				if($_G['cache']['profilesetting'][$filedname]['formtype'] == 'file' && !preg_match('/^https?:\/\/(.*)?\.(jpg|png|gif|jpeg|bmp)$/i', $value)) {
					showmessage('activity_imgurl_error');
				}
				if(empty($value) && $filedname != 'residedist' && $filedname != 'residecommunity') {
					showmessage('activity_exile_field');
				}
				$ufielddata['userfield'][$filedname] = $value;
			}
		}
		if(!empty($activity['ufield']['extfield'])) {
			foreach($activity['ufield']['extfield'] as $fieldid) {
				$value = cutstr(dhtmlspecialchars(trim($_GET[''.$fieldid])), 50, '.');
				$ufielddata['extfield'][$fieldid] = $value;
			}
		}
		$ufielddata = !empty($ufielddata) ? serialize($ufielddata) : '';
	}
	if($_G['setting']['activitycredit'] && $activity['credit'] && empty($applyinfo['verified'])) {
		checklowerlimit(['extcredits'.$_G['setting']['activitycredit'] => '-'.$activity['credit']]);
		updatemembercount($_G['uid'], [$_G['setting']['activitycredit'] => '-'.$activity['credit']], true, 'ACC', $_G['tid']);
	}
	if($applyinfo && $applyinfo['verified'] == 2) {
		$newinfo = [
			'tid' => $_G['tid'],
			'username' => $_G['username'],
			'uid' => $_G['uid'],
			'message' => $message,
			'verified' => $verified,
			'dateline' => $_G['timestamp'],
			'payment' => $payment,
			'ufielddata' => $ufielddata
		];
		table_forum_activityapply::t()->update($applyinfo['applyid'], $newinfo);
	} else {
		$data = ['tid' => $_G['tid'], 'username' => $_G['username'], 'uid' => $_G['uid'], 'message' => $message, 'verified' => $verified, 'dateline' => $_G['timestamp'], 'payment' => $payment, 'ufielddata' => $ufielddata];
		table_forum_activityapply::t()->insert($data);
	}

	$applynumber = table_forum_activityapply::t()->fetch_count_for_thread($_G['tid']);
	table_forum_activity::t()->update($_G['tid'], ['applynumber' => $applynumber]);

	if($thread['authorid'] != $_G['uid']) {
		notification_add($thread['authorid'], 'activity', 'activity_notice', [
			'tid' => $_G['tid'],
			'subject' => $thread['subject'],
		]);
		$space = [];
		space_merge($space, 'field_home');

		if(!empty($space['privacy']['feed']['newreply'])) {
			$feed['icon'] = 'activity';
			$feed['title_template'] = 'feed_reply_activity_title';
			$feed['title_data'] = [
				'subject' => "<a href=\"forum.php?mod=viewthread&tid={$_G['tid']}\">{$thread['subject']}</a>",
				'hash_data' => "tid{$_G['tid']}"
			];
			$feed['id'] = $_G['tid'];
			$feed['idtype'] = 'tid';
			postfeed($feed);
		}
	}
	showmessage('activity_completion', "forum.php?mod=viewthread&tid={$_G['tid']}".($_GET['from'] ? '&from='.$_GET['from'] : ''), [], ['showdialog' => 1, 'showmsg' => true, 'locationtime' => true, 'alert' => 'right']);

} elseif(submitcheck('activitycancel')) {
	table_forum_activityapply::t()->delete_for_user($_G['uid'], $_G['tid']);
	$applynumber = table_forum_activityapply::t()->fetch_count_for_thread($_G['tid']);
	table_forum_activity::t()->update($_G['tid'], ['applynumber' => $applynumber]);
	$message = cutstr(dhtmlspecialchars($_GET['message']), 200);
	if($thread['authorid'] != $_G['uid']) {
		notification_add($thread['authorid'], 'activity', 'activity_cancel', [
			'tid' => $_G['tid'],
			'subject' => $thread['subject'],
			'reason' => $message
		]);
	}
	showmessage('activity_cancel_success', "forum.php?mod=viewthread&tid={$_G['tid']}&do=viewapplylist".($_GET['from'] ? '&from='.$_GET['from'] : ''), [], ['showdialog' => 1, 'closetime' => true]);
}
	