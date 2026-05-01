<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class tplfile {

	public static function getphptemplate($content) {
		if(strtolower(substr($content, 0, 5)) == '<?php') {
			$pos = strpos($content, "\n");
			return $pos !== false ? substr($content, $pos + 1) : $content;
		} else {
			return $content;
		}
	}

	public static function file_exists($file, $nocache = false) {
		static $cached = [];
		if(isset($cached[$file]) && !$nocache) {
			return $cached[$file];
		}

		return $cached[$file] = file_exists($file) ? 1 : 0;
	}

	public static function file_get_contents($file) {
		return self::getphptemplate(@implode('', file($file)));
	}

	public static function filemtime($file) {
		if(file_exists($file)) {
			return filemtime($file);
		}
		$ext = fileext($file) == 'htm' ? 'php' : 'htm';
		$file = substr($file, 0, -4).'.'.$ext;
		return @filemtime($file);
	}

}