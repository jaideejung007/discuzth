<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class helper_seccheck {

	const RESTFUL_QAA = ['calc', 'calcmad'];

	private static function _check($type) {
		global $_G;
		$secappend = '';
		if(!defined('IN_MOBILE')) {
			if(isset($_GET['idhash']) && $_GET['idhash']) {
				$secappend = $_GET['idhash'];
			} elseif($type == 'code') {
				if(isset($_GET['seccodehash']) && $_GET['seccodehash']) {
					$secappend = $_GET['seccodehash'];
				}
			} elseif($type == 'qaa') {
				if(isset($_GET['secqaahash']) && $_GET['secqaahash']) {
					$secappend = $_GET['secqaahash'];
				}
			}
		}
		if(!isset($_G['cookie']['sec'.$type.$secappend])) {
			return false;
		}
		list($ssid, $sign) = explode('.', $_G['cookie']['sec'.$type.$secappend]);
		if($sign != substr(md5($ssid.$_G['uid'].$_G['authkey']), 8, 18)) {
			return false;
		}
		$seccheck = table_common_seccheck::t()->fetch($ssid);
		if(!$seccheck) {
			return false;
		}
		if(TIMESTAMP - $seccheck['dateline'] > 600 || $seccheck['verified'] > 4) {
			table_common_seccheck::t()->delete_expiration($ssid);
			return false;
		}
		return $seccheck;
	}

	private static function _create($type, $code = '') {
		global $_G;
		$secappend = '';
		if(!defined('IN_MOBILE')) {
			if(isset($_GET['idhash']) && $_GET['idhash']) {
				$secappend = $_GET['idhash'];
			} elseif($type == 'code') {
				if(isset($_GET['seccodehash']) && $_GET['seccodehash']) {
					$secappend = $_GET['seccodehash'];
				}
			} elseif($type == 'qaa') {
				if(isset($_GET['secqaahash']) && $_GET['secqaahash']) {
					$secappend = $_GET['secqaahash'];
				}
			}
		}
		$ssid = table_common_seccheck::t()->insert([
			'dateline' => TIMESTAMP,
			'code' => $code,
			'succeed' => 0,
			'verified' => 0,
		], true);
		dsetcookie('sec'.$type.$secappend, $ssid.'.'.substr(md5($ssid.$_G['uid'].$_G['authkey']), 8, 18));
	}

	public static function make_seccode($seccode = '') {
		global $_G;
		if(!$seccode) {
			$seccode = random(6, 1);
			$seccodeunits = '';
			if($_G['setting']['seccodedata']['type'] == 1) {
				$lang = lang('seccode');
				$len = strtoupper(CHARSET) == 'GBK' ? 2 : 3;
				$code = [substr($seccode, 0, 3), substr($seccode, 3, 3)];
				$seccode = '';
				for($i = 0; $i < 2; $i++) {
					$seccode .= substr($lang['chn'], $code[$i] * $len, $len);
				}
			} elseif($_G['setting']['seccodedata']['type'] == 3) {
				$s = sprintf('%04s', base_convert($seccode, 10, 20));
				$seccodeunits = 'CEFHKLMNOPQRSTUVWXYZ';
			} else {
				$s = sprintf('%04s', base_convert($seccode, 10, 24));
				$seccodeunits = 'BCEFGHJKMPQRTVWXY2346789';
			}
			if($seccodeunits) {
				$seccode = '';
				for($i = 0; $i < 4; $i++) {
					$unit = ord($s[$i]);
					$seccode .= ($unit >= 0x30 && $unit <= 0x39) ? $seccodeunits[$unit - 0x30] : $seccodeunits[$unit - 0x57];
				}
			}
		}
		self::_create('code', $seccode);
		return $seccode;
	}

	public static function make_secqaa() {
		global $_G;
		loadcache('secqaa');
		$secqaakey = max(1, random(1, 1));
		if($_G['cache']['secqaa'][$secqaakey]['type']) {
			if(defined('IN_RESTFUL')) {
				$_G['cache']['secqaa'][$secqaakey]['question'] = self::RESTFUL_QAA[array_rand(self::RESTFUL_QAA)];
			}
			$etype = explode(':', $_G['cache']['secqaa'][$secqaakey]['question']);
			if(count($etype) > 1) {
				if(!preg_match('/^\w+$/', $etype[0]) || !preg_match('/^\w+$/', $etype[1])) {
					return;
				}
				$qaafile = DISCUZ_PLUGIN($etype[0]).'/secqaa/secqaa_'.$etype[1].'.php';
				$class = $etype[1];
			} else {
				if(!preg_match('/^\w+$/', $_G['cache']['secqaa'][$secqaakey]['question'])) {
					return;
				}
				$qaafile = libfile('secqaa/'.$_G['cache']['secqaa'][$secqaakey]['question'], 'class');
				$class = $_G['cache']['secqaa'][$secqaakey]['question'];
			}
			if(file_exists($qaafile)) {
				@include_once $qaafile;
				$class = 'secqaa_'.$class;
				if(class_exists($class)) {
					$qaa = new $class();
					if(method_exists($qaa, 'make')) {
						$_G['cache']['secqaa'][$secqaakey]['answer'] = md5($qaa->make($_G['cache']['secqaa'][$secqaakey]['question']));
					} elseif(method_exists($qaa, 'create')) {
						$answer = $qaa->create($_G['cache']['secqaa'][$secqaakey]['question']);
						$code = random(5);
						memory('set', 'secqaa_'.$code, [
							'answer' => $answer,
							'class' => $class,
							'file' => $qaafile,
						], 300);
						$_G['cache']['secqaa'][$secqaakey]['answer'] = $code;
					}
				}
			}
		}
		self::_create('qaa', substr($_G['cache']['secqaa'][$secqaakey]['answer'], 0, 6));
		return $_G['cache']['secqaa'][$secqaakey]['question'];
	}

	public static function check_seccode($value, $idhash, $fromjs = 0, $modid = '', $verifyonly = false) {
		global $_G;
		if(!$_G['setting']['seccodestatus']) {
			return true;
		}
		$seccheck = self::_check('code');
		if(!$seccheck) {
			return false;
		}
		$ssid = $seccheck['ssid'];
		if(!is_numeric($_G['setting']['seccodedata']['type'])) {
			$etype = explode(':', $_G['setting']['seccodedata']['type']);
			if(count($etype) > 1) {
				if(!preg_match('/^\w+$/', $etype[0]) || !preg_match('/^\w+$/', $etype[1])) {
					return false;
				}
				$codefile = DISCUZ_PLUGIN($etype[0]).'/seccode/seccode_'.$etype[1].'.php';
				$class = $etype[1];
			} else {
				if(!preg_match('/^\w+$/', $_G['setting']['seccodedata']['type'])) {
					return false;
				}
				$codefile = libfile('seccode/'.$_G['setting']['seccodedata']['type'], 'class');
				$class = $_G['setting']['seccodedata']['type'];
			}
			if(file_exists($codefile)) {
				@include_once $codefile;
				$class = 'seccode_'.$class;
				if(class_exists($class)) {
					$code = new $class();
					if(method_exists($code, 'check')) {
						$return = $code->check($value, $idhash, $seccheck, $fromjs, $modid, $verifyonly);
					}
				}
			} else {
				$return = false;
			}
		} else {
			$return = $seccheck['code'] == strtoupper($value);
		}
		if($return) {
			table_common_seccheck::t()->update_succeed($ssid);
		} else {
			table_common_seccheck::t()->update_verified($ssid);
		}
		if(!$verifyonly) {
			table_common_seccheck::t()->delete($ssid);
		}
		return $return;
	}

	public static function check_secqaa($value, $idhash, $verifyonly = false) {
		global $_G;
		if(!$_G['setting']['secqaa']) {
			return true;
		}
		$seccheck = self::_check('qaa');
		if(!$seccheck) {
			return false;
		}
		$ssid = $seccheck['ssid'];
		if(strlen($seccheck['code']) == 5) {
			$v = memory('get', 'secqaa_'.$seccheck['code']);
			if(!$v) {
				return false;
			}
			if(!file_exists($v['file'])) {
				return false;
			}
			require_once $v['file'];
			if(!class_exists($v['class']) || !method_exists($v['class'], 'check')) {
				return false;
			}
			$qaa = new $v['class'];
			$return = $qaa->check($value, $v['answer']);
		} else {
			$return = $seccheck['code'] == substr(md5($value), 0, 6);
		}
		if($return) {
			table_common_seccheck::t()->update_succeed($ssid);
		} else {
			table_common_seccheck::t()->update_verified($ssid);
		}
		if(!$verifyonly) {
			table_common_seccheck::t()->delete($ssid);
		}
		return $return;
	}

	public static function rule_register() {
		global $_G;
		$status = self::checkStatus('register');
		$seccheckrule = &$_G['setting']['seccodedata']['rule']['register'];
		$rule = false;
		if($seccheckrule['allow'] == 2) {
			if($seccheckrule['numlimit'] > 0) {
				loadcache('seccodedata', true);
				if($_G['cache']['seccodedata']['register']['show']) {
					$rule = true;
				} else {
					$regnumber = table_common_member::t()->count_by_regdate(TIMESTAMP - $seccheckrule['timelimit']);
					if($regnumber >= $seccheckrule['numlimit']) {
						$rule = true;
						$_G['cache']['seccodedata']['register']['show'] = 1;
						savecache('seccodedata', $_G['cache']['seccodedata']);
					}
				}
			}
		} else {
			$rule = $status;
		}
		return $status && $rule;
	}

	public static function rule_login() {
		global $_G;
		$status = self::checkStatus('login');
		$seccheckrule = &$_G['setting']['seccodedata']['rule']['login'];
		$rule = false;
		if($seccheckrule['allow'] == 2) {
			$rule = false;
		} else {
			$rule = $status;
		}
		return $status && $rule;
	}

	public static function rule_post($action) {
		global $_G;
		$status = self::checkStatus('post');
		$seccheckrule = &$_G['setting']['seccodedata']['rule']['post'];
		$rule = false;
		if($seccheckrule['allow'] == 2) {
			if(table_common_member_secwhite::t()->check($_G['uid'])) {
				$rule = false;
			} else {
				$rule = getuserprofile('posts') < $_G['setting']['seccodedata']['minposts'];
				if(!$rule && $seccheckrule['numlimit']) {
					$count = table_forum_post::t()->count_by_search('pid:0', null, null, null, null, $_G['uid'], null, TIMESTAMP - $seccheckrule['timelimit']);
					$rule = $seccheckrule['numlimit'] <= $count;
				}
				if($action == 'newthread' && !$rule && !empty($_POST) && $seccheckrule['nplimit']) {
					if(!$_G['cookie']['st_t']) {
						$rule = true;
					} else {
						list($uid, $t, $hash) = explode('|', $_G['cookie']['st_t']);
						list($t, $m) = explode(',', $t);
						if(md5($uid.'|'.$t.$_G['config']['security']['authkey']) == $hash && !$m) {
							if(TIMESTAMP - $t <= $seccheckrule['nplimit']) {
								$rule = true;
							} else {
								$rule = false;
							}
						} else {
							$rule = true;
						}
					}
				}
				if($action == 'reply' && !$rule && !empty($_POST) && $seccheckrule['vplimit']) {
					if(!$_G['cookie']['st_p']) {
						$rule = true;
					} else {
						list($uid, $t, $hash) = explode('|', $_G['cookie']['st_p']);
						list($t, $m) = explode(',', $t);
						if(md5($uid.'|'.$t.$_G['config']['security']['authkey']) == $hash && !$m) {
							if(TIMESTAMP - $t <= $seccheckrule['vplimit']) {
								$rule = true;
							} else {
								$rule = false;
							}
						} else {
							$rule = true;
						}
					}
				}
			}
		} else {
			$rule = $status;
		}
		return $status && $rule;
	}

	public static function rule_publish($rule) {
		global $_G;
		return self::checkStatus('post');
	}

	public static function rule_card() {
		global $_G;
		return self::checkStatus('card');
	}

	public static function seccheck($rule, $param = []) {
		global $_G;
		if($_G['uid'] && !checkperm('seccode')) {
			return [];
		}
		loadcache('secqaa');
		if(!self::_checkMinPosts()) {
			return [];
		}
		if(str_contains($rule, ':')) {
			if(!self::checkStatus($rule)) {
				return [];
			}
			list($pluginid, $name) = explode(':', $rule);
			if(preg_match('/^\w+$/i', $name)) {
				$f = DISCUZ_PLUGIN($pluginid).'/seccheck/seccheck_'.$name.'.php';
				if(file_exists($f)) {
					require_once $f;
					if(class_exists($c = 'seccheck_'.$name)) {
						$seccheck = new $c();
						if(method_exists($seccheck, 'rule')) {
							return self::_return($seccheck->rule($param));
						}
					}
				}
			}
		} elseif(method_exists('helper_seccheck', 'rule_'.$rule)) {
			return self::_return(call_user_func(['helper_seccheck', 'rule_'.$rule], $param));
		}
		return [];
	}

	public static function checkStatus($rule) {
		global $_G;
		return !empty($_G['setting']['secqaa']['statuses']) && in_array($rule, $_G['setting']['secqaa']['statuses']);
	}

	private static function _checkMinPosts() {
		global $_G;
		return !$_G['setting']['secqaa']['minposts'] || getuserprofile('posts') < $_G['setting']['secqaa']['minposts'];
	}

	private static function _return($v) {
		if(!$v) {
			return [];
		}
		global $_G;
		return [
			$v && !empty($_G['setting']['secqaa']['allowcode']),
			$v && !empty($_G['cache']['secqaa'])
		];
	}

}

