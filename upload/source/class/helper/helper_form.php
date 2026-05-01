<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class helper_form {


	public static function submitcheck($var, $allowget = 0, $seccodecheck = 0, $secqaacheck = 0) {
		if(!getgpc($var)) {
			return FALSE;
		} else {
			global $_G;
			$formhashChecked = !empty($_GET['formhash']) && $_GET['formhash'] == formhash();
			
			if(defined('IN_RESTFUL') && $formhashChecked ||
				($allowget && ($allowget !== 2 || $formhashChecked)) ||
				($_SERVER['REQUEST_METHOD'] == 'POST' && $formhashChecked && empty($_SERVER['HTTP_X_FLASH_VERSION']) &&
					(empty($_SERVER['HTTP_REFERER']) ||
						preg_replace('/https?:\/\/([^\:\/]+).*/i', "\\1", $_SERVER['HTTP_REFERER']) == preg_replace('/([^\:]+).*/', "\\1", $_SERVER['HTTP_HOST'])))) {
				if(checkperm('seccode') && !defined('DISABLE_SECCHECK')) {
					if($secqaacheck && !check_secqaa($_GET['secanswer'], $_GET['secqaahash'])) {
						showmessage('submit_secqaa_invalid');
					}
					if($seccodecheck && !check_seccode($_GET['seccodeverify'], $_GET['seccodehash'], 0, $_GET['seccodemodid'])) {
						showmessage('submit_seccode_invalid');
					}
				}
				if(defined('IN_ADMINCP')) {
					serializecomponent();
				}
				return TRUE;
			} else {
				showmessage('submit_invalid');
			}
		}
	}

	public static function censor($message, $modword = NULL, $return = FALSE, $modasban = TRUE) {
		global $_G;
		$censor = discuz_censor::instance();
		$censor->check($message, $modword);
		
		
		if(($censor->modbanned() && empty($_G['group']['ignorecensor'])) || (($modasban && !empty($_G['setting']['modasban'])) && $censor->modmoderated() && empty($_G['group']['ignorecensor']))) {
			$wordbanned = implode(', ', $censor->words_found);
			if($return) {
				return ['message' => lang('message', 'word_banned', ['wordbanned' => $wordbanned])];
			}
			if(!defined('IN_ADMINCP')) {
				showmessage('word_banned', '', ['wordbanned' => $wordbanned]);
			} else {
				cpmsg(lang('message', 'word_banned'), '', 'error', ['wordbanned' => $wordbanned]);
			}
		}
		if($_G['group']['allowposturl'] == 0) {
			$urllist = self::get_url_list($message);
			if(is_array($urllist[1])) {
				foreach($urllist[1] as $key => $val) {
					if(!$val = trim($val)) continue;
					if(!iswhitelist($val)) {
						if($return) {
							return ['message' => 'post_url_nopermission'];
						}
						showmessage('post_url_nopermission');
					}
				}
			}
		} elseif($_G['group']['allowposturl'] == 2) {
			$message = preg_replace("/\[url(=((https?|ftp|gopher|news|telnet|rtsp|mms|callto|bctp|thunder|qqdl|synacast){1}:\/\/|www\.|mailto:|tel:|magnet:)?([^\r\n\[\"']+?))?\](.+?)\[\/url\]/is", '\\5', $message);
		}
		return $message;
	}

	public static function censormod($message) {
		global $_G;
		if($_G['group']['ignorecensor']) {
			return false;
		}
		$modposturl = false;
		if($_G['group']['allowposturl'] == 1) {
			$urllist = self::get_url_list($message);
			if(is_array($urllist[1])) foreach($urllist[1] as $key => $val) {
				if(!$val = trim($val)) continue;
				if(!iswhitelist($val)) {
					$modposturl = true;
				}
			}
		}
		if($modposturl) {
			return true;
		}

		$censor = discuz_censor::instance();
		$censor->check($message);
		return $censor->modmoderated();
	}

	public static function get_url_list($message) {
		$return = [];

		(strpos($message, '[/img]') || strpos($message, '[/flash]')) && $message = preg_replace("/\[img[^\]]*\]\s*([^\[\<\r\n]+?)\s*\[\/img\]|\[flash[^\]]*\]\s*([^\[\<\r\n]+?)\s*\[\/flash\]/is", '', $message);
		if(preg_match_all("/((https?|ftp|gopher|news|telnet|rtsp|mms|callto|bctp|thunder|qqdl|synacast){1}:\/\/|www\.)[^ \[\]\"']+/i", $message, $urllist)) {
			foreach($urllist[0] as $key => $val) {
				$val = trim($val);
				$return[0][$key] = $val;
				if(!preg_match('/^https?:\/\//is', $val)) $val = 'http://'.$val;
				$tmp = parse_url($val);
				$return[1][$key] = $tmp['host'];
				if($tmp['port']) {
					$return[1][$key] .= ":{$tmp['port']}";
				}
			}
		}
		return $return;
	}

	public static function updatemoderate($idtype, $ids, $status = 0) {
		$ids = is_array($ids) ? $ids : [$ids];
		if(!$ids) {
			return;
		}
		if(!$status) {
			foreach($ids as $id) {
				table_common_moderate::t()->insert_moderate($idtype, [
					'id' => $id,
					'status' => 0,
					'dateline' => TIMESTAMP,
				], false, true);
			}
		} elseif($status == 1) {
			table_common_moderate::t()->update_moderate($ids, $idtype, ['status' => 1]);
		} elseif($status == 2) {
			table_common_moderate::t()->delete_moderate($ids, $idtype);
		}
	}
}

