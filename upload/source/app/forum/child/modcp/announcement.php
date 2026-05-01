<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_MODCP')) {
	exit('Access Denied');
}

$annlist = null;
$add_successed = $edit_successed = false;
$op = empty($_GET['op']) ? 'add' : $_GET['op'];

$announce = ['subject' => '', 'message' => '', 'starttime' => '', 'endtime' => ''];
$announce['checked'] = ['selected="selected"', ''];

switch($op) {

	case 'add':

		$announce['starttime'] = dgmdate(TIMESTAMP, 'd');
		$announce['endtime'] = dgmdate(TIMESTAMP + 86400 * 30, 'd');
		if(submitcheck('submit')) {
			$message = is_array($_GET['message']) ? $_GET['message'][$_GET['type']] : '';
			save_announce(0, $_GET['starttime'], $_GET['endtime'], $_GET['subject'], $_GET['type'], $message, 0);
			$add_successed = true;
		}
		break;

	case 'manage':

		$annlist = get_annlist();

		if(submitcheck('submit')) {
			$delids = [];
			if(!empty($_GET['delete']) && is_array($_GET['delete'])) {
				foreach($_GET['delete'] as $id) {
					$id = intval($id);
					if(isset($annlist[$id])) {
						unset($annlist[$id]);
						$delids[] = $id;
					}
				}
				if($delids) {
					table_forum_announcement::t()->delete_by_id_username($delids, $_G['username']);
				}
			}

			$updateorder = false;
			if(!empty($_GET['order']) && is_array($_GET['order'])) {
				foreach($_GET['order'] as $id => $val) {
					$val = intval($val);
					if(isset($annlist[$id]) && $annlist[$id]['displayorder'] != $val) {
						$annlist[$id]['displayorder'] = $val;
						table_forum_announcement::t()->update_displayorder_by_id_username($id, $val, $_G['username']);
						$updateorder = true;
					}
				}
			}

			if($delids || $updateorder) {
				update_announcecache();
			}
		}

		break;

	case 'edit':
		$id = intval($_GET['id']);
		$announce = table_forum_announcement::t()->fetch_by_id_username($id, $_G['username']);
		if(!count($announce)) {
			showmessage('modcp_ann_nofound');
		}

		if(!submitcheck('submit')) {
			$announce['starttime'] = $announce['starttime'] ? dgmdate($announce['starttime'], 'd') : '';
			$announce['endtime'] = $announce['endtime'] ? dgmdate($announce['endtime'], 'd') : '';
			$announce['subject'] = strip_tags($announce['subject']);
			$announce['message'] = $announce['type'] != 1 ? dhtmlspecialchars($announce['message']) : $announce['message'];
			$announce['checked'] = $announce['type'] != 1 ? ['selected="selected"', ''] : ['', 'selected="selected"'];
		} else {
			$announce['starttime'] = $_GET['starttime'];
			$announce['endtime'] = $_GET['endtime'];
			$announce['checked'] = $_GET['type'] != 1 ? ['selected="selected"', ''] : ['', 'selected="selected"'];
			$message = $_GET['message'][$_GET['type']];
			save_announce($id, $_GET['starttime'], $_GET['endtime'], $_GET['subject'], $_GET['type'], $message, $_GET['displayorder']);
			$announce['subject'] = dhtmlspecialchars(trim($_GET['subject']));
			if(intval($_GET['type']) == 1) {
				list($message) = explode("\n", trim($message));
				$announce['message'] = dhtmlspecialchars($message);
			} else {
				$announce['message'] = dhtmlspecialchars(trim($message));
			}
			$edit_successed = true;
		}

		break;

}

$annlist = get_annlist();

function get_annlist() {
	global $_G;
	$annlist = table_forum_announcement::t()->fetch_all_by_displayorder();
	foreach($annlist as $announce) {
		$announce['disabled'] = $announce['author'] != $_G['member']['username'] ? 'disabled' : '';
		$announce['starttime'] = $announce['starttime'] ? dgmdate($announce['starttime'], 'd') : '-';
		$announce['endtime'] = $announce['endtime'] ? dgmdate($announce['endtime'], 'd') : '-';
		$annlist[$announce['id']] = $announce;
	}
	return $annlist;
}

function update_announcecache() {
	require_once libfile('function/cache');
	updatecache(['announcements', 'announcements_forum']);
}

function save_announce($id, $starttime, $endtime, $subject, $type, $message, $displayorder = 0) {
	global $_G;

	$displayorder = intval($displayorder);
	$type = intval($type);

	$starttime = empty($starttime) || strtotime($starttime) < TIMESTAMP ? TIMESTAMP : strtotime($starttime);
	$endtime = empty($endtime) ? 0 : (strtotime($endtime) < $starttime ? ($starttime + 86400 * 30) : strtotime($endtime));

	$subject = dhtmlspecialchars(trim($subject));

	if($type == 1) {
		list($message) = explode("\n", trim($message));
		$message = dhtmlspecialchars($message);
	} else {
		$type = 0;
		$message = trim($message);
	}

	if(empty($subject) || empty($message)) {
		acpmsg('modcp_ann_empty');
	} elseif($type == 1 && !preg_match('/^https?:\/\//is', $message)) {
		acpmsg('modcp_ann_urlerror');
	} else {
		$data = ['author' => $_G['username'], 'subject' => $subject, 'type' => $type, 'starttime' => $starttime, 'endtime' => $endtime,
			'message' => $message, 'displayorder' => $displayorder];

		if(empty($id)) {
			table_forum_announcement::t()->insert($data);
		} else {
			table_forum_announcement::t()->update($id, $data, true);
		}
		update_announcecache();
		return true;
	}
}

