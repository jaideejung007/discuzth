<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loaducenter();
if(UC_STANDALONE) {
	$ucsetting = uc_get_settings();
}

if(submitcheck('settingsubmit')) {
	isset($settingnew['regname']) && empty($settingnew['regname']) && $settingnew['regname'] = 'register';
	isset($settingnew['reglinkname']) && empty($settingnew['reglinkname']) && $settingnew['reglinkname'] = cplang('reglinkname_default');

	$settingnew['pwlength'] = intval($settingnew['pwlength']);
	$settingnew['regstatus'] = (array)$settingnew['regstatus'];
	if(in_array('open', $settingnew['regstatus']) && in_array('invite', $settingnew['regstatus'])) {
		$settingnew['regstatus'] = 3;
	} elseif(in_array('open', $settingnew['regstatus'])) {
		$settingnew['regstatus'] = 1;
	} elseif(in_array('invite', $settingnew['regstatus'])) {
		$settingnew['regstatus'] = 2;
	} else {
		$settingnew['regstatus'] = 0;
	}

	$settingnew['welcomemsg'] = (array)$settingnew['welcomemsg'];
	if(in_array('1', $settingnew['welcomemsg']) && in_array('2', $settingnew['welcomemsg'])) {
		$settingnew['welcomemsg'] = 3;
	} elseif(in_array('1', $settingnew['welcomemsg'])) {
		$settingnew['welcomemsg'] = 1;
	} elseif(in_array('2', $settingnew['welcomemsg'])) {
		$settingnew['welcomemsg'] = 2;
	} else {
		$settingnew['welcomemsg'] = 0;
	}

	if(empty($settingnew['strongpw'])) {
		$settingnew['strongpw'] = [];
	}

	if(isset($settingnew['censoruser'])) {
		$settingnew['censoruser'] = trim(preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/", "\r\n", $settingnew['censoruser']));
	}

	foreach(['inviteconfig' => 'inviteipwhite', 'ipverifywhite', 'ipregctrl', 'ipaccess', 'adminipaccess'] as $ipkey => $ipfield) {
		if(!is_int($ipkey)) {
			if(isset($settingnew[$ipkey][$ipfield])) {
				$ipfilterpointer = &$settingnew[$ipkey][$ipfield];
			}
		} else {
			if(isset($settingnew[$ipfield])) {
				$ipfilterpointer = &$settingnew[$ipfield];
			}
		}
		if(isset($ipfilterpointer)) {
			$ipfilterpointer = trim(preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/", "\r\n", $ipfilterpointer));
		}
		unset($ipfilterpointer);
	}

	if(!empty($settingnew['ipaccess']) && !ipaccess($_G['clientip'], $settingnew['ipaccess'])) {
		cpmsg('setting_ipaccess_invalid', '', 'error');
	}

	if(isset($settingnew['adminipaccess'])) {
		if($settingnew['adminipaccess'] = trim(preg_replace("/(\s*(\r\n|\n\r|\n|\r)\s*)/", "\r\n", $settingnew['adminipaccess']))) {
			if(!ipaccess($_G['clientip'], $settingnew['adminipaccess'])) {
				cpmsg('setting_adminipaccess_invalid', '', 'error');
			}
		}
	}

	if(isset($settingnew['welcomemsgtitle'])) {
		$settingnew['welcomemsgtitle'] = cutstr(trim(dhtmlspecialchars($settingnew['welcomemsgtitle'])), 75);
	}

	if(isset($settingnew['inviteconfig'])) {
		if($settingnew['inviteconfig']['invitecodeprice']) {
			$settingnew['inviteconfig']['invitecodeprice'] = round(abs($settingnew['inviteconfig']['invitecodeprice']), 2);
		}
	}

	if(isset($settingnew['domainwhitelist'])) {
		$settingnew['domainwhitelist'] = trim(preg_replace("/(\s*(\r\n|\n\r|\n|\r)\s*)/", "\r\n", $settingnew['domainwhitelist']));
	}
	if(empty($settingnew['domainwhitelist'])) {
		$settingnew['domainwhitelist_affectimg'] = 0;
	}
	$settingnew['domainwhitelist_affectimg'] = intval($settingnew['domainwhitelist_affectimg']);
	if(UC_STANDALONE) {
		uc_set_settings($_GET['ucsettingnew']);
	}
} else {
	shownav('global', 'setting_'.$operation);

	$_GET['anchor'] = in_array($_GET['anchor'], ['register', 'access']) ? $_GET['anchor'] : 'register';
	showsubmenuanchors('setting_access', [
		['setting_access_register', 'register', $_GET['anchor'] == 'register'],
		['setting_access_access', 'access', $_GET['anchor'] == 'access']
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	$wmsgcheck = [$setting['welcomemsg'] => 'checked'];
	$setting['inviteconfig'] = dunserialize($setting['inviteconfig']);
	$setting['extcredits'] = dunserialize($setting['extcredits']);
	$buycredits = $rewardcredits = '';
	for($i = 0; $i <= 8; $i++) {
		if($setting['extcredits'][$i]['available']) {
			$extcredit = 'extcredits'.$i.' ('.$setting['extcredits'][$i]['title'].')';
			$buycredits .= '<option value="'.$i.'" '.($i == intval($setting['inviteconfig']['invitecredit']) ? 'selected' : '').'>'.($i ? $extcredit : $lang['none']).'</option>';
			$rewardcredits .= '<option value="'.$i.'" '.($i == intval($setting['inviteconfig']['inviterewardcredit']) ? 'selected' : '').'>'.($i ? $extcredit : $lang['none']).'</option>';
		}
	}

	$groupselect = '';
	foreach(table_common_usergroup::t()->fetch_all_by_type('special') as $group) {
		$groupselect .= "<option value=\"{$group['groupid']}\" ".($group['groupid'] == $setting['inviteconfig']['invitegroupid'] ? 'selected' : '').">{$group['grouptitle']}</option>\n";
	}

	$taskarray = [['', cplang('select')]];
	foreach(table_common_task::t()->fetch_all_by_available(2) as $task) {
		$taskarray[] = [$task['taskid'], $task['name']];
	}

	/*search={"setting_access":"action=setting&operation=access","setting_access_register":"action=setting&operation=access&anchor=register"}*/
	showtableheader('', 'nobottom', 'id="register"'.($_GET['anchor'] != 'register' ? ' style="display: none"' : ''));
	$regstatus = [];
	if($setting['regstatus'] == 1 || $setting['regstatus'] == 3) {
		$regstatus[] = 'open';
	}
	if($setting['regstatus'] == 2 || $setting['regstatus'] == 3) {
		$regstatus[] = 'invite';
	}
	showsetting('setting_access_register_status', ['settingnew[regstatus]', [
		['open', $lang['setting_access_register_open']],
		['invite', $lang['setting_access_register_invite'], 'showinvite'],
	]], $regstatus, 'mcheckbox');

	showtagheader('tbody', 'showinvite', in_array('invite', $regstatus), 'sub');
	showsetting('setting_access_register_invite_buyprompt', 'settingnew[inviteconfig][invitecodeprompt]', $setting['inviteconfig']['invitecodeprompt'], 'textarea');
	showsetting('setting_access_register_invite_buy', 'settingnew[inviteconfig][buyinvitecode]', $setting['inviteconfig']['buyinvitecode'], 'radio');
	showsetting('setting_access_register_invite_buyprice', 'settingnew[inviteconfig][invitecodeprice]', $setting['inviteconfig']['invitecodeprice'], 'text');
	showsetting('setting_access_register_invite_credit', '', '', '<select name="settingnew[inviteconfig][inviterewardcredit]">'.$rewardcredits.'</select>');
	showsetting('setting_access_register_invite_addcredit', 'settingnew[inviteconfig][inviteaddcredit]', $setting['inviteconfig']['inviteaddcredit'], 'text');
	showsetting('setting_access_register_invite_invitedcredit', 'settingnew[inviteconfig][invitedaddcredit]', $setting['inviteconfig']['invitedaddcredit'], 'text');
	showsetting('setting_access_register_invite_group', '', '', '<select name="settingnew[inviteconfig][invitegroupid]"><option value="0">'.$lang['usergroups_system_0'].'</option>'.$groupselect.'</select>');
	showsetting('setting_access_register_invite_areawhite', 'settingnew[inviteconfig][inviteareawhite]', $setting['inviteconfig']['inviteareawhite'], 'textarea');
	showsetting('setting_access_register_invite_ipwhite', 'settingnew[inviteconfig][inviteipwhite]', $setting['inviteconfig']['inviteipwhite'], 'textarea');
	showtagfooter('tbody');

	showsetting('setting_access_register_regclosemessage', 'settingnew[regclosemessage]', $setting['regclosemessage'], 'textarea');
	showsetting('setting_access_register_name', 'settingnew[regname]', $setting['regname'], 'text');
	showsetting('setting_access_register_regemail', 'settingnew[regemail]', $setting['regemail'], 'radio');
	if(UC_STANDALONE) {
		showsetting('uc_setting_doublee', 'ucsettingnew[doublee]', $ucsetting['doublee'], 'radio');
		showsetting('uc_setting_accessemail', 'ucsettingnew[accessemail]', $ucsetting['accessemail'], 'textarea');
		showsetting('uc_setting_censoremail', 'ucsettingnew[censoremail]', $ucsetting['censoremail'], 'textarea');
	}
	showsetting('setting_access_register_send_register_url', 'settingnew[sendregisterurl]', $setting['sendregisterurl'], 'radio');
	showsetting('setting_access_register_link_name', 'settingnew[reglinkname]', $setting['reglinkname'], 'text');
	showsetting('setting_access_register_censoruser', 'settingnew[censoruser]', $setting['censoruser'], 'textarea');
	showsetting('setting_access_register_pwlength', 'settingnew[pwlength]', $setting['pwlength'], 'text');
	$setting['strongpw'] = dunserialize($setting['strongpw']);
	showsetting('setting_access_register_strongpw', ['settingnew[strongpw]', [
		['1', $lang['setting_access_register_strongpw_1']],
		['2', $lang['setting_access_register_strongpw_2']],
		['3', $lang['setting_access_register_strongpw_3']],
		['4', $lang['setting_access_register_strongpw_4']],
	]], $setting['strongpw'], 'mcheckbox2');
	showsetting('setting_access_register_verify', ['settingnew[regverify]', [
		[0, $lang['none'], ['regverifyext' => 'none']],
		[1, $lang['setting_access_register_verify_email'], ['regverifyext' => '']],
		[2, $lang['setting_access_register_verify_manual'], ['regverifyext' => '']]
	]], $setting['regverify'], 'mradio');
	showtagheader('tbody', 'regverifyext', $setting['regverify'], 'sub');
	showsetting('setting_access_register_verify_areawhite', 'settingnew[areaverifywhite]', $setting['areaverifywhite'], 'textarea');
	showsetting('setting_access_register_verify_ipwhite', 'settingnew[ipverifywhite]', $setting['ipverifywhite'], 'textarea');
	showtagfooter('tbody');
	showsetting('setting_access_register_maildomain', ['settingnew[regmaildomain]', [
		[0, $lang['none'], ['regmaildomainext' => 'none']],
		[1, $lang['setting_access_register_maildomain_white'], ['regmaildomainext' => '']],
		[2, $lang['setting_access_register_maildomain_black'], ['regmaildomainext' => '']]
	]], $setting['regmaildomain'], 'mradio');
	showtagheader('tbody', 'regmaildomainext', $setting['regmaildomain'], 'sub');
	showsetting('setting_access_register_maildomain_list', 'settingnew[maildomainlist]', $setting['maildomainlist'], 'textarea');
	showtagfooter('tbody');
	showsetting('setting_access_register_ctrl', 'settingnew[regctrl]', $setting['regctrl'], 'text');
	showsetting('setting_access_register_floodctrl', 'settingnew[regfloodctrl]', $setting['regfloodctrl'], 'text');
	showsetting('setting_access_register_ipctrl_time', 'settingnew[ipregctrltime]', $setting['ipregctrltime'], 'text');
	showsetting('setting_access_register_ipctrl', 'settingnew[ipregctrl]', $setting['ipregctrl'], 'textarea');
	$welcomemsg = [];
	if($setting['welcomemsg'] == 1) {
		$welcomemsg[] = '1';
	} elseif($setting['welcomemsg'] == 2) {
		$welcomemsg[] = '2';
	} elseif($setting['welcomemsg'] == 3) {
		$welcomemsg[] = '1';
		$welcomemsg[] = '2';
	} else {
		$welcomemsg[] = '0';
	}
	showsetting('setting_access_register_welcomemsg', ['settingnew[welcomemsg]', [
		[1, $lang['setting_access_register_welcomemsg_pm']],
		[2, $lang['setting_access_register_welcomemsg_email']]
	]], $welcomemsg, 'mcheckbox');
	showsetting('setting_access_register_welcomemsgtitle', 'settingnew[welcomemsgtitle]', $setting['welcomemsgtitle'], 'text');
	showsetting('setting_access_register_welcomemsgtxt', 'settingnew[welcomemsgtxt]', $setting['welcomemsgtxt'], 'textarea');
	showsetting('setting_access_register_bbrules', 'settingnew[bbrules]', $setting['bbrules'], 'radio', '', 1);
	showsetting('setting_access_register_bbruleforce', 'settingnew[bbrulesforce]', $setting['bbrulesforce'], 'radio');
	showsetting('setting_access_register_bbrulestxt', 'settingnew[bbrulestxt]', $setting['bbrulestxt'], 'textarea');
	showtablefooter();
	/*search*/

	/*search={"setting_access":"action=setting&operation=access","setting_access_access":"action=setting&operation=access&anchor=access"}*/
	showtableheader('', 'nobottom', 'id="access"'.($_GET['anchor'] != 'access' ? ' style="display: none"' : ''));
	showsetting('setting_access_access_newbiespan', 'settingnew[newbiespan]', $setting['newbiespan'], 'text');
	showsetting('setting_access_access_ipaccess', 'settingnew[ipaccess]', $setting['ipaccess'], 'textarea');
	showsetting('setting_access_access_adminipaccess', 'settingnew[adminipaccess]', $setting['adminipaccess'], 'textarea');
	showsetting('setting_access_access_domainwhitelist', 'settingnew[domainwhitelist]', '', '<textarea class="tarea" cols="50" id="settingnew[domainwhitelist]" name="settingnew[domainwhitelist]" onkeydown="textareakey(this, event)" onkeyup="textareasize(this, 0)" ondblclick="textareasize(this, 1)" rows="6">'.$setting['domainwhitelist'].'</textarea><br><input class="checkbox" type="checkbox" value="1" name="settingnew[domainwhitelist_affectimg]" '.($setting['domainwhitelist_affectimg'] ? 'checked' : '').'>'.cplang('setting_access_access_domainwhitelist_affectimg'));
	showtablefooter();
	/*search*/

	showtableheader('', 'notop');
	showsubmit('settingsubmit');
	showtablefooter();
	showformfooter();

}