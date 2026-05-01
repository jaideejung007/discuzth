<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$_G['forum_thread']['starttime'] = gmdate($_G['forum_thread']['dateline']);
$_G['forum_thread']['remaintime'] = '';
switch($_G['forum_thread']['special']) {
	case 1:
		require_once childfile('poll', 'forum/thread');;
		break;
	case 2:
		require_once childfile('trade', 'forum/thread');;
		break;
	case 3:
		require_once childfile('reward', 'forum/thread');;
		break;
	case 4:
		require_once childfile('activity', 'forum/thread');;
		break;
	case 5:
		require_once childfile('debate', 'forum/thread');;
		break;
	case 127:
		if($_G['forum_firstpid']) {
			$sppos = strpos($postlist[$_G['forum_firstpid']]['message'], chr(0).chr(0).chr(0));
			$specialextra = substr($postlist[$_G['forum_firstpid']]['message'], $sppos + 3);
			$postlist[$_G['forum_firstpid']]['message'] = substr($postlist[$_G['forum_firstpid']]['message'], 0, $sppos);
			if($specialextra) {
				if(array_key_exists($specialextra, $_G['setting']['threadplugins'])) {
					@include_once DISCUZ_PLUGIN($_G['setting']['threadplugins'][$specialextra]['module']).'.class.php';
					$classname = 'threadplugin_'.$specialextra;
					if(class_exists($classname) && method_exists($threadpluginclass = new $classname, 'viewthread')) {
						$threadplughtml = $threadpluginclass->viewthread($_G['tid']);
					}
				}
			}
		}
		break;
}
	