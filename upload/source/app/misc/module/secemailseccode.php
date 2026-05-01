<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_GET['action'] == 'send') {

	if(!defined('IN_RESTFUL')) {
		$refererhost = parse_url($_SERVER['HTTP_REFERER']);
		$refererhost['host'] .= !empty($refererhost['port']) ? (':'.$refererhost['port']) : '';

		if($refererhost['host'] != $_SERVER['HTTP_HOST']) {
			showmessage('submit_invalid');
		}
	}

	$svctype = empty($_GET['svctype']) ? 0 : $_GET['svctype'];
	$email = empty($_GET['email']) ? $_G['member']['email'] : $_GET['email'];
	list($seccodecheck, $secqaacheck) = seccheck('card');

	if((!$seccodecheck && !$secqaacheck) || submitcheck('seccodesubmit', 0, $seccodecheck, $secqaacheck)) {
		$setting_mail = dunserialize($_G['setting']['mail']);
		$length = $setting_mail['emailcodedefaultlength'] ? $setting_mail['emailcodedefaultlength'] : 6;
		$secemailseccode = random($length, 1);

		
		if(empty($email) || !preg_match('/^[\-\.\w]+@[\.\-\w]+(\.\w+)+$/', $email)) {
			showmessage('profile_email_illegal');
		}

		
		
		$result = mailcode::send($_G['uid'], 0, $svctype, $email, $secemailseccode, 0);

		
		
		
		if(is_string($result)) {
			showmessage('secemailseccode_send_failure', '', [$result]);
		} else if($result >= 0) {
			showmessage('secemailseccode_send_success', 'misc.php?mod=secemailseccode&action=send', [], ['showdialog' => 1, 'closetime' => true, 'alert' => 'right']);
		} else {
			if($result <= -1 && $result >= -9) {
				showmessage('secemailseccode_send_err_'.abs($result));
			} else {
				showmessage('secemailseccode_send_failure');
			}
		}
	} else {
		$handlekey = 'sendemailseccode';

		
		if(empty($email) || !preg_match('/^[\-\.\w]+@[\.\-\w]+(\.\w+)+$/', $email)) {
			showmessage('profile_email_illegal');
		}

		include template('common/secemailseccode');
	}

}