<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


class mailcode {

	
	
	const DISCUZ_CLASS_EMAIL_TYPE_SECCODE = 0;
	const DISCUZ_CLASS_EMAIL_TYPE_MESSAGE = 1;

	
	
	
	const DISCUZ_CLASS_EMAIL_SRVTYPE_OTHERSRV = 0;
	const DISCUZ_CLASS_EMAIL_SRVTYPE_SECCHECK = 1;
	const DISCUZ_CLASS_EMAIL_SRVTYPE_NEWSLETT = 2;

	
	
	
	
	const DISCUZ_CLASS_EMAIL_ERROR_NOWNOERR = 0;
	const DISCUZ_CLASS_EMAIL_ERROR_TIMELESS = -1;
	const DISCUZ_CLASS_EMAIL_ERROR_NUMLIMIT = -2;
	const DISCUZ_CLASS_EMAIL_ERROR_GLBLIMIT = -4;
	const DISCUZ_CLASS_EMAIL_ERROR_EMAILDISAB = -8;
	const DISCUZ_CLASS_EMAIL_ERROR_EMAILGWERR = -9;

	
	
	const DISCUZ_CLASS_EMAIL_VERIFY_FAIL = 0;
	const DISCUZ_CLASS_EMAIL_VERIFY_PASS = 1;
	const DISCUZ_CLASS_EMAIL_VERIFY_INVALID = -1;

	
	
	const DISCUZ_CLASS_EMAIL_SEND_FAIL = 0;
	const DISCUZ_CLASS_EMAIL_SEND_SUCCESS = 1;

	
	public static function verify($uid, $svctype, $email, $seccode, $updateverify = 1) {
		$setting_mail = dunserialize(getglobal('setting/mail'));
		
		$emailtimelimit = $setting_mail['emailtimelimit'];
		$emailtimelimit = $emailtimelimit > 0 ? $emailtimelimit : 86400;
		$emailverifylimit = $setting_mail['emailverifylimit'];
		$emailverifylimit = $emailverifylimit > 0 ? $emailverifylimit : 5;
		$lastsend = table_common_emaillog::t()->get_lastemail_by_uese($uid, 0, $svctype, $email);
		$result = self::DISCUZ_CLASS_EMAIL_VERIFY_FAIL;
		if($seccode == $lastsend['content'] && $lastsend['verify'] < $emailverifylimit && time() - $lastsend['dateline'] < $emailtimelimit) {
			$result = self::DISCUZ_CLASS_EMAIL_VERIFY_PASS;
		}
		if($lastsend['verify'] >= $emailverifylimit) {
			$result = self::DISCUZ_CLASS_EMAIL_VERIFY_INVALID;
		}
		if($updateverify && $result != self::DISCUZ_CLASS_EMAIL_VERIFY_INVALID) {
			table_common_emaillog::t()->update($lastsend['logid'], ['verify' => $lastsend['verify'] + 1]);
		}
		return $result;
	}

	public static function send($uid, $emailtype, $svctype, $email, $content, $force) {
		
		$time = time();
		$ip = getglobal('clientip');
		$port = getglobal('remoteport');

		
		$check = self::check($uid, $email, $time, $ip, $port, $force);
		if($check < 0) {
			self::log($emailtype, $svctype, $check, $uid, $email, $time, $ip, $port, $content);
			return $check;
		}

		
		$output = self::output($uid, $emailtype, $svctype, $email, $content);
		$status = $output ? 0 : 1;
		self::log($emailtype, $svctype, $status, $uid, $email, $time, $ip, $port, $content);
		return $output;
	}

	protected static function check($uid, $email, $time, $ip, $port, $force) {
		$setting_mail = dunserialize(getglobal('setting/mail'));
		
		
		if(!$setting_mail['emailcodestatus']) {
			return self::DISCUZ_CLASS_EMAIL_ERROR_EMAILDISAB;
		}

		if(!$force) {
			
			$emailtimelimit = $setting_mail['emailtimelimit'];
			$emailtimelimit = $emailtimelimit > 0 ? $emailtimelimit : 86400;
			
			$emailnumlimit = $setting_mail['emailnumlimit'];
			$emailnumlimit = $emailnumlimit > 0 ? $emailnumlimit : 5;
			
			$emailinterval = $setting_mail['emailinterval'];
			$emailinterval = $emailinterval > 0 ? $emailinterval : 300;
			
			$emailglblimit = $setting_mail['emailglblimit'];
			$emailglblimit = $emailglblimit > 0 ? $emailglblimit : 1000;

			
			$ut = table_common_emaillog::t()->get_email_by_ut($uid, $emailtimelimit);
			$et = table_common_emaillog::t()->get_email_by_et($email, $emailtimelimit);
			if($time - $ut[0]['dateline'] < $emailinterval || $time - $et[0]['dateline'] < $emailinterval) {
				return self::DISCUZ_CLASS_EMAIL_ERROR_TIMELESS;
			}
			if(count($ut) > $emailnumlimit || count($et) > $emailnumlimit) {
				return self::DISCUZ_CLASS_EMAIL_ERROR_NUMLIMIT;
			}

			
			$globalsend = table_common_emaillog::t()->count_email_by_time($emailtimelimit);
			if($globalsend > $emailglblimit) {
				return self::DISCUZ_CLASS_EMAIL_ERROR_GLBLIMIT;
			}
		}

		return self::DISCUZ_CLASS_EMAIL_ERROR_NOWNOERR;
	}


	protected static function output($uid, $emailtype, $svctype, $email, $content) {
		global $_G;
		$setting_mail = dunserialize(getglobal('setting/mail'));
		
		$emailinterval = $setting_mail['emailinterval'];
		$emailinterval = $emailinterval > 0 ? $emailinterval : 300;
		$email_seccode_subject = [
			'tpl' => 'email_seccode_verify',
			'var' => [
				'seccode' => $content,
				'emailinterval' => intval($emailinterval / 60),
				'siteurl' => $_G['siteurl'],
				'bbname' => $_G['setting']['bbname'],
			]
		];

		if(!function_exists('sendmail')) {
			include libfile('function/mail');
		}
		if(!sendmail($email, $email_seccode_subject)) {
			runlog('sendmail', "E-MAIL SECCODE SEND : {$email} sendmail failed.");
			return self::DISCUZ_CLASS_EMAIL_SEND_FAIL;
		}
		return self::DISCUZ_CLASS_EMAIL_SEND_SUCCESS;
	}

	protected static function log($emailtype, $svctype, $status, $uid, $email, $time, $ip, $port, $content = '') {
		return table_common_emaillog::t()->insert(['emailtype' => $emailtype, 'svctype' => $svctype, 'status' => $status, 'uid' => $uid, 'email' => $email, 'dateline' => $time, 'ip' => $ip, 'port' => $port, 'content' => $content]);
	}

}