<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace admin;

class widget_setting {

	public static function get() {
		global $_G;

		return !empty($_G['cache']['admin']['widget']) ? $_G['cache']['admin']['widget'] : [];
	}

	public static function reset() {
		global $_G;

		unset($_G['cache']['admin']['widget']);
		savecache('admin', $_G['cache']['admin']);
	}

	public static function set() {
		global $_G;

		$data = $_GET['pos'];
		$hide = $_GET['hide'];

		$settingData = $saveData = [];
		foreach($data as $type => $widgets) {
			foreach($widgets as $widget) {
				$settingData[$type][] = $widget;
				if(!empty($hide[$type]) && in_array($widget, $hide[$type])) {
					continue;
				}
				$saveData[$type][] = $widget;
			}
		}

		$_G['cache']['admin']['widget'] = [
			'data' => ['data' => $settingData, 'hide' => $hide],
			'setting' => $saveData,
		];
		savecache('admin', $_G['cache']['admin']);
	}


}

