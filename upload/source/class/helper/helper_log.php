<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class helper_log {

	public static function runlog($file, $message, $halt = 0) {
		global $_G;

		$nowurl = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : ($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
		if($_G['setting']['log'][$file]) {
			$errorlog = [
				'timestamp' => TIMESTAMP,
				'clientip' => $_G['clientip'],
				'uid' => $_G['uid'],
				'nowurl' => $nowurl,
				'message' => str_replace(["\r", "\n"], [' ', ' '], trim($message)),
			];
			$member_log = $_G['member'];
			logger($file, $member_log, $_G['member']['uid'], $errorlog);
		}
		if($halt) {
			exit();
		}
	}


	public static function writelog($file, $log) {
		global $_G;
		$yearmonth = dgmdate(TIMESTAMP, 'Ym', $_G['setting']['timeoffset']);
		$logdir = DISCUZ_DATA.'./log/';
		if(!is_dir($logdir)) {
			dmkdir($logdir);
		}
		$logfile = $logdir.$yearmonth.'_'.$file.'.php';
		if(@filesize($logfile) > 2048000) {
			$dir = opendir($logdir);
			$length = strlen($file);
			$maxid = $id = 0;
			while($entry = readdir($dir)) {
				if(str_contains($entry, $yearmonth.'_'.$file)) {
					$id = intval(substr($entry, $length + 8, -4));
					$id > $maxid && $maxid = $id;
				}
			}
			closedir($dir);

			$logfilebak = $logdir.$yearmonth.'_'.$file.'_'.($maxid + 1).'.php';
			@rename($logfile, $logfilebak);
		}
		$fp = fopen($logfile, 'a');
		if($fp) {
			if(!is_array($log)) {
				$log = [$log];
			}
			foreach($log as $tmp) {
				fwrite($fp, "<?PHP exit;?>\t".str_replace(['<?', '?>'], '', $tmp)."\n");
			}
			fflush($fp);
			fclose($fp);
		} else {
			fclose($fp);
		}
	}


	public static function useractionlog($uid, $action) {
		$uid = intval($uid);
		if(empty($uid) || empty($action)) {
			return false;
		}
		$action = getuseraction($action);
		table_common_member_action_log::t()->insert(['uid' => $uid, 'action' => $action, 'dateline' => TIMESTAMP]);
		return true;
	}

	public static function getuseraction($var) {
		$value = false;
		$ops = ['tid', 'pid', 'blogid', 'picid', 'doid', 'sid', 'aid', 'uid_cid', 'blogid_cid', 'sid_cid', 'picid_cid', 'aid_cid', 'topicid_cid', 'pmid'];
		if(is_numeric($var)) {
			$value = $ops[$var] ?? false;
		} else {
			$value = array_search($var, $ops);
		}
		return $value;
	}

}

