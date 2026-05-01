<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once libfile('function/forumlist');
$forumlist = '<SELECT name="addfid">'.forumselect(FALSE, 0, 0, TRUE).'</select>';

loadcache('forums');

if(!submitcheck('accesssubmit')) {

	shownav('user', 'members_access_edit');
	showchildmenu([['nav_members', 'members&operation=list'],
		[$member['username'].' ', 'members&operation=edit&uid='.$member['uid']]], cplang('members_access_edit'));

	/*search={"members_access_edit":"action=members&operation=access"}*/
	showtips('members_access_tips');
	showtableheader(cplang('members_access_now'), 'nobottom fixpadding');
	showsubtitle(['forum', 'members_access_view', 'members_access_post', 'members_access_reply', 'members_access_getattach', 'members_access_getimage', 'members_access_postattach', 'members_access_postimage', 'members_access_adminuser', 'members_access_dateline']);


	$accessmasks = table_forum_access::t()->fetch_all_by_uid($_GET['uid']);
	foreach($accessmasks as $id => $access) {
		$adminuser = C::t('common_member'.$tableext)->fetch($access['adminuser']);
		$access['dateline'] = $access['dateline'] ? dgmdate($access['dateline']) : '';
		$forum = $_G['cache']['forums'][$id];
		showtablerow('', '', [
			($forum['type'] == 'forum' ? '' : '|-----')."&nbsp;<a href=\"".ADMINSCRIPT."?action=forums&operation=edit&fid={$forum['fid']}&anchor=perm\">{$forum['name']}</a>",
			accessimg($access['allowview']),
			accessimg($access['allowpost']),
			accessimg($access['allowreply']),
			accessimg($access['allowgetattach']),
			accessimg($access['allowgetimage']),
			accessimg($access['allowpostattach']),
			accessimg($access['allowpostimage']),
			$adminuser['username'],
			$access['dateline'],
		]);
	}

	if(empty($accessmasks)) {
		showtablerow('', '', [
			'-',
			'-',
			'-',
			'-',
			'-',
			'-',
			'-',
			'-',
			'-',
			'-',
		]);
	}

	showtablefooter();
	showformheader("members&operation=access&uid={$_GET['uid']}");
	showtableheader(cplang('members_access_add'), 'notop fixpadding');
	showsetting('members_access_add_forum', '', '', $forumlist);
	foreach(['view', 'post', 'reply', 'getattach', 'getimage', 'postattach', 'postimage'] as $perm) {
		showsetting('members_access_add_'.$perm, ['allow'.$perm.'new', [
			[0, cplang('default')],
			[1, cplang('members_access_allowed')],
			[-1, cplang('members_access_disallowed')],
		], TRUE], 0, 'mradio');
	}
	showsubmit('accesssubmit', 'submit');
	showtablefooter();
	showformfooter();
	/*search*/

} else {

	$addfid = intval($_GET['addfid']);
	if($addfid && $_G['cache']['forums'][$addfid]) {
		$allowviewnew = !$_GET['allowviewnew'] ? 0 : ($_GET['allowviewnew'] > 0 ? 1 : -1);
		$allowpostnew = !$_GET['allowpostnew'] ? 0 : ($_GET['allowpostnew'] > 0 ? 1 : -1);
		$allowreplynew = !$_GET['allowreplynew'] ? 0 : ($_GET['allowreplynew'] > 0 ? 1 : -1);
		$allowgetattachnew = !$_GET['allowgetattachnew'] ? 0 : ($_GET['allowgetattachnew'] > 0 ? 1 : -1);
		$allowgetimagenew = !$_GET['allowgetimagenew'] ? 0 : ($_GET['allowgetimagenew'] > 0 ? 1 : -1);
		$allowpostattachnew = !$_GET['allowpostattachnew'] ? 0 : ($_GET['allowpostattachnew'] > 0 ? 1 : -1);
		$allowpostimagenew = !$_GET['allowpostimagenew'] ? 0 : ($_GET['allowpostimagenew'] > 0 ? 1 : -1);

		if($allowviewnew == -1) {
			$allowpostnew = $allowreplynew = $allowgetattachnew = $allowgetimagenew = $allowpostattachnew = $allowpostimagenew = -1;
		} elseif($allowpostnew == 1 || $allowreplynew == 1 || $allowgetattachnew == 1 || $allowgetimagenew == 1 || $allowpostattachnew == 1 || $allowpostimagenew == 1) {
			$allowviewnew = 1;
		}

		if(!$allowviewnew && !$allowpostnew && !$allowreplynew && !$allowgetattachnew && !$allowgetimagenew && !$allowpostattachnew && !$allowpostimagenew) {
			table_forum_access::t()->delete_by_fid($addfid, $_GET['uid']);
			if(!table_forum_access::t()->count_by_uid($_GET['uid'])) {
				C::t('common_member'.$tableext)->update($_GET['uid'], ['accessmasks' => 0]);
			}
		} else {
			$data = ['uid' => $_GET['uid'], 'fid' => $addfid, 'allowview' => $allowviewnew, 'allowpost' => $allowpostnew, 'allowreply' => $allowreplynew, 'allowgetattach' => $allowgetattachnew, 'allowgetimage' => $allowgetimagenew, 'allowpostattach' => $allowpostattachnew, 'allowpostimage' => $allowpostimagenew, 'adminuser' => $_G['uid'], 'dateline' => $_G['timestamp']];
			table_forum_access::t()->insert($data, 0, 1);
			C::t('common_member'.$tableext)->update($_GET['uid'], ['accessmasks' => 1]);
		}
		updatecache('forums');

	}
	cpmsg('members_access_succeed', 'action=members&operation=access&uid='.$_GET['uid'], 'succeed');

}
	