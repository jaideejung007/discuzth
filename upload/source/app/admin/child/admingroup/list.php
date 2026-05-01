<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('groupsubmit') && $ids = dimplode($_GET['delete'])) {
	$gids = [];
	$query = table_common_usergroup::t()->fetch_all_by_groupid($_GET['delete']);
	foreach($query as $g) {
		$gids[] = $g['groupid'];
	}
	if($gids) {
		table_common_usergroup::t()->delete_usergroup($gids);
		table_common_usergroup_field::t()->delete($gids);
		table_common_admingroup::t()->delete($gids);
		$newgroupid = table_common_usergroup::t()->fetch_new_groupid();
		table_common_member::t()->update_by_groupid($gids, ['groupid' => $newgroupid, 'adminid' => '0'], 'UNBUFFERED');
		deletegroupcache($gids);
	}
}

$grouplist = table_common_admingroup::t()->fetch_all_merge_usergroup();
if(!submitcheck('groupsubmit')) {

	shownav('user', 'nav_admingroups');
	showsubmenu('nav_admingroups');
	showtips('admingroup_tips');

	showformheader('admingroup');
	showtableheader('', 'fixpadding');
	showsubtitle(['', 'usergroups_title', '', 'type', 'admingroup_level', 'usergroups_stars', 'usergroups_color',
		'<input class="checkbox" type="checkbox" name="gbcmember" onclick="checkAll(\'value\', this.form, \'gbmember\', \'gbcmember\', 1)" /> <a href="javascript:;" onclick="if(getmultiids()) window.open(\''.ADMINSCRIPT.'?action=usergroups&operation=edit&multi=\' + getmultiids());return false;">'.$lang['multiedit'].'</a>',
		'<input class="checkbox" type="checkbox" name="gpcmember" onclick="checkAll(\'value\', this.form, \'gpmember\', \'gpcmember\', 1)" /> <a href="javascript:;" onclick="if(getmultiids()) window.open(\''.ADMINSCRIPT.'?action=admingroup&operation=edit&multi=\' + getmultiids());return false;">'.$lang['multiedit'].'</a>',
	]);

	foreach($grouplist as $gid => $group) {
		$adminidselect = '<select name="newradminid['.$group['groupid'].']">';
		for($i = 1; $i <= 3; $i++) {
			$adminidselect .= '<option value="'.$i.'"'.($i == $group['radminid'] ? ' selected="selected"' : '').'>'.$lang['usergroups_system_'.$i].'</option>';
		}
		$adminidselect .= '</select>';
		$staticurl = STATICURL;
		showtablerow('', ['', '', 'class="td23 lightfont"', 'class="td25"', '', 'class="td25"'], [
			$group['type'] == 'system' ? '<input type="checkbox" class="checkbox" disabled="disabled" />' : "<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$group['groupid']}\">",
			'<span style="color:'.$group['color'].'">'.$group['grouptitle'].'</span>',
			"(groupid:{$group['groupid']})",
			$group['type'] == 'system' ? cplang('inbuilt') : cplang('custom'),
			$group['type'] == 'system' ? $lang['usergroups_system_'.$group['radminid']] : $adminidselect,
			"<input type=\"text\" class=\"txt\" size=\"2\" name=\"group_stars[{$group['groupid']}]\" value=\"{$group['stars']}\">",
			"<input type=\"text\" id=\"group_color_{$group['groupid']}_v\" class=\"left txt\" size=\"6\" name=\"group_color[{$group['groupid']}]\" value=\"{$group['color']}\" onchange=\"updatecolorpreview('group_color_P{$group['groupid']}')\"><input type=\"button\" id=\"group_color_{$group['groupid']}\"  class=\"colorwd\" onclick=\"group_color_{$group['groupid']}_frame.location='{$staticurl}image/admincp/getcolor.htm?group_color_{$group['groupid']}|group_color_{$group['groupid']}_v';showMenu({'ctrlid':'group_color_{$group['groupid']}'})\" style=\"background: {$group['color']}\" /><span id=\"group_color_{$group['groupid']}_menu\" style=\"display: none\"><iframe name=\"group_color_{$group['groupid']}_frame\" src=\"\" frameborder=\"0\" width=\"210\" height=\"148\" scrolling=\"no\"></iframe></span>",
			"<input class=\"checkbox\" type=\"checkbox\" chkvalue=\"gbmember\" value=\"{$group['groupid']}\" onclick=\"multiupdate(this)\" /><a href=\"".ADMINSCRIPT."?action=usergroups&operation=edit&id={$group['admingid']}\" class=\"act\">{$lang['admingroup_setting_user']}</a>",
			"<input class=\"checkbox\" type=\"checkbox\" chkvalue=\"gpmember\" value=\"{$group['groupid']}\" onclick=\"multiupdate(this)\" /><a href=\"".ADMINSCRIPT."?action=admingroup&operation=edit&id={$group['admingid']}\" class=\"act\">{$lang['admingroup_setting_admin']}</a>"
		]);
	}
	showtablerow('', ['class="td25"', '', '', '', 'colspan="6"'], [
		cplang('add_new'),
		'<input type="text" class="txt" size="12" name="grouptitlenew">',
		'',
		cplang('custom'),
		"<select name=\"radminidnew\"><option value=\"1\">{$lang['usergroups_system_1']}</option><option value=\"2\">{$lang['usergroups_system_2']}</option><option value=\"3\" selected=\"selected\">{$lang['usergroups_system_3']}</option>",
	]);
	showsubmit('groupsubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();

} else {

	foreach($grouplist as $gid => $group) {
		$stars = intval($_GET['group_stars'][$gid]);
		$color = dhtmlspecialchars($_GET['group_color'][$gid]);
		if($group['color'] != $color || $group['stars'] != $stars || $group['icon'] != $avatar) {
			table_common_usergroup::t()->update_usergroup($gid, ['stars' => $stars, 'color' => $color]);
		}
	}

	$grouptitlenew = dhtmlspecialchars(trim($_GET['grouptitlenew']));
	$radminidnew = intval($_GET['radminidnew']);

	foreach($_GET['newradminid'] as $groupid => $newradminid) {
		table_common_usergroup::t()->update_usergroup($groupid, ['radminid' => $newradminid]);
	}

	if($grouptitlenew && in_array($radminidnew, [1, 2, 3])) {

		$data = [];
		$usergroup = table_common_usergroup::t()->fetch($radminidnew);
		foreach($usergroup as $key => $val) {
			if(!in_array($key, ['groupid', 'radminid', 'type', 'system', 'grouptitle'])) {
				$val = addslashes($val);
				$data[$key] = $val;
			}
		}
		$fielddata = [];
		$usergroup = table_common_usergroup_field::t()->fetch($radminidnew);
		foreach($usergroup as $key => $val) {
			if($key != 'groupid') {
				$val = addslashes($val);
				$fielddata[$key] = $val;
			}
		}

		$adata = [];
		$admingroup = table_common_admingroup::t()->fetch($radminidnew);
		foreach($admingroup as $key => $val) {
			if($key != 'admingid') {
				$val = addslashes($val);
				$adata[$key] = $val;
			}
		}

		$data['radminid'] = $radminidnew;
		$data['type'] = 'special';
		$data['grouptitle'] = $grouptitlenew;
		$newgroupid = table_common_usergroup::t()->insert($data, true);
		if($newgroupid) {
			$adata['admingid'] = $newgroupid;
			$fielddata['groupid'] = $newgroupid;
			table_common_admingroup::t()->insert($adata);
			table_common_usergroup_field::t()->insert($fielddata);
		}
	}

	updatecache(['usergroups', 'groupreadaccess', 'admingroups', 'setting']);

	cpmsg('admingroups_edit_succeed', 'action=admingroup', 'succeed');

}
	