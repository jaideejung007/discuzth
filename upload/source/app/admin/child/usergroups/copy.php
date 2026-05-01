<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('usergroups');

$source = intval($_GET['source']);
$sourceusergroup = $_G['cache']['usergroups'][$source];

if(empty($sourceusergroup)) {
	cpmsg('usergroups_copy_source_invalid', '', 'error');
}

$delfields = [
	'usergroups' => ['groupid', 'radminid', 'type', 'system', 'grouptitle', 'creditshigher', 'creditslower', 'stars', 'color', 'icon', 'groupavatar'],
];
$fields = [
	'usergroups' => table_common_usergroup::t()->fetch_table_struct(),
	'usergroupfields' => table_common_usergroup_field::t()->fetch_table_struct(),
];

if(!submitcheck('copysubmit')) {

	$groupselect = [];
	foreach(table_common_usergroup::t()->fetch_all_not([6, 7], true) as $group) {
		$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
		$groupselect[$group['type']] .= "<option value=\"{$group['groupid']}\">{$group['grouptitle']}</option>\n";
	}
	$groupselect = '<optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
		($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
		($groupselect['specialadmin'] ? '<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
		'<optgroup label="'.$lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup>';

	$usergroupselect = '<select name="target[]" size="10" multiple="multiple">'.$groupselect.'</select>';
	$optselect = '<select name="options[]" size="10" multiple="multiple">';
	$fieldarray = array_merge($fields['usergroups'], $fields['usergroupfields']);
	$listfields = array_diff($fieldarray, $delfields['usergroups']);
	foreach($listfields as $field) {
		if(isset($lang['project_option_group_'.$field])) {
			$optselect .= '<option value="'.$field.'">'.$lang['project_option_group_'.$field].'</option>';
		}
	}
	$optselect .= '</select>';
	shownav('user', 'usergroups_copy');
	showchildmenu([['nav_usergroups', 'usergroups']], cplang('usergroups_copy'));
	showtips('usergroups_copy_tips');
	showformheader('usergroups&operation=copy');
	showhiddenfields(['source' => $source]);
	showtableheader();
	showtitle('usergroups_copy');
	showsetting(cplang('usergroups_copy_source').':', '', '', $sourceusergroup['grouptitle']);
	showsetting('usergroups_copy_target', '', '', $usergroupselect);
	showsetting('usergroups_copy_options', '', '', $optselect);
	showsubmit('copysubmit');
	showtablefooter();
	showformfooter();

} else {

	$gids = $comma = '';
	if(!empty($_GET['target']) && is_array($_GET['target']) && count($_GET['target'])) {
		foreach($_GET['target'] as $key => $gid) {
			$_GET['target'][$key] = intval($gid);
			if(empty($_GET['target'][$key]) || $_GET['target'][$key] == $source) {
				unset($_GET['target'][$key]);
			}
		}
	} else {
		cpmsg('usergroups_copy_target_invalid', '', 'error');
	}

	$groupoptions = [];
	if(is_array($_GET['options']) && !empty($_GET['options'])) {
		foreach($_GET['options'] as $option) {
			if($option = trim($option)) {
				if(in_array($option, $fields['usergroups'])) {
					$groupoptions['common_usergroup'][] = $option;
				} elseif(in_array($option, $fields['usergroupfields'])) {
					$groupoptions['common_usergroup_field'][] = $option;
				}
			}
		}
	}

	if(empty($groupoptions)) {
		cpmsg('usergroups_copy_options_invalid', '', 'error');
	}
	foreach(['common_usergroup', 'common_usergroup_field'] as $table) {
		if(is_array($groupoptions[$table]) && !empty($groupoptions[$table])) {
			$sourceusergroup = C::t($table)->fetch($source);
			if(!$sourceusergroup) {
				cpmsg('usergroups_copy_source_invalid', '', 'error');
			}
			foreach($sourceusergroup as $key => $value) {
				if(!in_array($key, $groupoptions[$table])) {
					unset($sourceusergroup[$key]);
				}
			}
			C::t($table)->update($_GET['target'], $sourceusergroup);
		}
	}

	updatecache('usergroups');
	cpmsg('usergroups_copy_succeed', 'action=usergroups', 'succeed');

}
	