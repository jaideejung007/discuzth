<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$membermf = C::t('common_member_field_forum'.$tableext)->fetch($_GET['uid']);
$membergroup = table_common_usergroup::t()->fetch($member['groupid']);
$member = array_merge($member, (array)$membermf, $membergroup);

if(!submitcheck('editsubmit')) {

	$checkadminid = [($member['adminid'] >= 0 ? $member['adminid'] : 0) => 'checked'];

	$member['groupterms'] = dunserialize($member['groupterms']);

	if($member['groupterms']['main']) {
		$expirydate = dgmdate($member['groupterms']['main']['time'], 'Y-n-j');
		$expirydays = ceil(($member['groupterms']['main']['time'] - TIMESTAMP) / 86400);
		$selecteaid = [$member['groupterms']['main']['adminid'] => 'selected'];
		$selectegid = [$member['groupterms']['main']['groupid'] => 'selected'];
	} else {
		$expirydate = $expirydays = '';
		$selecteaid = [$member['adminid'] => 'selected'];
		$selectegid = [($member['type'] == 'member' ? 0 : $member['groupid']) => 'selected'];
	}

	$extgroups = $expgroups = '';
	$radmingids = 0;
	$extgrouparray = explode("\t", $member['extgroupids']);
	$groups = ['system' => '', 'special' => '', 'member' => ''];
	$group = ['groupid' => 0, 'radminid' => 0, 'type' => '', 'grouptitle' => $lang['usergroups_system_0'], 'creditshigher' => 0, 'creditslower' => '0'];
	$query = array_merge([$group], (array)table_common_usergroup::t()->fetch_all_not([6, 7]));
	foreach($query as $group) {
		if($group['groupid'] && !in_array($group['groupid'], [4, 5, 6, 7, 8]) && ($group['type'] == 'system' || $group['type'] == 'special')) {
			$extgroups .= showtablerow('', ['class="td27"', 'style="width:70%"'], [
				'<input class="checkbox" type="checkbox" name="extgroupidsnew[]" value="'.$group['groupid'].'" '.(in_array($group['groupid'], $extgrouparray) ? 'checked' : '').' id="extgid_'.$group['groupid'].'" /><label for="extgid_'.$group['groupid'].'"> '.$group['grouptitle'].'</label>',
				'<input type="text" class="txt" size="9" name="extgroupexpirynew['.$group['groupid'].']" value="'.(in_array($group['groupid'], $extgrouparray) && !empty($member['groupterms']['ext'][$group['groupid']]) ? dgmdate($member['groupterms']['ext'][$group['groupid']], 'Y-n-j') : '').'" onclick="showcalendar(event, this)" />'
			], TRUE);
		}
		if($group['groupid'] && $group['type'] == 'member' && !($member['credits'] >= $group['creditshigher'] && $member['credits'] < $group['creditslower']) && $member['groupid'] != $group['groupid']) {
			continue;
		}

		$expgroups .= '<option name="expgroupidnew" value="'.$group['groupid'].'" '.$selectegid[$group['groupid']].'>'.$group['grouptitle'].'</option>';

		if($group['groupid'] != 0) {
			$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
			$groups[$group['type']] .= '<option value="'.$group['groupid'].'"'.($member['groupid'] == $group['groupid'] ? 'selected="selected"' : '').' gtype="'.$group['type'].'">'.$group['grouptitle'].'</option>';
			if($group['type'] == 'special' && !$group['radminid']) {
				$radmingids .= ','.$group['groupid'];
			}
		}

	}

	if(!$groups['member']) {
		$group = table_common_usergroup::t()->fetch_new_groupid(true);
		$groups['member'] = '<option value="'.$group['groupid'].'" gtype="member">'.$group['grouptitle'].'</option>';
	}

	/*search={"members_group":"action=members&operation=group"}*/
	shownav('user', 'members_group');
	showchildmenu([['nav_members', 'members&operation=list'],
		[$member['username'].' ', 'members&operation=edit&uid='.$member['uid']]], cplang('members_group'));

	echo '<script src="'.STATICURL.'js/calendar.js" type="text/javascript"></script>';
	showformheader("members&operation=group&uid={$member['uid']}");
	showtableheader('usergroup', 'nobottom');
	showsetting('members_group_group', '', '', '<select name="groupidnew" onchange="if(in_array(this.value, ['.$radmingids.'])) {$(\'relatedadminid\').style.display = \'\';$(\'adminidnew\').name=\'adminidnew[\' + this.value + \']\';} else {$(\'relatedadminid\').style.display = \'none\';$(\'adminidnew\').name=\'adminidnew[0]\';}"><optgroup label="'.$lang['usergroups_system'].'">'.$groups['system'].'<optgroup label="'.$lang['usergroups_special'].'">'.$groups['special'].'<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groups['specialadmin'].'<optgroup label="'.$lang['usergroups_member'].'">'.$groups['member'].'</select>');
	showtagheader('tbody', 'relatedadminid', $member['type'] == 'special' && !$member['radminid'], 'sub');
	showsetting('members_group_related_adminid', '', '', '<select id="adminidnew" name="adminidnew['.$member['groupid'].']"><option value="0"'.($member['adminid'] == 0 ? ' selected' : '').'>'.$lang['none'].'</option><option value="3"'.($member['adminid'] == 3 ? ' selected' : '').'>'.$lang['usergroups_system_3'].'</option><option value="2"'.($member['adminid'] == 2 ? ' selected' : '').'>'.$lang['usergroups_system_2'].'</option><option value="1"'.($member['adminid'] == 1 ? ' selected' : '').'>'.$lang['usergroups_system_1'].'</option></select>');
	showtagfooter('tbody');
	showsetting('members_group_validity', 'expirydatenew', $expirydate, 'calendar');
	showsetting('members_group_orig_adminid', '', '', '<select name="expgroupidnew">'.$expgroups.'</select>');
	showsetting('members_group_orig_groupid', '', '', '<select name="expadminidnew"><option value="0" '.$selecteaid[0].'>'.$lang['usergroups_system_0'].'</option><option value="1" '.$selecteaid[1].'>'.$lang['usergroups_system_1'].'</option><option value="2" '.$selecteaid[2].'>'.$lang['usergroups_system_2'].'</option><option value="3" '.$selecteaid[3].'>'.$lang['usergroups_system_3'].'</option></select>');
	showtablefooter();

	showtableheader('members_group_extended', 'noborder fixpadding');
	showsubtitle(['usergroup', 'validity']);
	echo $extgroups;
	showtablerow('', 'colspan="2"', cplang('members_group_extended_comment'));
	showtablefooter();

	showtableheader('members_edit_reason', 'notop');
	showsetting('members_group_reason', 'reason', '', 'textarea');
	showsetting('members_group_reason_notify', 'reasonnotify', '', 'radio');
	showsubmit('editsubmit');
	showtablefooter();

	showformfooter();
	/*search*/

} else {

	$group = table_common_usergroup::t()->fetch($_GET['groupidnew']);
	if(!$group) {
		cpmsg('undefined_action', '', 'error');
	}

	if(strlen(is_array($_GET['extgroupidsnew']) ? implode("\t", $_GET['extgroupidsnew']) : '') > 30) {
		cpmsg('members_edit_groups_toomany', '', 'error');
	}

	if($member['groupid'] != $_GET['groupidnew'] && isfounder($member)) {
		cpmsg('members_edit_groups_isfounder', '', 'error');
	}

	$_GET['adminidnew'] = $_GET['adminidnew'][$_GET['groupidnew']];
	switch($group['type']) {
		case 'member':
			$_GET['groupidnew'] = in_array($_GET['adminidnew'], [1, 2, 3]) ? $_GET['adminidnew'] : $_GET['groupidnew'];
			break;
		case 'special':
			if($group['radminid']) {
				$_GET['adminidnew'] = $group['radminid'];
			} elseif(!in_array($_GET['adminidnew'], [1, 2, 3])) {
				$_GET['adminidnew'] = -1;
			}
			break;
		case 'system':
			$_GET['adminidnew'] = in_array($_GET['groupidnew'], [1, 2, 3]) ? $_GET['groupidnew'] : -1;
			break;
	}

	$groupterms = [];

	if($_GET['expirydatenew']) {

		$maingroupexpirynew = strtotime($_GET['expirydatenew']);

		$group = table_common_usergroup::t()->fetch($_GET['expgroupidnew']);
		if(!$group) {
			$_GET['expgroupidnew'] = in_array($_GET['expadminidnew'], [1, 2, 3]) ? $_GET['expadminidnew'] : $_GET['expgroupidnew'];
		} else {
			switch($group['type']) {
				case 'special':
					if($group['radminid']) {
						$_GET['expadminidnew'] = $group['radminid'];
					} elseif(!in_array($_GET['expadminidnew'], [1, 2, 3])) {
						$_GET['expadminidnew'] = -1;
					}
					break;
				case 'system':
					$_GET['expadminidnew'] = in_array($_GET['expgroupidnew'], [1, 2, 3]) ? $_GET['expgroupidnew'] : -1;
					break;
			}
		}

		if($_GET['expgroupidnew'] == $_GET['groupidnew']) {
			cpmsg('members_edit_groups_illegal', '', 'error');
		} elseif($maingroupexpirynew > TIMESTAMP) {
			if($_GET['expgroupidnew'] || $_GET['expadminidnew']) {
				$groupterms['main'] = ['time' => $maingroupexpirynew, 'adminid' => $_GET['expadminidnew'], 'groupid' => $_GET['expgroupidnew']];
			} else {
				$groupterms['main'] = ['time' => $maingroupexpirynew];
			}
			$groupterms['ext'][$_GET['groupidnew']] = $maingroupexpirynew;
		}

	}

	if(is_array($_GET['extgroupexpirynew'])) {
		foreach($_GET['extgroupexpirynew'] as $extgroupid => $expiry) {
			if(is_array($_GET['extgroupidsnew']) && in_array($extgroupid, $_GET['extgroupidsnew']) && !isset($groupterms['ext'][$extgroupid]) && $expiry && ($expiry = strtotime($expiry)) > TIMESTAMP) {
				$groupterms['ext'][$extgroupid] = $expiry;
			}
		}
	}

	$exarr = is_array($_GET['extgroupidsnew']) ? $_GET['extgroupidsnew'] : [];
	$exinfo = '';

	foreach($exarr as $extgroupid) {
		$extfetch = table_common_usergroup::t()->fetch($extgroupid);
		$extgroupinfo .= (empty($extgroupinfo) ? '' : ', ').$extfetch['grouptitle'].' => '.(empty($groupterms['ext'][$extgroupid]) ? 0 : dgmdate($groupterms['ext'][$extgroupid], 'Y-m-d H:i:s'));
	}

	$grouptermsnew = serialize($groupterms);
	$groupexpirynew = groupexpiry($groupterms);
	$extgroupidsnew = $_GET['extgroupidsnew'] && is_array($_GET['extgroupidsnew']) ? implode("\t", $_GET['extgroupidsnew']) : '';

	C::t('common_member'.$tableext)->update($member['uid'], ['groupid' => $_GET['groupidnew'], 'adminid' => $_GET['adminidnew'], 'extgroupids' => $extgroupidsnew, 'groupexpiry' => $groupexpirynew]);
	if(C::t('common_member_field_forum'.$tableext)->fetch($member['uid'])) {
		C::t('common_member_field_forum'.$tableext)->update($member['uid'], ['groupterms' => $grouptermsnew]);
	} else {
		C::t('common_member_field_forum'.$tableext)->insert(['uid' => $member['uid'], 'groupterms' => $grouptermsnew]);
	}

	if($_GET['groupidnew'] != $member['groupid'] && (in_array($_GET['groupidnew'], [4, 5]) || in_array($member['groupid'], [4, 5]))) {
		$my_opt = in_array($_GET['groupidnew'], [4, 5]) ? 'banuser' : 'unbanuser';
		banlog($member['username'], $member['groupid'], $_GET['groupidnew'], $groupexpirynew, $_GET['reason']);
	}

	if(isset($_GET['reason']) && isset($_GET['reasonnotify']) && !empty($_GET['reason']) && $_GET['reasonnotify']) {
		$mainfetch = table_common_usergroup::t()->fetch($_GET['groupidnew']);
		$notearr = [
			'user' => "<a href=\"home.php?mod=space&uid={$_G['uid']}\">{$_G['username']}</a>",
			'day' => !empty($_GET['expirydatenew']) ? addslashes($_GET['expirydatenew']) : 0,
			'groupname' => $mainfetch['grouptitle'],
			'extgroupinfo' => empty($exinfo) ? cplang('members_group_extended_none') : $exinfo,
			'reason' => addslashes($_GET['reason']),
			'from_id' => 0,
			'from_idtype' => 'changeusergroup'
		];
		notification_add($member['uid'], 'system', 'member_change_usergroup', $notearr, 1);
	}

	cpmsg('members_edit_groups_succeed', "action=members&operation=group&uid={$member['uid']}", 'succeed');

}
	