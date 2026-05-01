<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

loadwitframe();

class witframe_plugin {

	public static function pluginList() {
		global $_G;

		if(!empty($_G['config']['witframe']['sdkurl'])) {
			$data = dfsockopen($_G['config']['witframe']['sdkurl'].'/index.php/system/page/index?from=discuz');
			$data = json_decode($data, true);
			if(!$data || !is_array($data)) {
				return [];
			}
			return $data['data'];
		}

		$conf = Lib\Core::GetSetting();
		if(!$conf) {
			return [];
		}

		return Lib\Site::Discuz_PluginList($conf['witPid']);
	}

	public static function getApis($witPlugins = null) {
		$apis = [];
		foreach(!$witPlugins ? self::pluginList() : $witPlugins as $plugin) {
			$url = self::_getConfigUrl($plugin);
			if(!$url) {
				continue;
			}
			$data = dfsockopen($url);
			$data = json_decode($data, true);
			if(!$data || !is_array($data)) {
				continue;
			}
			$plugin['config'] = $data['data'];
			$apis[$plugin['path']] = $plugin;
		}
		return $apis;
	}

	public static function getSettingValue($apis = null) {
		$setting = [];
		foreach(!$apis ? self::getApis() : $apis as $row) {
			foreach($row['config'] as $config) {
				$setting[$config['type']][$row['path'].'/'.$config['page']] = $config;
			}
		}
		foreach($setting as $key => $row) {
			ksort($setting[$key]);
		}
		ksort($setting);
		return $setting;
	}

	private static function _getWitPluginURL() {
		global $_G;

		if(!empty($_G['config']['witframe']['sdkurl'])) {
			return $_G['config']['witframe']['sdkurl'].'/index.php/plugin';
		}
		return Lib\Core::WitPluginURL;
	}

	private static function _getConfigUrl($plugin) {
		$conf = Lib\Core::GetSetting();
		if(!$conf) {
			return '';
		}
		return self::_getWitPluginURL().'/'.$plugin['path'].'/discuz_config?pid='.$conf['witPid'].'&_authSign='.restfulAuthSign();
	}

	public static function getApiUrl($type, $path, $setTitle = false) {
		$conf = Lib\Core::GetSetting();
		if(!$conf) {
			return '';
		}

		global $_G;

		if(!isset($_G['setting']['witframe_plugins'][$type][$path])) {
			return '';
		}

		if($setTitle) {
			$GLOBALS['navtitle'] = $_G['setting']['witframe_plugins'][$type][$path]['name'];
		}

		return self::_getWitPluginURL().'/'.$path.'?pid='.$conf['witPid'].'&_authSign='.restfulAuthSign();
	}

	public static function getApiByType($type) {
		$conf = Lib\Core::GetSetting();
		if(!$conf) {
			return '';
		}

		global $_G;

		return !empty($_G['setting']['witframe_plugins'][$type]) ? $_G['setting']['witframe_plugins'][$type] : [];
	}

}