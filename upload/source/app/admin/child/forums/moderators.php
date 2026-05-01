<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!$fid) {
	cpmsg('undefined_action');
}

if(!submitcheck('modsubmit')) {

	$forum = table_forum_forum::t()->fetch($fid);
	shownav('forum', 'forums_moderators_edit');
	showchildmenu([['nav_forums', 'forums'], [$forum['name'].' ', '']], cplang('forums_moderators_edit'));
	showtips('forums_moderators_tips');
	showformheader("forums&operation=moderators&fid=$fid&");
	showtableheader('', 'fixpadding');
	showsubtitle(['', 'display_order', 'username', 'usergroups', 'forums_moderators_inherited']);

	$modgroups = table_common_admingroup::t()->fetch_all_merge_usergroup(array_keys(table_common_usergroup::t()->fetch_all_by_radminid(0)));
	$groupselect = '<select name="newgroup">';
	foreach($modgroups as $modgroup) {
		if($modgroup['radminid'] == 3) {
			$groupselect .= '<option value="'.$modgroup['admingid'].'">'.$modgroup['grouptitle'].'</option>';
		}
		$modgroups[$modgroup['admingid']] = $modgroup['grouptitle'];
	}
	$groupselect .= '</select>';

	$moderators = table_forum_moderator::t()->fetch_all_by_fid($fid);
	$uids = array_keys($moderators);
	if($uids) {
		$users = table_common_member::t()->fetch_all($uids);
	}

	foreach($moderators as $mod) {
		if(isset($modgroups[$users[$mod['uid']]['groupid']])) {
			$groupid = $modgroups[$users[$mod['uid']]['groupid']];
		} else {
			$extgrouparray = explode("\t", $users[$mod['uid']]['extgroupids']);
			foreach($extgrouparray as $extgroupid) {
				if(isset($modgroups[$extgroupid])) {
					$groupid = $modgroups[$extgroupid];
					break;
				}
			}
		}
		showtablerow('', ['class="td25"', 'class="td28"'], [
			'<input type="checkbox" class="checkbox" name="delete[]" value="'.$mod['uid'].'"'.($mod['inherited'] ? ' disabled' : '').' />',
			'<input type="text" class="txt" name="displayordernew['.$mod['uid'].']" value="'.$mod['displayorder'].'" size="2" />',
			"<a href=\"".ADMINSCRIPT."?mod=forum&action=members&operation=group&uid={$mod['uid']}\" target=\"_blank\">{$users[$mod['uid']]['username']}</a>",
			$groupid,
			cplang($mod['inherited'] ? 'yes' : 'no'),
		]);
	}

	if($forum['type'] == 'group' || $forum['type'] == 'sub') {
		$checked = $forum['type'] == 'group' ? 'checked' : '';
		$disabled = 'disabled';
	} else {
		$checked = $forum['inheritedmod'] ? 'checked' : '';
		$disabled = '';
	}

	showtablerow('', ['class="td25"', 'class="td28"'], [
		cplang('add_new'),
		'<input type="text" class="txt" name="newdisplayorder" value="0" size="2" />',
		'<input type="text" class="txt" name="newmoderator" value="" size="20" />',
		$groupselect.
		'<label><input type="checkbox" name="newextgroup" class="checkbox" value="1" />'.cplang('members_group_extended').'</label>',
		''
	]);

	showsubmit('modsubmit', 'submit', 'del', '<input class="checkbox" type="checkbox" name="inheritedmodnew" value="1" '.$checked.' '.$disabled.' id="inheritedmodnew" /><label for="inheritedmodnew">'.cplang('forums_moderators_inherit').'</label>');
	showtablefooter();
	showformfooter();

} else {
	$forum = table_forum_forum::t()->fetch($fid);
	$inheritedmodnew = $_GET['inheritedmodnew'];
	if($forum['type'] == 'group') {
		$inheritedmodnew = 1;
	} elseif($forum['type'] == 'sub') {
		$inheritedmodnew = 0;
	}

	if(!empty($_GET['delete']) || $_GET['newmoderator'] || (bool)$forum['inheritedmod'] != (bool)$inheritedmodnew) {

		$fidarray = $newmodarray = $origmodarray = [];

		if($forum['type'] == 'group') {
			$query = table_forum_forum::t()->fetch_all_fids(1, 'forum', $fid);
			foreach($query as $sub) {
				$fidarray[] = $sub['fid'];
			}
			$query = table_forum_forum::t()->fetch_all_fids(1, 'sub', $fidarray);
			foreach($query as $sub) {
				$fidarray[] = $sub['fid'];
			}
		} elseif($forum['type'] == 'forum') {
			$query = table_forum_forum::t()->fetch_all_fids(1, 'sub', $fid);
			foreach($query as $sub) {
				$fidarray[] = $sub['fid'];
			}
		}

		if(is_array($_GET['delete'])) {
			foreach($_GET['delete'] as $uid) {
				table_forum_moderator::t()->delete_by_uid_fid_inherited($uid, $fid, $fidarray);
			}

			$excludeuids = [];
			$deleteuids = '\''.implode('\',\'', $_GET['delete']).'\'';
			foreach(table_forum_moderator::t()->fetch_all_by_uid($_GET['delete']) as $mod) {
				$excludeuids[] = $mod['uid'];
			}

			$usergroups = [];
			$query = table_common_usergroup::t()->range();
			foreach($query as $group) {
				$usergroups[$group['groupid']] = $group;
			}

			$members = table_common_member::t()->fetch_all($_GET['delete'], false, 0);
			foreach($members as $uid => $member) {
				if(!in_array($uid, $excludeuids) && !in_array($member['adminid'], [1, 2])) {
					if($usergroups[$member['groupid']]['type'] == 'special' && $usergroups[$member['groupid']]['radminid'] != 3) {
						$adminidnew = -1;
						$groupidnew = $member['groupid'];
					} else {
						$adminidnew = 0;
						foreach($usergroups as $group) {
							if($group['type'] == 'member' && $member['credits'] >= $group['creditshigher'] && $member['credits'] < $group['creditslower']) {
								$groupidnew = $group['groupid'];
								break;
							}
						}
					}
					table_common_member::t()->update($member['uid'], ['adminid' => $adminidnew, 'groupid' => $groupidnew]);
				}
			}
		}

		if($_GET['newmoderator']) {
			$member = table_common_member::t()->fetch_by_username($_GET['newmoderator']);
			if(!$member) {
				$member = table_common_member::t()->fetch_by_loginname($_GET['newmoderator']);
			}
			if(!$member) {
				cpmsg_error('members_edit_nonexistence');
			} else {
				$newmodarray[] = $member['uid'];
				$membersetarr = [];
				if(empty($_GET['newextgroup'])) {
					if(!in_array($member['adminid'], [1, 2, 3, 4, 5, 6, 7, 8, -1])) {
						$membersetarr['groupid'] = $_GET['newgroup'];
					}
				} else {
					$extgrouparray = explode("\t", $member['extgroupids']);
					if(!in_array($_GET['newgroup'], $extgrouparray)) {
						$membersetarr['extgroupids'] = implode("\t", array_merge($extgrouparray, [$_GET['newgroup']]));
					}
				}
				if(!in_array($member['adminid'], [1, 2])) {
					$membersetarr['adminid'] = '3';
				}
				if(!empty($membersetarr)) {
					table_common_member::t()->update($member['uid'], $membersetarr);
				}

				table_forum_moderator::t()->insert([
					'uid' => $member['uid'],
					'fid' => $fid,
					'displayorder' => $_GET['newdisplayorder'],
					'inherited' => '0',
				], false, true);
			}
		}

		if((bool)$forum['inheritedmod'] != (bool)$inheritedmodnew) {
			foreach(table_forum_moderator::t()->fetch_all_by_fid_inherited($fid) as $mod) {
				$origmodarray[] = $mod['uid'];
				if(!$forum['inheritedmod'] && $inheritedmodnew) {
					$newmodarray[] = $mod['uid'];
				}
			}
			if($forum['inheritedmod'] && !$inheritedmodnew) {
				table_forum_moderator::t()->delete_by_uid_fid($origmodarray, $fidarray);
			}
		}

		foreach($newmodarray as $uid) {
			table_forum_moderator::t()->insert([
				'uid' => $uid,
				'fid' => $fid,
				'displayorder' => $_GET['newdisplayorder'],
				'inherited' => '0',
			], false, true);

			if($inheritedmodnew) {
				foreach($fidarray as $ifid) {
					table_forum_moderator::t()->insert([
						'uid' => $uid,
						'fid' => $ifid,
						'inherited' => '1',
					], false, true);
				}
			}
		}

		if($forum['type'] == 'group') {
			$inheritedmodnew = 1;
		} elseif($forum['type'] == 'sub') {
			$inheritedmodnew = 0;
		}
		table_forum_forum::t()->update($fid, ['inheritedmod' => $inheritedmodnew]);
	}

	if(is_array($_GET['displayordernew'])) {
		foreach($_GET['displayordernew'] as $uid => $order) {
			table_forum_moderator::t()->update_by_fid_uid($fid, $uid, [
				'displayorder' => $order,
			]);
		}
	}

	$fidarray[] = $fid;
	foreach($fidarray as $fid) {
		$moderators = $tab = '';
		$modorder = [];
		$modmemberarray = table_forum_moderator::t()->fetch_all_no_inherited_by_fid($fid);
		foreach($modmemberarray as $moduid => $modmember) {
			$modorder[] = $moduid;
		}
		$members = table_common_member::t()->fetch_all_username_by_uid($modorder);
		foreach($modorder as $mod) {
			if(!$members[$mod]) {
				continue;
			}
			$moderators .= $tab.addslashes($members[$mod]);
			$tab = "\t";
		}

		table_forum_forumfield::t()->update($fid, ['moderators' => $moderators]);
	}
	cpmsg('forums_moderators_update_succeed', "mod=forum&action=forums&operation=moderators&fid=$fid", 'succeed');

}
	