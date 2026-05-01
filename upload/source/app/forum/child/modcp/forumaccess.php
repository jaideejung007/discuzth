<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_MODCP')) {
	exit('Access Denied');
}

$list = $logids = [];

include_once(libfile('function/forumlist'));
$forumlistall = forumselect(false, false, $_G['fid']);

$adderror = $successed = 0;
$new_user = isset($_GET['new_user']) ? trim($_GET['new_user']) : '';

if($_G['fid'] && $_G['forum']['ismoderator'] && $new_user != '' && submitcheck('addsubmit')) {
	$deleteaccess = isset($_GET['deleteaccess']) ? 1 : 0;
	foreach(['view', 'post', 'reply', 'getattach', 'getimage', 'postattach', 'postimage'] as $key) {
		${'new_'.$key} = isset($_GET['new_'.$key]) ? intval($_GET['new_'.$key]) : '';
	}

	if($new_user != '') {

		$user = table_common_member::t()->fetch_by_username($new_user);
		$uid = $user['uid'];

		if(empty($user)) {
			$adderror = 1;
		} elseif($user['adminid'] && $_G['adminid'] != 1) {
			$adderror = 2;
		} else {

			$access = table_forum_access::t()->fetch_all_by_fid_uid($_G['fid'], $uid);
			$access = $access[0];

			if($deleteaccess) {

				if($access && $_G['adminid'] != 1 && inwhitelist($access)) {
					$adderror = 3;
				} else {
					$successed = true;
					$access && delete_access($uid, $_G['fid']);
				}

			} elseif($new_view || $new_post || $new_reply || $new_getattach || $new_getimage || $new_postattach || $new_postimage) {

				if($new_view == -1) {
					$new_view = $new_post = $new_reply = $new_getattach = $new_getimage = $new_postattach = $new_postimage = -1;
				} else {
					$new_view = 0;
					$new_post = $new_post ? -1 : 0;
					$new_reply = $new_reply ? -1 : 0;
					$new_getattach = $new_getattach ? -1 : 0;
					$new_getimage = $new_getimage ? -1 : 0;
					$new_postattach = $new_postattach ? -1 : 0;
					$new_postimage = $new_postimage ? -1 : 0;
				}

				if(empty($access)) {
					$successed = true;
					$data = ['uid' => $uid, 'fid' => $_G['fid'], 'allowview' => $new_view, 'allowpost' => $new_post, 'allowreply' => $new_reply,
						'allowgetattach' => $new_getattach, 'allowgetimage' => $new_getimage,
						'allowpostattach' => $new_postattach, 'allowpostimage' => $new_postimage,
						'adminuser' => $_G['uid'], 'dateline' => $_G['timestamp']];
					table_forum_access::t()->insert($data);
					table_common_member::t()->update($uid, ['accessmasks' => 1], 'UNBUFFERED');

				} elseif($new_view == -1 && $access['allowview'] == 1 && $_G['adminid'] != 1) {
					$adderror = 3;
				} else {
					if($_G['adminid'] > 1) {
						$new_view = $access['allowview'] == 1 ? 1 : $new_view;
						$new_post = $access['allowpost'] == 1 ? 1 : $new_post;
						$new_reply = $access['allowreply'] == 1 ? 1 : $new_reply;
						$new_getattach = $access['allowgetattach'] == 1 ? 1 : $new_getattach;
						$new_getimage = $access['allowgetimage'] == 1 ? 1 : $new_getimage;
						$new_postattach = $access['postattach'] == 1 ? 1 : $new_postattach;
						$new_postimage = $access['postimage'] == 1 ? 1 : $new_postimage;
					}
					$successed = true;
					$data = ['allowview' => $new_view, 'allowpost' => $new_post, 'allowreply' => $new_reply,
						'allowgetattach' => $new_getattach, 'allowgetimage' => $new_getimage,
						'allowpostattach' => $new_postattach, 'allowpostimage' => $new_postimage,
						'adminuser' => $_G['uid'], 'dateline' => $_G['timestamp']];
					table_forum_access::t()->update_for_uid($uid, $_G['fid'], $data);
					table_common_member::t()->update($uid, ['accessmasks' => 1], 'UNBUFFERED');

				}
			}
		}
	}

	$new_user = $adderror ? $new_user : '';
}

$new_user = dhtmlspecialchars($new_user);
$suser = isset($_GET['suser']) ? trim($_GET['suser']) : '';
if(submitcheck('searchsubmit')) {
	if($suser != '') {
		$suid = table_common_member::t()->fetch_uid_by_username($suser);
	}
}
$suser = dhtmlspecialchars($suser);

$page = max(1, intval($_G['page']));
$ppp = 10;
$list = ['pagelink' => '', 'data' => []];

if($num = table_forum_access::t()->fetch_all_by_fid_uid($_G['fid'], $suid, 1)) {

	$page = $page > ceil($num / $ppp) ? ceil($num / $ppp) : $page;
	$start_limit = ($page - 1) * $ppp;
	$list['pagelink'] = multi($num, $ppp, $page, "forum.php?mod=modcp&fid={$_G['fid']}&action={$_GET['action']}");

	$query = table_forum_access::t()->fetch_all_by_fid_uid($_G['fid'], $suid, 0, $start_limit, $ppp);
	$uidarray = [];
	foreach($query as $access) {
		$uidarray[$access['uid']] = $access['uid'];
		$uidarray[$access['adminuser']] = $access['adminuser'];
		$access['allowview'] = accessimg($access['allowview']);
		$access['allowpost'] = accessimg($access['allowpost']);
		$access['allowreply'] = accessimg($access['allowreply']);
		$access['allowpostattach'] = accessimg($access['allowpostattach']);
		$access['allowgetattach'] = accessimg($access['allowgetattach']);
		$access['allowgetimage'] = accessimg($access['allowgetimage']);
		$access['allowpostimage'] = accessimg($access['allowpostimage']);
		$access['dateline'] = dgmdate($access['dateline'], 'd');
		$access['forum'] = '<a href="forum.php?mod=forumdisplay&fid='.$access['fid'].'" target="_blank">'.strip_tags($_G['cache']['forums'][$access['fid']]['name']).'</a>';
		$list['data'][] = $access;
	}

	$users = [];
	if($uids = dimplode($uidarray)) {
		$users = table_common_member::t()->fetch_all_username_by_uid($uidarray);
	}
}

function delete_access($uid, $fid) {
	table_forum_access::t()->delete_by_fid($fid, $uid);
	$mask = table_forum_access::t()->count_by_uid($uid);
	if(!$mask) {
		table_common_member::t()->update($uid, ['accessmasks' => ''], 'UNBUFFERED');
	}
}

function accessimg($access) {
	return $access == -1 ? '<i class="fico-remove_circle fc-i"></i>' :
		($access == 1 ? '<i class="fico-check_right fc-v"></i>' : '<i class="fico-stars fc-p"></i>');
}

function inwhitelist($access) {
	$return = false;
	foreach(['allowview', 'allowpost', 'allowreply', 'allowpostattach', 'allowgetattach', 'allowgetimage'] as $key) {
		if($access[$key] == 1) {
			$return = true;
			break;
		}
	}
	return $return;
}

