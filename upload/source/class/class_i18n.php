<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

class i18n {

	const defaultPath = DISCUZ_ROOT.'./source/i18n/'.DISCUZ_LANG.'/';

	public static function getLang($file, $i18n = '') {
		global $_G;

		static $loaded = [];

		if(empty($i18n) && isset($loaded[$file])) {
			return $loaded[$file];
		}

		$return = self::defaultPath.$file;

		$i18n = !empty($i18n) ? $i18n : $_G['i18n'];

		$lang = [];

		if($i18n && !empty($_G['setting']['i18n'][$i18n])) {
			if(isset($_G['setting']['i18n_custom'][$i18n])) {
				$customSource = $_G['setting']['i18n_custom'][$i18n] ?? 'default';
				loadcache('lang');
				if(!empty($_G['cache']['lang'][$_G['setting']['i18n'][$i18n]][$file])) {
					return $loaded[$file] = $_G['cache']['lang'][$_G['setting']['i18n'][$i18n]][$file];
				} elseif(is_dir($path = $_G['setting']['i18n'][$customSource].'/')) {
					if(file_exists($path.$file)) {
						require $path.$file;
					}
					if(!empty($lang)) {
						return $loaded[$file] = $lang;
					}
				}
			} elseif(is_dir($path = $_G['setting']['i18n'][$i18n].'/')) {
				if(file_exists($path.$file)) {
					require $path.$file;
				}
				if(!empty($lang)) {
					return $loaded[$file] = $lang;
				}
			}
		}

		if(file_exists($return)) {
			require $return;
		}
		return $loaded[$file] = $lang;
	}

	public static function cmd($cmd, $langkey = '', $path = '') {
		global $_G;

		$i18n = !empty($_G['setting']['i18n']) ? $_G['setting']['i18n'] : [];

		switch($cmd) {
			case 'get':
				return !empty($langkey) ? $i18n[$langkey] : $i18n;
			case 'set':
				$i18n[$langkey] = $path;
				break;
			case 'rm':
				unset($i18n[$langkey]);
				break;
		}

		table_common_setting::t()->update_batch(['i18n' => $i18n]);
		require_once libfile('function/cache');
		updatecache('setting');

		$_G['setting']['i18n'] = $i18n;
		return '';
	}

}