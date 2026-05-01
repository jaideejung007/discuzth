<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function updatecache($cachename = '') {

	$updatelist = empty($cachename) ? [] : (is_array($cachename) ? $cachename : [$cachename]);

	if(!$updatelist) {
		@include_once libfile('cache/setting', 'function');
		build_cache_setting();
		$cachedir = DISCUZ_ROOT.'./source/function/cache';
		$cachedirhandle = dir($cachedir);
		while($entry = $cachedirhandle->read()) {
			if(!in_array($entry, ['.', '..']) && preg_match('/^cache\_([\_\w]+)\.php$/', $entry, $entryr) && $entryr[1] != 'setting' && str_ends_with($entry, '.php') && is_file($cachedir.'/'.$entry)) {
				@include_once libfile('cache/'.$entryr[1], 'function');
				call_user_func('build_cache_'.$entryr[1]);
			}
		}
		foreach(table_common_plugin::t()->fetch_all_data(1) as $plugin) {
			$dir = substr($plugin['directory'], 0, -1);
			$cachedir = DISCUZ_PLUGIN($dir).'/cache';
			if(file_exists($cachedir)) {
				$cachedirhandle = dir($cachedir);
				while($entry = $cachedirhandle->read()) {
					if(!in_array($entry, ['.', '..']) && preg_match('/^cache\_([\_\w]+)\.php$/', $entry, $entryr) && str_ends_with($entry, '.php') && is_file($cachedir.'/'.$entry)) {
						@include_once libfile('cache/'.$entryr[1], 'plugin/'.$dir);
						call_user_func('build_cache_plugin_'.$entryr[1]);
					}
				}
			}
		}
	} else {
		foreach($updatelist as $entry) {
			$entrys = explode(':', $entry);
			if(count($entrys) == 1) {
				@include_once libfile('cache/'.$entry, 'function');
				call_user_func('build_cache_'.$entry);
			} else {
				@include_once libfile('cache/'.$entrys[1], 'plugin/'.$entrys[0]);
				call_user_func('build_cache_plugin_'.$entrys[1]);
			}
		}
	}

}

function writetocache($script, $cachedata, $prefix = 'cache_') {
	global $_G;

	$dir = DISCUZ_DATA.'./sysdata/';
	if(!is_dir($dir)) {
		dmkdir($dir, 0777);
	}

	$s = "<?php\n//Discuz! cache file, DO NOT modify me!\n//Identify: ".md5($prefix.$script.'.php'.$cachedata.$_G['config']['security']['authkey'])."\n\n$cachedata?>";

	$fp = fopen("$dir$prefix$script.php", 'cb');
	if(!($fp && flock($fp, LOCK_EX) && ftruncate($fp, 0) && fwrite($fp, $s) && fflush($fp) && flock($fp, LOCK_UN) && fclose($fp))) {
		flock($fp, LOCK_UN);
		fclose($fp);
		unlink("$dir$prefix$script.php");
		exit('Can not write to cache files, please check directory ./data/ and ./data/sysdata/ .');
	}
}


function getcachevars($data, $type = 'VAR') {
	$evaluate = '';
	foreach($data as $key => $val) {
		if(!preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/", $key)) {
			continue;
		}
		if(is_array($val)) {
			$evaluate .= "\$$key = ".arrayeval($val).";\n";
		} else {
			$val = addcslashes($val, '\'\\');
			$evaluate .= $type == 'VAR' ? "\$$key = '$val';\n" : "define('".strtoupper($key)."', '$val');\n";
		}
	}
	return $evaluate;
}

function smthumb($size, $smthumb = 50) {
	if($size[0] <= $smthumb && $size[1] <= $smthumb) {
		return ['w' => $size[0], 'h' => $size[1]];
	}
	$sm = [];
	$x_ratio = $smthumb / $size[0];
	$y_ratio = $smthumb / $size[1];
	if(($x_ratio * $size[1]) < $smthumb) {
		$sm['h'] = ceil($x_ratio * $size[1]);
		$sm['w'] = $smthumb;
	} else {
		$sm['w'] = ceil($y_ratio * $size[0]);
		$sm['h'] = $smthumb;
	}
	return $sm;
}

function arrayeval($array, $level = 0) {
	if(!is_array($array)) {
		return "'".$array."'";
	}
	if(is_array($array) && function_exists('var_export')) {
		return var_export($array, true);
	}

	$space = str_repeat("\t", $level + 1);
	$evaluate = "Array\n$space(\n";
	$comma = $space;
	if(is_array($array)) {
		foreach($array as $key => $val) {
			$key = is_string($key) ? '\''.addcslashes($key, '\'\\').'\'' : $key;
			$val = !is_array($val) && (!preg_match('/^\-?[1-9]\d*$/', $val) || strlen($val) > 12) ? '\''.addcslashes($val, '\'\\').'\'' : $val;
			if(is_array($val)) {
				$evaluate .= "$comma$key => ".arrayeval($val, $level + 1);
			} else {
				$evaluate .= "$comma$key => $val";
			}
			$comma = ",\n$space";
		}
	}
	$evaluate .= "\n$space)";
	return $evaluate;
}

function pluginsettingvalue($type) {
	global $_G;

	loadcache('pluginsetting');
	$pluginvalue = [];
	$pluginsetting = $_G['cache']['pluginsetting'][$type] ?? [];

	$varids = $pluginids = [];
	foreach($pluginsetting as $pluginid => $v) {
		foreach($v['setting'] as $varid => $var) {
			$varids[] = $varid;
			$pluginids[$varid] = $pluginid;
		}
	}
	if($varids) {
		foreach(table_common_pluginvar::t()->fetch_all($varids) as $plugin) {
			$values = (array)dunserialize($plugin['value']);
			foreach($values as $id => $value) {
				$pluginvalue[$id][$pluginids[$plugin['pluginvarid']]][$plugin['variable']] = $value;
			}
		}
	}

	return $pluginvalue;
}

function stylesettingvalue($type) {
	global $_G;

	loadcache('stylesetting');
	$stylevalue = [];
	$stylesetting = $_G['cache']['stylesetting'][$type] ?? [];

	$varids = $styleids = [];
	foreach($stylesetting as $styleid => $v) {
		foreach($v['setting'] as $varid => $var) {
			$varids[] = $varid;
			$styleids[$varid] = $styleid;
		}
	}
	if($varids) {
		foreach(table_common_stylevar_extra::t()->fetch_all($varids) as $style) {
			$values = (array)dunserialize($style['value']);
			foreach($values as $id => $value) {
				$stylevalue[$id][$styleids[$style['stylevarid']]][$style['variable']] = $value;
			}
		}
	}

	return $stylevalue;
}

function cleartemplatecache() {
	$cachedir = DISCUZ_DATA.'./template';
	if(!is_dir($cachedir)) {
		dmkdir($cachedir);
	}
	$tpl = dir($cachedir);
	while($entry = $tpl->read()) {
		if(preg_match('/\.tpl\.php$/', $entry)) {
			@unlink(DISCUZ_DATA.'./template/'.$entry);
		}
	}
	$tpl->close();

	cleardiycache();
}

function cleardiycache($dir = DISCUZ_DATA.'./diy') {
	if($directory = @dir($dir)) {
		while($entry = $directory->read()) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$filename = $dir.'/'.$entry;
			if(is_file($filename)) {
				@unlink($filename);
			} else {
				cleardiycache($filename);
			}
		}
		$directory->close();
		@rmdir($dir);
	}
}

