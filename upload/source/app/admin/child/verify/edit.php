<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

shownav('user', 'nav_members_verify');
$vid = $_GET['vid'] < 8 ? intval($_GET['vid']) : 0;
showchildmenu([['members_verify', 'verify']], 'verify'.$vid);

$verifyarr = $_G['setting']['verify'][$vid];
if(!submitcheck('verifysubmit')) {
	if($vid == 7) {
		showtips('members_verify_setting_tips');
	}
	showformheader("verify&operation=edit&vid=$vid", 'enctype');
	showtableheader();
	$readonly = $vid == 6 || $vid == 7 ? 'readonly' : '';
	showsetting('members_verify_title', 'verify[title]', $verifyarr['title'], 'text', $readonly);
	showsetting('members_verify_enable', 'verify[available]', $verifyarr['available'], 'radio');
	$verificonhtml = '';
	if($verifyarr['icon']) {
		$icon_url = parse_url($verifyarr['icon']);
		$prefix = !$icon_url['host'] && !str_contains($verifyarr['icon'], $_G['setting']['attachurl'].'common/') ? $_G['setting']['attachurl'].'common/' : '';
		$verificonhtml = '<label><input type="checkbox" class="checkbox" name="deleteicon['.$vid.']" value="yes" /> '.$lang['delete'].'</label><br /><img src="'.$prefix.$verifyarr['icon'].'" />';
	}
	$unverifyiconhtml = '';
	if($verifyarr['unverifyicon']) {
		$unverifyiconurl = parse_url($verifyarr['unverifyicon']);

		$prefix = !$unverifyiconurl['host'] && !str_contains($verifyarr['unverifyicon'], $_G['setting']['attachurl'].'common/') ? $_G['setting']['attachurl'].'common/' : '';
		$unverifyiconhtml = '<label><input type="checkbox" class="checkbox" name="delunverifyicon['.$vid.']" value="yes" /> '.$lang['delete'].'</label><br /><img src="'.$prefix.$verifyarr['unverifyicon'].'" />';
	}
	showsetting('members_verify_showicon', 'verify[showicon]', $verifyarr['showicon'], 'radio', '', 1);
	showsetting('members_unverify_icon', 'unverifyiconnew', (!$unverifyiconurl['host'] ? str_replace($_G['setting']['attachurl'].'common/', '', $verifyarr['unverifyicon']) : $verifyarr['unverifyicon']), 'filetext', '', 0, $unverifyiconhtml);
	showsetting('members_verify_icon', 'iconnew', (!$icon_url['host'] ? str_replace($_G['setting']['attachurl'].'common/', '', $verifyarr['icon']) : $verifyarr['icon']), 'filetext', '', 0, $verificonhtml);
	showtagfooter('tbody');

	if($vid == 6) {
		showsetting('members_verify_view_real_name', 'verify[viewrealname]', $verifyarr['viewrealname'], 'radio');
	}
	if($vid) {
		$varname = ['verify[field]', [], 'isfloat'];
		foreach(table_common_member_profile_setting::t()->fetch_all_by_available(1) as $value) {
			if(!in_array($value['fieldid'], ['constellation', 'zodiac', 'birthyear', 'birthmonth', 'birthcountry', 'birthprovince', 'birthdist', 'birthcommunity', 'residecountry', 'resideprovince', 'residedist', 'residecommunity'])) {
				$varname[1][] = [$value['fieldid'], $value['title'], $value['fieldid']];
			}
		}

		showsetting('members_verify_setting_field', $varname, $verifyarr['field'], 'omcheckbox');
	}
	$groupselect = [];
	foreach(table_common_usergroup::t()->fetch_all_not([6, 7]) as $group) {
		$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
		$groupselect[$group['type']] .= "<option value=\"{$group['groupid']}\" ".((is_array($verifyarr['groupid']) && in_array($group['groupid'], $verifyarr['groupid'])) ? 'selected' : '').">{$group['grouptitle']}</option>\n";
	}
	$groupselect = '<optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
		($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
		($groupselect['specialadmin'] ? '<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
		'<optgroup label="'.$lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup>';
	showsetting('members_verify_group', '', '', '<select name="verify[groupid][]" multiple="multiple" size="10">'.$groupselect.'</select>');

	showsubmit('verifysubmit');
	showtablefooter();
	showformfooter();
} else {
	foreach($_G['setting']['verify'] as $key => $value) {
		if(!is_array($value)) {
			continue;
		}
		$_G['setting']['verify'][$key]['icon'] = str_replace($_G['setting']['attachurl'].'common/', '', $value['icon']);
		$_G['setting']['verify'][$key]['unverifyicon'] = str_replace($_G['setting']['attachurl'].'common/', '', $value['unverifyicon']);
	}
	$verifynew = getgpc('verify');
	if($vid == 6 || $vid == 7) {
		$verifynew['title'] = $_G['setting']['verify'][$vid]['title'];
	}
	if($verifynew['available'] == 1 && !trim($verifynew['title'])) {
		cpmsg('members_verify_update_title_error', '', 'error');
	}
	$verifynew['icon'] = getverifyicon('iconnew', $vid);
	$verifynew['unverifyicon'] = getverifyicon('unverifyiconnew', $vid, 'unverify_icon');

	if($_GET['deleteicon']) {
		$verifynew['icon'] = delverifyicon($verifyarr['icon']);
	}
	if($_GET['delunverifyicon']) {
		$verifynew['unverifyicon'] = delverifyicon($verifyarr['unverifyicon']);
	}
	if(!empty($verifynew['field']['residecity'])) {
		$verifynew['field']['residecountry'] = 'residecountry';
		$verifynew['field']['resideprovince'] = 'resideprovince';
		$verifynew['field']['residedist'] = 'residedist';
		$verifynew['field']['residecommunity'] = 'residecommunity';
	}
	if(!empty($verifynew['field']['birthday'])) {
		$verifynew['field']['birthyear'] = 'birthyear';
		$verifynew['field']['birthmonth'] = 'birthmonth';
	}
	if(!empty($verifynew['field']['birthcity'])) {
		$verifynew['field']['birthcountry'] = 'birthcountry';
		$verifynew['field']['birthprovince'] = 'birthprovince';
		$verifynew['field']['birthdist'] = 'birthdist';
		$verifynew['field']['birthcommunity'] = 'birthcommunity';
	}
	$verifynew['groupid'] = !empty($verifynew['groupid']) && is_array($verifynew['groupid']) ? $verifynew['groupid'] : [];
	$_G['setting']['verify'][$vid] = $verifynew;
	$_G['setting']['verify']['enabled'] = false;
	for($i = 1; $i < 8; $i++) {
		if($_G['setting']['verify'][$i]['available'] && !$_G['setting']['verify']['enabled']) {
			$_G['setting']['verify']['enabled'] = true;
		}
		if($_G['setting']['verify'][$i]['icon']) {
			$icon_url = parse_url($_G['setting']['verify'][$i]['icon']);
		}
		$_G['setting']['verify'][$i]['icon'] = !$icon_url['host'] ? str_replace($_G['setting']['attachurl'].'common/', '', $_G['setting']['verify'][$i]['icon']) : $_G['setting']['verify'][$i]['icon'];
	}
	table_common_setting::t()->update_setting('verify', $_G['setting']['verify']);
	if(isset($verifynew['viewrealname']) && !$verifynew['viewrealname']) {
		table_common_member_profile_setting::t()->update('realname', ['showinthread' => 0]);
		$custominfo = table_common_setting::t()->fetch_setting('customauthorinfo', true);
		if(isset($custominfo[0]['field_realname'])) {
			unset($custominfo[0]['field_realname']);
			table_common_setting::t()->update_setting('customauthorinfo', $custominfo);
			updatecache(['custominfo']);
		}
	}
	updatecache(['setting']);
	cpmsg('members_verify_update_succeed', 'action=verify', 'succeed');
}
	