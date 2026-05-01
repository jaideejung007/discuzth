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

$id = intval($_GET['id']);
$membervars = ['act', 'num', 'time'];
$postvars = ['act', 'forumid', 'num', 'time', 'threadid', 'authorid'];
$modvars = [];
$custom_types = table_common_setting::t()->fetch_setting('tasktypes', true);
$custom_scripts = array_keys($custom_types);

$submenus = [];
foreach($custom_types as $k => $v) {
	$submenus[] = [$v['name'], "tasks&operation=add&script=$k", $_GET['script'] == $k];
}

$operation = $operation ? $operation : 'list';

$file = childfile('tasks/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}
require_once $file;

function gettasks() {
	global $_G;
	$checkdirs = array_merge([''], $_G['setting']['plugins']['available']);
	$tasks = [];
	foreach($checkdirs as $key) {
		if($key) {
			$dir = DISCUZ_PLUGIN($key).'/task';
		} else {
			$dir = DISCUZ_ROOT.'./source/class/task';
		}
		if(!file_exists($dir)) {
			continue;
		}
		$taskdir = dir($dir);
		while($entry = $taskdir->read()) {
			if(!in_array($entry, ['.', '..']) && preg_match('/^task\_[\w\.]+$/', $entry) && str_ends_with($entry, '.php') && strlen($entry) < 30 && is_file($dir.'/'.$entry)) {
				@include_once $dir.'/'.$entry;
				$taskclass = substr($entry, 0, -4);
				if(class_exists($taskclass)) {
					$task = new $taskclass();
					$script = substr($taskclass, 5);
					$script = ($key ? $key.':' : '').$script;
					$tasks[$entry] = [
						'class' => $script,
						'name' => lang('task/'.$script, $task->name),
						'version' => $task->version,
						'copyright' => lang('task/'.$script, $task->copyright),
						'filemtime' => @filemtime($dir.'/'.$entry)
					];
				}
			}
		}
	}
	uasort($tasks, 'filemtimesort');
	return $tasks;
}

