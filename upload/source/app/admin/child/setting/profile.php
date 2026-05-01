<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('settingsubmit')) {
	$temp = [];
	$profilegroup = dunserialize($setting['profilegroup']);
	$enabledgroup = true;
	if(!empty($settingnew['profilegroupnew'])) {
		foreach($settingnew['profilegroupnew'] as $key => $value) {
			if(!in_array($key, ['base', 'contact', 'edu', 'work', 'info'])) {
				unset($profilegroup[$key]);
				continue;
			}
			$temp[$key] = $value['displayorder'];
			$profilegroup[$key]['available'] = !empty($value['available']) ? 1 : 0;
			$profilegroup[$key]['displayorder'] = $value['displayorder'];
			$profilegroup[$key]['title'] = $value['title'];
			if($enabledgroup && $value['available']) {
				$enabledgroup = false;
			}
		}
		asort($temp);
	} else {
		if(!empty($settingnew['profile'])) {
			$prokey = $settingnew['profile']['type'];
			unset($settingnew['profile']['type']);
			$profilegroup[$prokey] = $settingnew['profile'];
		}
		foreach($profilegroup as $key => $value) {
			if(!in_array($key, ['base', 'contact', 'edu', 'work', 'info'])) {
				unset($profilegroup[$key]);
				continue;
			}
			$temp[$key] = $value['displayorder'];
			if($enabledgroup && $value['available']) {
				$enabledgroup = false;
			}
		}
		asort($temp);
	}
	foreach($temp as $key => $value) {
		if($enabledgroup) {
			$profilegroup[$key]['available'] = 1;
		}
		$settingnew['profilegroup'][$key] = $profilegroup[$key];
	}
} else {
	shownav('user', 'nav_members_profile_group');

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	$_GET['anchor'] = in_array($_GET['anchor'], ['base', 'edit']) ? $_GET['anchor'] : 'base';

	$profilegroup = dunserialize($setting['profilegroup']);
	if($_GET['anchor'] == 'edit' && in_array($_GET['type'], ['base', 'contact', 'edu', 'work', 'info'])) {
		shownav('user', 'nav_members_profile_group');
		$groupinfo = $profilegroup[$_GET['type']];
		showchildmenu([['members_profile', 'members&operation=profile'], ['members_profile_group', 'setting&operation=profile']], $groupinfo['title']);

		showtableheader();
		showsetting('setting_profile_group_name', 'settingnew[profile][title]', $groupinfo['title'], 'text');
		showsetting('setting_profile_group_available', 'settingnew[profile][available]', $groupinfo['available'], 'radio');
		showsetting('setting_profile_group_displayorder', 'settingnew[profile][displayorder]', $groupinfo['displayorder'], 'text');

		$varname = ['settingnew[profile][field]', [], 'isfloat'];
		foreach(table_common_member_profile_setting::t()->fetch_all_by_available(1) as $value) {
			if(!in_array($value['fieldid'], ['constellation', 'zodiac', 'birthyear', 'birthmonth', 'residecountry', 'resideprovince', 'birthcountry', 'birthprovince', 'residedist', 'residecommunity'])) {
				$varname[1][] = [$value['fieldid'], $value['title'], $value['fieldid']];
			}
		}
		$varname[1][] = ['sightml', $lang['setting_profile_personal_signature'], 'sightml'];
		$varname[1][] = ['customstatus', $lang['setting_profile_permission_basic_status'], 'customstatus'];
		$varname[1][] = ['timeoffset', $lang['setting_profile_time_zone'], 'timeoffset'];

		showsetting('setting_profile_field', $varname, $groupinfo['field'], 'omcheckbox');
		echo "<input type=\"hidden\" name=\"settingnew[profile][type]\" value=\"{$_GET['type']}\" />";

	} else {
		$current = [$_GET['action'] => 1];
		$profilenav = [
			['members_profile_list', 'members&operation=profile', $current['members']],
			['members_profile_group', 'setting&operation=profile', $current['setting']],
		];
		showsubmenu($lang['members_profile'], $profilenav);

		showtips('setting_profile_tips');
		showtableheader('setting_profile_group_setting', 'fixpadding');
		showsubtitle(['setting_profile_group_available', 'setting_profile_group_displayorder', 'setting_profile_group_name', ''], 'header');
		foreach($profilegroup as $key => $group) {
			showtablerow('', ['class="td25"', '', '', 'class="td25"'], [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"settingnew[profilegroupnew][$key][available]\" value=\"1\" ".($profilegroup[$key]['available'] ? 'checked' : '').' />',
				"<input type=\"text\" class=\"txt\" size=\"8\" name=\"settingnew[profilegroupnew][$key][displayorder]\" value=\"{$profilegroup[$key]['displayorder']}\">",
				"<input type=\"text\" class=\"txt\" size=\"8\" name=\"settingnew[profilegroupnew][$key][title]\" value=\"{$profilegroup[$key]['title']}\">",
				"<a href=\"".ADMINSCRIPT."?action=setting&operation=profile&anchor=edit&type=$key\">".$lang['edit'].'</a>'
			]);
		}
	}

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}