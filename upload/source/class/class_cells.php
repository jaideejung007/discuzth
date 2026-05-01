<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class cells {

	public static $r_dzTpl = [
		"/[\n\r\t]*\{eval\}\s*(\<\!\-\-)*(.+?)(\-\-\>)*\s*\{\/eval\}[\n\r\t]*/is",
		"/[\n\r\t]*\{eval\s+(.+?)\s*\}[\n\r\t]*/is",
		"/[\n\r\t]*\{csstemplate\}[\n\r\t]*/is",
		'/\<\?.+?\>/s',
		"/[\n\r\t]*\{template\s+([a-z0-9_:\/]+)\}[\n\r\t]*/is",
		"/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/is",
		"/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/is",
		"/([\n\r\t]*)\{if\s+(.+?)\}([\n\r\t]*)/is",
		"/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/is",
		'/\{else\}/i',
		'/\{\/if\}/i',
		"/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*/is",
		"/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/is",
		'/\{\/loop\}/i',
	];
	private static $r_php = [
		'/<\?/',
		'/\?>/',
	];
	private static $r_js = [
		'/<script(.*?)>/i',
	];

	public static function getCells($dir) {
		static $files = null;
		if($directory = @dir($dir)) {
			while($entry = $directory->read()) {
				if($entry == '.' || $entry == '..') {
					continue;
				}
				$filename = $dir.'/'.$entry;
				if(is_file($filename)) {
					if(fileext($filename) == 'php') {
						$files[$filename] = $filename;
					}
				} else {
					self::getCells($filename);
				}
			}
			$directory->close();
		}
		return $files;
	}

	public static function getClass($file) {
		if(!file_exists($file)) {
			return null;
		}
		$p = strpos($file, 'cells/');
		if($p === false) {
			return null;
		}
		$cellId = substr(substr($file, $p + 6), 0, -4);
		$c = self::className($cellId);
		require_once $file;
		if(!property_exists($c, 'name') || !property_exists($c, 'cellList')) {
			return null;
		}
		return $cellId;
	}

	public static function className($s) {
		return str_replace('/', '_', $s);
	}

	public static function checkRequire($cellId, $template) {
		if(!self::className($cellId)::$requireList) {
			return true;
		}
		foreach(self::className($cellId)::$requireList as $cell) {
			if(!str_contains($template, '{cell '.$cell.'}')) {
				return false;
			}
		}
		return true;
	}

	public static function checkTemplate($template) {
		$template = preg_replace('/\<\!\-\-\{(.+?)\}\-\-\>/s', "{\\1}", $template);
		foreach(self::$r_dzTpl as $r) {
			if(preg_match($r, $template)) {
				return false;
			}
		}
		foreach(self::$r_php as $r) {
			if(preg_match($r, $template)) {
				return false;
			}
		}
		foreach(self::$r_js as $r) {
			if(preg_match($r, $template)) {
				return false;
			}
		}
		return true;
	}

	public static function saveTemplate($id, $cellId, $type, $template) {
		global $_G;
		$_G['setting']['cells'][self::getTplKey($type)][$id][$cellId] = $template;
		$_G['setting']['cells'][self::getUsedKey($type)][$id][$cellId] = self::getUsedSetting($cellId, $template);

		$ids = [];
		foreach(table_common_style::t()->fetch_all_data(true) as $style) {
			$ids[] = $style['styleid'];
		}

		foreach($_G['setting']['cells'] as $mKey => $mVal) {
			foreach($mVal as $id => $data) {
				if(!in_array($id, $ids)) {
					unset($_G['setting']['cells'][$mKey][$id]);
				}
			}
		}

		$settings = [
			'cells' => $_G['setting']['cells'],
		];
		table_common_setting::t()->update_batch($settings);
	}

	public static function getUsedSetting($cellId, $template) {
		if(!self::className($cellId)::$used) {
			return [];
		}

		$value = [];
		foreach(self::className($cellId)::$used as $cell => $key) {
			$value[$key] = str_contains($template, '{cell '.$cell.'}') ? 1 : 0;
		}
		return $value;
	}

	public static function getTplKey($type) {
		return $type ? 'tplM' : 'tpl';
	}

	public static function getUsedKey($type) {
		return $type ? 'usedM' : 'used';
	}

	public static function getUsed($cellId) {
		global $_G;
		if(!empty($_ENV['cells'][$cellId])) {
			return $_ENV['cells'][$cellId];
		}
		$id = $_G['style']['styleid'];
		$usedKey = self::getUsedKey(defined('IN_MOBILE') ? 1 : 0);
		return $_G['setting']['cells'][$usedKey][$id][$cellId];
	}

	public static function getTemplate($cellId, $beginCell = '', $endCell = '') {
		global $_G;

		$type = defined('IN_MOBILE') ? 1 : 0;
		$tplKey = cells::getTplKey($type);
		$styleid = $_G['style']['styleid'];
		if(empty($_G['setting']['cells'][$tplKey][$styleid][$cellId])) {
			return '';
		}
		$template = $_G['setting']['cells'][$tplKey][$styleid][$cellId];
		if(!$beginCell && !$endCell) {
			return $template;
		}
		$beginCell = '{cell '.$beginCell.'}';
		$endCell = '{cell '.$endCell.'}';
		$p = strpos($template, $beginCell);
		if($p === false) {
			return '';
		}
		$template = substr($template, $p + strlen($beginCell));
		$p = strpos($template, $endCell);
		if($p === false) {
			return '';
		}
		return substr($template, 0, $p);
	}

}