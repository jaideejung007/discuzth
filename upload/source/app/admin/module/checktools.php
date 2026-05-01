<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

if(!isfounder()) cpmsg('noaccess_isfounder', '', 'error');

$file = childfile('checktools/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;

function pvsort($key, $v, $s) {
	$r = '/';
	$p = '';
	foreach($key as $k) {
		$r .= $p.preg_quote($k);
		$p = '|';
	}
	$r .= '/';
	preg_match_all($r, $v, $a);
	$a = $a[0];
	$a = array_flip($a);
	foreach($a as $key => $value) {
		$s = str_replace($key, '$'.($value + 1), $s);
	}
	return $s;
}

function pvadd($s, $t = []) {
	$s = str_replace(['$3', '$2', '$1'], ['~4', '~3', '~2'], $s);
	if(!$t) {
		return str_replace(['~4', '~3', '~2'], ['$4', '$3', '$2'], $s);
	} else {
		return str_replace(['~4', '~3', '~2'], [$t[0].'4'.$t[1], $t[0].'3'.$t[1], $t[0].'2'.$t[1]], $s);
	}

}

function checkmailerror($type, $error) {
	global $alertmsg;
	$alertmsg .= !$alertmsg ? $error : '';
}

function getremotefile($file) {
	global $_G;
	@set_time_limit(0);
	$file = $file.'?'.TIMESTAMP.rand(1000, 9999);
	if(str_starts_with($file, 'ftp://')) {
		$str = file_get_contents($file);
	} else {
		$str = dfsockopen($file);
		if(empty($str)) {
			require_once DISCUZ_ROOT.'./source/class/class_oss.php';
			$str = oss_base::check_file($file);
		}
	}
	return $str;
}

function checkhook($currentdir, $ext = '', $sub = 1, $skip = '') {
	global $hooks, $hookdata;
	$dir = opendir($currentdir);
	$exts = '/('.$ext.')$/i';
	$skips = explode(',', $skip);

	while($entry = readdir($dir)) {
		$file = $currentdir.$entry;
		if($entry != '.' && $entry != '..' && (preg_match($exts, $entry) || $sub && is_dir($file)) && !in_array($entry, $skips)) {
			if($sub && is_dir($file)) {
				checkhook($file.'/', $ext, $sub, $skip);
			} else {
				$data = file_get_contents($file);
				$hooks = [];
				preg_replace_callback('/\{hook\/(\w+?)(\s+(.+?))?\}/i', 'checkhook_callback_findhook_13', $data);
				if($hooks) {
					foreach($hooks as $v) {
						$hookdata[$file][$v][] = $v;
					}
				}
			}
		}
	}
}

function checkhook_callback_findhook_13($matches) {
	findhook($matches[1], $matches[3]);
	return '';
}

function findhook($hookid, $key) {
	global $hooks;
	if($key) {
		$key = ' '.$key;
	}
	$hooks[] = '<!--{hook/'.$hookid.$key.'}-->';
}

function generate_key($length = 32) {
	$random = secrandom($length);
	$info = md5($_SERVER['SERVER_SOFTWARE'].$_SERVER['SERVER_NAME'].$_SERVER['SERVER_ADDR'].$_SERVER['SERVER_PORT'].$_SERVER['HTTP_USER_AGENT'].time());
	$return = '';
	for($i = 0; $i < $length; $i++) {
		$return .= $random[$i].$info[$i];
	}
	return $return;
}