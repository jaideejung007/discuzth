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
	if($isfounder && isset($settingnew['mail'])) {
		$setting['mail'] = dunserialize($setting['mail']);
		$oldsmtp = $settingnew['mail']['mailsend'] == 3 ? $settingnew['mail']['smtp'] : $settingnew['mail']['esmtp'];
		$deletesmtp = $settingnew['mail']['mailsend'] != 1 ? $oldsmtp['delete'] : [];
		$settingnew['mail']['smtp'] = [];
		foreach($oldsmtp as $id => $value) {
			if((empty($deletesmtp) || !in_array($id, $deletesmtp)) && !empty($value['server']) && !empty($value['port'])) {
				$passwordmask = $setting['mail']['smtp'][$id]['auth_password'] ? $setting['mail']['smtp'][$id]['auth_password'][0].'********'.substr($setting['mail']['smtp'][$id]['auth_password'], -2) : '';
				$value['auth_password'] = $value['auth_password'] == $passwordmask ? $setting['mail']['smtp'][$id]['auth_password'] : $value['auth_password'];
				$value['timeout'] = strlen(trim($value['timeout'])) ? intval($value['timeout']) : 30;
				$settingnew['mail']['smtp'][] = $value;
			}
		}
		unset($settingnew['mail']['esmtp']);

		if(!empty($_GET['newsmtp'])) {
			foreach($_GET['newsmtp']['server'] as $id => $server) {
				if(!empty($server) && !empty($_GET['newsmtp']['port'][$id])) {
					$settingnew['mail']['smtp'][] = [
						'server' => $server,
						'port' => $_GET['newsmtp']['port'][$id] ? intval($_GET['newsmtp']['port'][$id]) : 25,
						'timeout' => strlen(trim($_GET['newsmtp']['timeout'][$id])) ? intval($_GET['newsmtp']['timeout'][$id]) : 30,
						'auth' => $_GET['newsmtp']['auth'][$id] ? 1 : 0,
						'from' => $_GET['newsmtp']['from'][$id],
						'auth_username' => $_GET['newsmtp']['auth_username'][$id],
						'auth_password' => $_GET['newsmtp']['auth_password'][$id],
						'precedence' => $_GET['newsmtp']['precedence'][$id]
					];
				}

			}
		}
	}
} else {
	shownav('founder', 'setting_mail');

	$_GET['anchor'] = in_array($_GET['anchor'], ['setting', 'check', 'seccode']) ? $_GET['anchor'] : 'setting';
	showsubmenuanchors('setting_mail', [
		['setting_mail_setting', 'mailsetting', $_GET['anchor'] == 'setting'],
		['setting_mail_check', 'mailcheck', $_GET['anchor'] == 'check'],
		['setting_mail_seccode', 'mailseccode', $_GET['anchor'] == 'seccode'],
	]);

	if(!$isfounder) {
		cpmsg('founder_action');
	}

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	$setting['mail'] = dunserialize($setting['mail']);
	$passwordmask = $setting['mail']['auth_password'] ? $setting['mail']['auth_password'][0].'********'.substr($setting['mail']['auth_password'], -2) : '';

	/*search={"setting_mail":"action=setting&operation=mail","setting_mail_setting":"action=setting&operation=mail&anchor=setting"}*/
	showtableheader('', '', 'id="mailsetting"'.($_GET['anchor'] != 'setting' ? ' style="display: none"' : ''));

	showsetting('setting_mail_setting_send', ['settingnew[mail][mailsend]', [
		[1, $lang['setting_mail_setting_send_1'], ['hidden1' => 'none', 'hidden2' => 'none', 'hidden3' => 'none']],
		[2, $lang['setting_mail_setting_send_2'], ['hidden1' => 'none', 'hidden2' => '', 'hidden3' => 'none']],
		[3, $lang['setting_mail_setting_send_3'], ['hidden1' => '', 'hidden2' => 'none', 'hidden3' => 'none']],
		[4, $lang['setting_mail_setting_send_4'], ['hidden1' => 'none', 'hidden2' => 'none', 'hidden3' => '']]
	]], $setting['mail']['mailsend'], 'mradio');
	$sendtype = $setting['mail']['mailsend'] == 2 ? 0 : 1;
	showtagheader('tbody', 'hidden1', $setting['mail']['mailsend'] == 3, 'sub');

	echo <<<EOF
		<tr><td colspan="2" style="border-top:0px dotted #DEEFFB;">
		<script type="text/JavaScript">
			var rowtypedata = [];
			function setrowtypedata(sendtype) {
				if(sendtype) {
					rowtypedata = [
						[
							[1,'', 'td25'],
							[1,'<input type="text" class="txt" name="newsmtp[server][]" style="width: 90%;">', 'td28'],
							[1,'<input type="text" class="txt" name="newsmtp[port][]" value="25">', 'td28'],
							[1,'<input type="text" class="txt" name="newsmtp[timeout][]" value="30">', 'td28'],
							[1,'<input type="checkbox" name="newsmtp[auth][]" value="1">', 'td25'],
							[1,'<input type="text" class="txt" name="newsmtp[from][]" style="width: 90%;">'],
							[1,'<input type="text" class="txt" name="newsmtp[auth_username][]" style="width: 90%;">'],
							[1,'<input type="text" class="txt" name="newsmtp[auth_password][]" style="width: 90%;">'],
							[1,'<input type="text" class="txt" name="newsmtp[precedence][]" style="width: 90%;">'],
						]
					];
				} else {
					rowtypedata = [
						[
							[1,'', 'td25'],
							[1,'<input type="text" class="txt" name="newsmtp[server][]" style="width: 90%;">', 'td28'],
							[1,'<input type="text" class="txt" name="newsmtp[port][]" value="25">', 'td28'],
							[1,'<input type="text" class="txt" name="newsmtp[timeout][]" value="30">', 'td28']
						]
					];
				}
			}

			setrowtypedata($sendtype);
		</script>

		<table style="margin-top: 0px;" class="tb tb2">
			<tr class="header">
				<th class="td25">{$lang['delete']}</th>
				<th class="td28">{$lang['setting_mail_setting_server']}</th>
				<th class="td28">{$lang['setting_mail_setting_port']}</th>
				<th class="td28">{$lang['setting_mail_setting_timeout']}</th>
			</tr>
EOF;
	foreach($setting['mail']['smtp'] as $id => $smtp) {
		$checkauth = $smtp['auth'] ? 'checked' : '';
		$smtp['auth_password'] = $smtp['auth_password'] ? $smtp['auth_password'][0].'********'.substr($smtp['auth_password'], -2) : '';
		$smtp['timeout'] = strlen(trim($smtp['timeout'])) ? intval($smtp['timeout']) : 30;
		showtablerow('', ['class="td25"', 'class="td28"', 'class="td28"', 'class="td28"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"settingnew[mail][smtp][delete][]\" value=\"$id\">",
			"<input type=\"text\" class=\"txt\" name=\"settingnew[mail][smtp][$id][server]\" value=\"{$smtp['server']}\" style=\"width: 90%;\">",
			"<input type=\"text\" class=\"txt\" name=\"settingnew[mail][smtp][$id][port]\" value=\"{$smtp['port']}\">",
			"<input type=\"text\" class=\"txt\" name=\"settingnew[mail][smtp][$id][timeout]\" value=\"{$smtp['timeout']}\">"
		]);
	}
	echo '<tr><td colspan="7"><div><a href="###" onclick="setrowtypedata(0);addrow(this, 0);" class="addtr">'.$lang['setting_mail_setting_edit_addnew'].'</a></div></td></tr>';

	showtablefooter();
	echo '</td></tr>';
	showtagfooter('tbody');
	showtagheader('tbody', 'hidden2', $setting['mail']['mailsend'] == 2, 'sub');

	echo <<<EOF
		<tr><td colspan="2" style="border-top:0px dotted #DEEFFB;">
		<table style="margin-top: 0px;" class="tb tb2">
			<tr class="header">
				<th class="td25">{$lang['delete']}</th>
				<th class="td28">{$lang['setting_mail_setting_server']}</th>
				<th class="td28">{$lang['setting_mail_setting_port']}</th>
				<th class="td28">{$lang['setting_mail_setting_timeout']}</th>
				<th id="auth_0">{$lang['setting_mail_setting_validate']}</th>
				<th id="from_0">{$lang['setting_mail_setting_from']}</th>
				<th id="username_0">{$lang['setting_mail_setting_username']}</th>
				<th id="password_0">{$lang['setting_mail_setting_password']}</th>
				<th id="precedence_0">{$lang['setting_mail_setting_precedence']}</th>
			</tr>
EOF;
	foreach($setting['mail']['smtp'] as $id => $smtp) {
		$checkauth = $smtp['auth'] ? 'checked' : '';
		$smtp['auth_password'] = $smtp['auth_password'] ? $smtp['auth_password'][0].'********'.substr($smtp['auth_password'], -2) : '';
		$smtp['timeout'] = strlen(trim($smtp['timeout'])) ? intval($smtp['timeout']) : 30;

		showtablerow('', ['class="td25"', 'class="td28"', 'class="td28"', 'class="td25"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"settingnew[mail][esmtp][delete][]\" value=\"$id\">",
			"<input type=\"text\" class=\"txt\" name=\"settingnew[mail][esmtp][$id][server]\" value=\"{$smtp['server']}\" style=\"width: 90%;\">",
			"<input type=\"text\" class=\"txt\" name=\"settingnew[mail][esmtp][$id][port]\" value=\"{$smtp['port']}\">",
			"<input type=\"text\" class=\"txt\" name=\"settingnew[mail][esmtp][$id][timeout]\" value=\"{$smtp['timeout']}\">",
			"<input type=\"checkbox\" name=\"settingnew[mail][esmtp][$id][auth]\" value=\"1\" $checkauth>",
			"<input type=\"text\" class=\"txt\" name=\"settingnew[mail][esmtp][$id][from]\" value=\"{$smtp['from']}\" style=\"width: 90%;\">",
			"<input type=\"text\" class=\"txt\" name=\"settingnew[mail][esmtp][$id][auth_username]\" value=\"{$smtp['auth_username']}\" style=\"width: 90%;\">",
			"<input type=\"text\" class=\"txt\" name=\"settingnew[mail][esmtp][$id][auth_password]\" value=\"{$smtp['auth_password']}\" style=\"width: 90%;\">",
			"<input type=\"text\" class=\"txt\" name=\"settingnew[mail][esmtp][$id][precedence]\" value=\"{$smtp['precedence']}\" style=\"width: 90%;\">",
		]);
	}
	echo '<tr><td colspan="7"><div><a href="###" onclick="setrowtypedata(1);addrow(this, 0);" class="addtr">'.$lang['setting_mail_setting_edit_addnew'].'</a></div></td></tr>';

	showtablefooter();
	echo '</td></tr>';

	showtagfooter('tbody');
	showtagheader('tbody', 'hidden3', $setting['mail']['mailsend'] == 4, 'sub');
	showsetting('setting_mail_setting_plugin', 'settingnew[mail][plugin]', $setting['mail']['plugin'], 'text');
	showtagfooter('tbody');
	showsetting('setting_mail_setting_delimiter', ['settingnew[mail][maildelimiter]', [
		[1, $lang['setting_mail_setting_delimiter_crlf']],
		[0, $lang['setting_mail_setting_delimiter_lf']],
		[2, $lang['setting_mail_setting_delimiter_cr']]]], $setting['mail']['maildelimiter'], 'mradio');
	showsetting('setting_mail_setting_includeuser', 'settingnew[mail][mailusername]', $setting['mail']['mailusername'], 'radio');
	showsetting('setting_mail_setting_silent', 'settingnew[mail][sendmail_silent]', $setting['mail']['sendmail_silent'], 'radio');
	showsubmit('settingsubmit');
	showtablefooter();
	/*search*/

	/*search={"setting_mail":"action=setting&operation=mail","setting_mail_check":"action=setting&operation=mail&anchor=check"}*/
	showtableheader('', '', 'id="mailcheck"'.($_GET['anchor'] != 'check' ? ' style="display: none"' : ''));
	showsetting('setting_mail_check_test_from', 'test_from', '', 'text');
	showsetting('setting_mail_check_test_to', 'test_to', '', 'textarea');
	showsubmit('', '', '<input type="submit" class="btn" name="mailcheck" value="'.cplang('setting_mail_check_submit').'" onclick="this.form.operation.value=\'mailcheck\';this.form.action=\''.ADMINSCRIPT.'?action=checktools&operation=mailcheck&frame=no\';this.form.target=\'mailcheckiframe\';">', '<iframe name="mailcheckiframe" style="display: none"></iframe>');
	showtablefooter();
	/*search*/

	/*search={"setting_mail":"action=setting&operation=mail","setting_mail_seccode":"action=setting&operation=mail&anchor=seccode"}*/
	showtableheader('', '', 'id="mailseccode"'.($_GET['anchor'] != 'seccode' ? ' style="display: none"' : ''));
	echo <<<EOF
		<tr>
			<th class="partition" colspan='2'>{$lang['setting_mail_seccode_title']}</th>
		</tr>
		<tr>
			<td class="tipsblock" s="1" colspan='2'>
				<ul id="mail_seccode_tipslis">
					{$lang['setting_mail_seccode_tips']}
				</ul>
			</td>
		</tr>
EOF;
	showtitle('setting_mail_seccode');
	showsetting('setting_mail_setting_emailcodestatus', 'settingnew[mail][emailcodestatus]', $setting['mail']['emailcodestatus'], 'radio', 0, 1);
	showsetting('setting_mail_setting_emailcodedefaultlength', 'settingnew[mail][emailcodedefaultlength]', $setting['mail']['emailcodedefaultlength'], 'text');
	showsetting('setting_mail_setting_emailverifylimit', 'settingnew[mail][emailverifylimit]', $setting['mail']['emailverifylimit'], 'text');
	showsetting('setting_mail_setting_emailtimelimit', 'settingnew[mail][emailtimelimit]', $setting['mail']['emailtimelimit'], 'text');
	showsetting('setting_mail_setting_emailnumlimit', 'settingnew[mail][emailnumlimit]', $setting['mail']['emailnumlimit'], 'text');
	showsetting('setting_mail_setting_emailinterval', 'settingnew[mail][emailinterval]', $setting['mail']['emailinterval'], 'text');
	showsetting('setting_mail_setting_emailglblimit', 'settingnew[mail][emailglblimit]', $setting['mail']['emailglblimit'], 'text');
	showtagfooter('tbody');
	showsubmit('settingsubmit');
	showtablefooter();
	/*search*/

	showformfooter();
}