<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// list 表示短信网关列表, edit 表示编辑短信网关, setting 表示短信网关全局配置
$operation = $operation ? $operation : 'setting';

cpheader();

$file = childfile('smsgw/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}
require_once $file;

function getsmsgws() {
	global $_G;
	$checkdirs = array_merge([''], $_G['setting']['plugins']['available']);
	$smsgws = [];
	foreach($checkdirs as $key) {
		if($key) {
			$dir = DISCUZ_PLUGIN($key).'/smsgw';
		} else {
			$dir = DISCUZ_ROOT.'./source/class/smsgw';
		}
		if(!file_exists($dir)) {
			continue;
		}
		$smsgwdir = dir($dir);
		while($entry = $smsgwdir->read()) {
			if(!in_array($entry, ['.', '..']) && preg_match('/^smsgw\_[\w\.]+$/', $entry) && str_ends_with($entry, '.php') && strlen($entry) < 30 && is_file($dir.'/'.$entry)) {
				@include_once $dir.'/'.$entry;
				$smsgwclass = substr($entry, 0, -4);
				if(class_exists($smsgwclass)) {
					$smsgw = new $smsgwclass();
					$script = substr($smsgwclass, 6);
					$script = ($key ? $key.':' : '').$script;
					$smsgws[$entry] = [
						'class' => $script,
						'name' => lang('smsgw/'.$script, $smsgw->name),
						'version' => $smsgw->version,
						'copyright' => lang('smsgw/'.$script, $smsgw->copyright),
						'type' => $smsgw->type,
						'sendrule' => $smsgw->sendrule,
						'filemtime' => @filemtime($dir.'/'.$entry)
					];
				}
			}
		}
	}
	uasort($smsgws, 'filemtimesort');
	return $smsgws;
}

