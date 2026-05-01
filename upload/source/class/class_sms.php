<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


class sms {

	
	
	const DISCUZ_CLASS_SMS_TYPE_SECCODE = 0;
	const DISCUZ_CLASS_SMS_TYPE_MESSAGE = 1;

	
	
	
	const DISCUZ_CLASS_SMS_SRVTYPE_OTHERSRV = 0;
	const DISCUZ_CLASS_SMS_SRVTYPE_SECCHECK = 1;
	const DISCUZ_CLASS_SMS_SRVTYPE_NEWSLETT = 2;

	
	
	
	
	const DISCUZ_CLASS_SMS_ERROR_NOWNOERR = 0;
	const DISCUZ_CLASS_SMS_ERROR_TIMELESS = -1;
	const DISCUZ_CLASS_SMS_ERROR_NUMLIMIT = -2;
	const DISCUZ_CLASS_SMS_ERROR_MILLIMIT = -3;
	const DISCUZ_CLASS_SMS_ERROR_GLBLIMIT = -4;
	const DISCUZ_CLASS_SMS_ERROR_CTFSMSGW = -5;
	const DISCUZ_CLASS_SMS_ERROR_CTFGWNME = -6;
	const DISCUZ_CLASS_SMS_ERROR_CTFGWCLS = -7;
	const DISCUZ_CLASS_SMS_ERROR_SMSDISAB = -8;
	const DISCUZ_CLASS_SMS_ERROR_SMSGWERR = -9;

	
	
	const DISCUZ_CLASS_SMS_VERIFY_FAIL = 0;
	const DISCUZ_CLASS_SMS_VERIFY_PASS = 1;
	const DISCUZ_CLASS_SMS_VERIFY_INVALID = -1;

	
	
	const DISCUZ_CLASS_SMSGW_GWTYPE_MSG = 0;
	const DISCUZ_CLASS_SMSGW_GWTYPE_TPL = 1;

	
	public static function verify($uid, $svctype, $secmobicc, $secmobile, $seccode, $updateverify = 1) {
		
		$smstimelimit = getglobal('setting/smstimelimit');
		$smstimelimit = $smstimelimit > 0 ? $smstimelimit : 86400;
		$smsverifylimit = getglobal('setting/smsverifylimit');
		$smsverifylimit = $smsverifylimit > 0 ? $smsverifylimit : 5;
		$lastsend = table_common_smslog::t()->get_lastsms_by_uumm($uid, $svctype, $secmobicc, $secmobile);
		$result = self::DISCUZ_CLASS_SMS_VERIFY_FAIL;
		if($seccode == $lastsend['content'] && $lastsend['verify'] < $smsverifylimit && time() - $lastsend['dateline'] < $smstimelimit) {
			$result = self::DISCUZ_CLASS_SMS_VERIFY_PASS;
		}
		if($lastsend['verify'] >= $smsverifylimit) {
			$result = self::DISCUZ_CLASS_SMS_VERIFY_INVALID;
		}
		if($updateverify && $result != self::DISCUZ_CLASS_SMS_VERIFY_INVALID) {
			table_common_smslog::t()->update($lastsend['smslogid'], ['verify' => $lastsend['verify'] + 1]);
		}
		return $result;
	}

	public static function send($uid, $smstype, $svctype, $secmobicc, $secmobile, $content, $force) {
		
		$time = time();
		$ip = getglobal('clientip');
		$port = getglobal('remoteport');

		
		$check = self::check($uid, $secmobicc, $secmobile, $time, $ip, $port, $force);
		if($check < 0) {
			self::log($smstype, $svctype, 0, $check, $uid, $secmobicc, $secmobile, $time, $ip, $port, $content);
			return $check;
		}

		
		$smsgw = self::smsgw($smstype, $secmobicc);
		if($smsgw < 0) {
			self::log($smstype, $svctype, 0, $smsgw, $uid, $secmobicc, $secmobile, $time, $ip, $port, $content);
			return $smsgw;
		}

		
		$output = self::output($smsgw, $uid, $smstype, $svctype, $secmobicc, $secmobile, $content);
		self::log($smstype, $svctype, 0, $output, $uid, $secmobicc, $secmobile, $time, $ip, $port, $content);
		return $output;
	}

	protected static function check($uid, $secmobicc, $secmobile, $time, $ip, $port, $force) {
		
		
		if(!getglobal('setting/smsstatus')) {
			return self::DISCUZ_CLASS_SMS_ERROR_SMSDISAB;
		}

		if(!$force) {
			
			$smstimelimit = getglobal('setting/smstimelimit');
			$smstimelimit = $smstimelimit > 0 ? $smstimelimit : 86400;
			
			$smsnumlimit = getglobal('setting/smsnumlimit');
			$smsnumlimit = $smsnumlimit > 0 ? $smsnumlimit : 5;
			
			$smsinterval = getglobal('setting/smsinterval');
			$smsinterval = $smsinterval > 0 ? $smsinterval : 300;
			
			$smsmillimit = getglobal('setting/smsmillimit');
			$smsmillimit = $smsmillimit > 0 ? $smsmillimit : 20;
			
			$smsglblimit = getglobal('setting/smsglblimit');
			$smsglblimit = $smsglblimit > 0 ? $smsglblimit : 1000;

			
			$ut = table_common_smslog::t()->get_sms_by_ut($uid, $smstimelimit);
			$mmt = table_common_smslog::t()->get_sms_by_mmt($secmobicc, $secmobile, $smstimelimit);
			if($time - $ut[0]['dateline'] < $smsinterval || $time - $mmt[0]['dateline'] < $smsinterval) {
				return self::DISCUZ_CLASS_SMS_ERROR_TIMELESS;
			}
			if(count($ut) > $smsnumlimit || count($mmt) > $smsnumlimit) {
				return self::DISCUZ_CLASS_SMS_ERROR_NUMLIMIT;
			}

			
			$lastmilion = table_common_smslog::t()->count_sms_by_milions_mmt($secmobicc, $secmobile, $smstimelimit);
			if($lastmilion > $smsmillimit) {
				return self::DISCUZ_CLASS_SMS_ERROR_MILLIMIT;
			}

			
			$globalsend = table_common_smslog::t()->count_sms_by_time($smstimelimit);
			if($globalsend > $smsglblimit) {
				return self::DISCUZ_CLASS_SMS_ERROR_GLBLIMIT;
			}
		}

		return self::DISCUZ_CLASS_SMS_ERROR_NOWNOERR;
	}

	protected static function smsgw($smstype, $secmobicc) {
		$smsgwlist = table_common_smsgw::t()->fetch_all_gw_avaliable();
		foreach($smsgwlist as $key => $value) {
			if(in_array($secmobicc, explode(',', $value['sendrule']))) {
				if($smstype == self::DISCUZ_CLASS_SMS_TYPE_MESSAGE && $value['type'] == self::DISCUZ_CLASS_SMSGW_GWTYPE_TPL) {
					continue;
				}
				$smsgw = $value;
			}
		}

		return $smsgw ?? self::DISCUZ_CLASS_SMS_ERROR_CTFSMSGW;

	}

	protected static function output($smsgw, $uid, $smstype, $svctype, $secmobicc, $secmobile, $content) {
		global $_G;
		$efile = explode(':', $smsgw['class']);
		if(is_array($efile) && count($efile) > 1) {
			$smsgwfile = in_array($efile[0], $_G['setting']['plugins']['available']) ? DISCUZ_PLUGIN($efile[0]).'/smsgw/smsgw_'.$efile[1].'.php' : '';
		} else {
			$smsgwfile = DISCUZ_ROOT.'./source/class/smsgw/smsgw_'.$smsgw['class'].'.php';
		}

		if($smsgwfile) {
			include($smsgwfile);
			$classname = 'smsgw_'.((is_array($efile) && count($efile) > 1) ? $efile[1] : $smsgw['class']);
			if(class_exists($classname)) {
				$class = new $classname();
				$class->parameters = dunserialize($smsgw['parameters']);
				$result = $class->send($uid, $smstype, $svctype, $secmobicc, $secmobile, ['content' => $content]);
			} else {
				$result = self::DISCUZ_CLASS_SMS_ERROR_CTFGWCLS;
			}
		} else {
			$result = self::DISCUZ_CLASS_SMS_ERROR_CTFGWNME;
		}

		if($result < 0 && ($result == self::DISCUZ_CLASS_SMS_ERROR_CTFGWCLS || $result == self::DISCUZ_CLASS_SMS_ERROR_CTFGWNME)) {
			$data = ['available' => '0'];
			table_common_smsgw::t()->update($smsgw['smsgwid'], $data);
		}

		return $result;
	}

	protected static function log($smstype, $svctype, $smsgw, $status, $uid, $secmobicc, $secmobile, $time, $ip, $port, $content = '') {
		return table_common_smslog::t()->insert(['smstype' => $smstype, 'svctype' => $svctype, 'smsgw' => $smsgw, 'status' => $status, 'uid' => $uid, 'secmobicc' => $secmobicc, 'secmobile' => $secmobile, 'dateline' => $time, 'ip' => $ip, 'port' => $port, 'content' => $content]);
	}

}