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
	$secmobicc = empty($_GET['secmobicc']) ? $_G['member']['secmobicc'] : $_GET['secmobicc'];
	$secmobile = empty($_GET['secmobile']) ? $_G['member']['secmobile'] : $_GET['secmobile'];
	list($seccodecheck, $secqaacheck) = seccheck('card');

	if((!$seccodecheck && !$secqaacheck) || submitcheck('seccodesubmit', 0, $seccodecheck, $secqaacheck)) {
		$length = $_G['setting']['smsdefaultlength'] ? $_G['setting']['smsdefaultlength'] : 4;
		$secmobseccode = random($length, 1);

		
		if(empty($secmobicc) || !preg_match('#^(\d){1,3}$#', $secmobicc)) {
			showmessage('profile_secmobicc_illegal');
		} else if(empty($secmobile) || !preg_match('#^(\d){1,12}$#', $secmobile)) {
			showmessage('profile_secmobile_illegal');
		}
		
		if(!empty($_G['setting']['smsmobileblacklist'])) {
			$smsmobileblacklist = $_G['setting']['smsmobileblacklist'];
			$smsmobileblacklist = explode(',', $smsmobileblacklist);
			if(in_array($secmobile, $smsmobileblacklist)) {
				showmessage('profile_secmobile_blacklist');
			}
		}
		
		if(!empty($_G['setting']['smsmobilesegmentblacklist'])) {
			$smsmobilesegmentblacklist = $_G['setting']['smsmobilesegmentblacklist'];
			$smsmobilesegmentblacklist = explode(',', $smsmobilesegmentblacklist);
			foreach($smsmobilesegmentblacklist as $smsbl) {
				if(!empty($smsbl)) {
					$smsbllen = strlen($smsbl);
					$mobilesub = substr($secmobile, 0, $smsbllen);
					if($mobilesub == $smsbl) {
						showmessage('profile_secmobile_blacklist');
					}
				}
			}
		}

		
		if(!empty($_G['setting']['smsmobilesegmentwhitelist'])) {
			$smsmobilesegmentwhitelist = $_G['setting']['smsmobilesegmentwhitelist'];
			$smsmobilesegmentwhitelist = explode(',', $smsmobilesegmentwhitelist);
			$inwl = false;
			foreach($smsmobilesegmentwhitelist as $smswl) {
				if(!empty($smswl)) {
					$smswllen = strlen($smswl);
					$mobilesub = substr($secmobile, 0, $smswllen);
					if($mobilesub == $smswl) {
						$inwl = true;
						break;
					}
				}
			}
			if(!$inwl) {
				showmessage('profile_secmobile_blacklist');
			}
		}

		
		
		$result = sms::send($_G['uid'], 0, $svctype, $secmobicc, $secmobile, $secmobseccode, 0);

		
		
		if(is_string($result)) {
			showmessage('secmobseccode_send_failure', '', [$result]);
		} else if($result >= 0) {
			if(!checkmobile()) {
				showmessage('secmobseccode_send_success', 'misc.php?mod=secmobseccode&action=send', [], ['showdialog' =>1, 'closetime' => true, 'alert' => 'right']);
			} else {
				showmessage('secmobseccode_send_success', '', [], ['alert' => 'right']);
			}
		} else {
			if($result <= -1 && $result >= -9) {
				showmessage('secmobseccode_send_err_'.abs($result));
			} else {
				showmessage('secmobseccode_send_failure');
			}
		}
	} else {
		$handlekey = 'sendsecmobseccode';

		
		if(empty($secmobicc)) {
			showmessage('profile_secmobicc_null');
		} else if(empty($secmobile)) {
			showmessage('profile_secmobile_null');
		}

		include template('common/secmobseccode');
	}

}