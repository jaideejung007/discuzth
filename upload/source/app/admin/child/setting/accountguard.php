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
	if($settingnew['accountguard']) {
		$settingnew['accountguard'] = serialize($settingnew['accountguard']);
	}

	if(!empty($_POST['aggid'])) {
		foreach(daddslashes($_POST['aggid']) as $gid => $v) {
			table_common_usergroup_field::t()->update($gid, ['forcelogin' => $v]);
		}
		updatecache('usergroups');
	}
} else {
	shownav('safe', 'setting_accountguard');

	showsubmenu('setting_'.$operation);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	loadcache('usergroups');
	$setting['accountguard'] = dunserialize($setting['accountguard']);
	$usergroups = table_common_usergroup_field::t()->fetch_all(array_keys($_G['cache']['usergroups']));
	/*search={"setting_accountguard":"action=setting&operation=sec","setting_sec_reginput":"action=setting&operation=sec&anchor=accountguard"}*/
	showtableheader('', 'nobottom');
	$forcelogin = '<tr class="header"><td></td><td>'.cplang('usergroups_edit_basic_forcelogin_none').'</td><td>'.cplang('usergroups_edit_basic_forcelogin_mail').'</td></tr>';
	ksort($_G['cache']['usergroups']);
	foreach($_G['cache']['usergroups'] as $gid => $usergroup) {
		if(in_array($gid, [7, 8])) {
			continue;
		}
		$forcelogin .= '<tr class="hover"><td>'.$usergroup['grouptitle'].'</td>'.
			'<td><label><input class="radio" type="radio" name="aggid['.$gid.']" '.(!$usergroups[$gid]['forcelogin'] ? 'checked ' : '').'value="0">'.'</label></td>'.
			'<td><label><input class="radio" type="radio" name="aggid['.$gid.']" '.($usergroups[$gid]['forcelogin'] == 2 ? 'checked ' : '').'value="2">'.'</label></td>'.
			'</tr>';
	}
	$forcelogin .= '<tr><td colspan="3" class="lineheight">'.cplang('setting_sec_accountguard_forcelogin_comment').'</td></table>';
	showsetting('setting_sec_accountguard_loginpwcheck', ['settingnew[accountguard][loginpwcheck]', [
		[0, $lang['setting_sec_accountguard_loginpwcheck_none']],
		[1, $lang['setting_sec_accountguard_loginpwcheck_prompt']],
		[2, $lang['setting_sec_accountguard_loginpwcheck_force']]]], $setting['accountguard']['loginpwcheck'], 'mradio');
	showsetting('setting_sec_accountguard_loginoutofdate', 'settingnew[accountguard][loginoutofdate]', $setting['accountguard']['loginoutofdate'], 'radio');
	showsetting('setting_sec_accountguard_loginoutofdatenum', 'settingnew[accountguard][loginoutofdatenum]', $setting['accountguard']['loginoutofdatenum'], 'text');
	showtablefooter();
	showtableheader('', 'nobottom');
	echo $forcelogin;
	showtablefooter();
	/*search*/
	showtableheader();
	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}