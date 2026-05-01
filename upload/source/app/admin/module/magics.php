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
$operation = $operation ? $operation : 'admin';

$file = childfile('magics/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;

function getmagics() {
	global $_G;
	$checkdirs = array_merge([''], $_G['setting']['plugins']['available']);
	$magics = [];
	foreach($checkdirs as $key) {
		if($key) {
			$dir = DISCUZ_PLUGIN($key).'/magic';
		} else {
			$dir = DISCUZ_ROOT.'./source/class/magic';
		}
		if(!file_exists($dir)) {
			continue;
		}
		$magicdir = dir($dir);
		while($entry = $magicdir->read()) {
			if(!in_array($entry, ['.', '..']) && preg_match('/^magic\_[\w\.]+$/', $entry) && str_ends_with($entry, '.php') && strlen($entry) < 30 && is_file($dir.'/'.$entry)) {
				@include_once $dir.'/'.$entry;
				$magicclass = substr($entry, 0, -4);
				if(class_exists($magicclass)) {
					$magic = new $magicclass();
					$script = substr($magicclass, 6);
					$script = ($key ? $key.':' : '').$script;
					$magics[$script] = [
						'class' => $script,
						'name' => lang('magic/'.$script, $magic->name),
						'desc' => lang('magic/'.$script, $magic->description),
						'price' => $magic->price,
						'weight' => $magic->weight,
						'useevent' => !empty($magic->useevent) ? $magic->useevent : 0,
						'version' => $magic->version,
						'copyright' => lang('magic/'.$script, $magic->copyright),
						'filemtime' => @filemtime($dir.'/'.$entry)
					];
				}
			}
		}
	}
	uasort($magics, 'filemtimesort');
	return $magics;
}

