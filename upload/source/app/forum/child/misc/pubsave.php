<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$return = threadpubsave($_G['tid']);
if($return > 0) {
	showmessage('post_newthread_succeed', dreferer(), ['coverimg' => '']);
} elseif($return == -1) {
	showmessage('post_newthread_mod_succeed', dreferer(), ['coverimg' => '']);
} elseif($return == -2) {
	showmessage('post_reply_mod_succeed', dreferer());
} else {
	showmessage('thread_nonexistence');
}
	