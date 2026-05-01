<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$oldmail = dunserialize($_G['setting']['mail']);
$settingnew = $_GET['settingnew'];
$oldsmtp = $settingnew['mail']['mailsend'] == 3 ? $settingnew['mail']['smtp'] : $settingnew['mail']['esmtp'];
$deletesmtp = $settingnew['mail']['mailsend'] != 1 ? ($settingnew['mail']['mailsend'] == 3 ? $settingnew['mail']['smtp']['delete'] : $settingnew['mail']['esmtp']['delete']) : [];
$settingnew['mail']['smtp'] = [];
foreach($oldsmtp as $id => $value) {
	if((empty($deletesmtp) || !in_array($id, $deletesmtp)) && !empty($value['server']) && !empty($value['port'])) {
		$passwordmask = $oldmail['smtp'][$id]['auth_password'] ? $oldmail['smtp'][$id]['auth_password'][0].'********'.substr($oldmail['smtp'][$id]['auth_password'], -2) : '';
		$value['auth_password'] = $value['auth_password'] == $passwordmask ? $oldmail['smtp'][$id]['auth_password'] : $value['auth_password'];
		$settingnew['mail']['smtp'][] = $value;
	}
}

if(!empty($_GET['newsmtp'])) {
	foreach($_GET['newsmtp']['server'] as $id => $smtp) {
		if(!empty($smtp) && !empty($_GET['newsmtp']['port'][$id])) {
			$settingnew['mail']['smtp'][] = [
				'server' => $smtp,
				'port' => $_GET['newsmtp']['port'][$id] ? intval($_GET['newsmtp']['port'][$id]) : 25,
				'auth' => $_GET['newsmtp']['auth'][$id] ? 1 : 0,
				'from' => $_GET['newsmtp']['from'][$id],
				'auth_username' => $_GET['newsmtp']['auth_username'][$id],
				'auth_password' => $_GET['newsmtp']['auth_password'][$id],
				'precedence' => $_GET['newsmtp']['precedence'][$id]
			];
		}
	}
}

$_G['setting']['mail'] = serialize($settingnew['mail']);
$test_to = $_GET['test_to'];
$test_from = $_GET['test_from'];
$date = date('Y-m-d H:i:s');
$alertmsg = '';

$title = $lang['setting_mailcheck_title_'.$settingnew['mail']['mailsend']];
$message = $lang['setting_mailcheck_message_'.$settingnew['mail']['mailsend']].' '.$test_from.$lang['setting_mailcheck_date'].' '.$date;

$_G['setting']['bbname'] = $lang['setting_mail_check_method_1'];
include libfile('function/mail');
$succeed = sendmail($test_to, $title.' @ '.$date, $_G['setting']['bbname']."\n\n\n$message", $test_from);
$_G['setting']['bbname'] = $lang['setting_mail_check_method_2'];
$succeed = sendmail($test_to, $title.' @ '.$date, $_G['setting']['bbname']."\n\n\n$message", $test_from);

if($succeed) {
	$alertmsg = $lang['setting_mail_check_success_1']."$title @ $date".$lang['setting_mail_check_success_2'];
} else {
	$alertmsg = $lang['setting_mail_check_error'].$alertmsg;
}

echo '<script language="javascript">alert(\''.str_replace(['\'', "\n", "\r"], ['\\\'', '\n', ''], $alertmsg).'\');parent.$(\'cpform\').action=\''.ADMINSCRIPT.'?action=setting&edit=yes\';parent.$(\'cpform\').target=\'_self\';parent.$(\'cpform\').operation.value=\'mail\';</script>';
	