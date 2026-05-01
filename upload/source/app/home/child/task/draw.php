<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['uid']) {
	showmessage('not_loggedin', NULL, [], ['login' => 1]);
}

$result = $tasklib->draw($id);
if($result === -1) {
	showmessage('task_up_to_limit', 'home.php?mod=task', ['tasklimits' => $tasklib->task['tasklimits']]);
} elseif($result === -2) {
	showmessage('task_failed', 'home.php?mod=task&item=failed');
} elseif($result === -3) {
	showmessage($tasklib->messagevalues['msg'], 'home.php?mod=task&do=view&id='.$id, $tasklib->messagevalues['values']);
} elseif($result === -4) {
	showmessage('task_exclusivetask', 'home.php?mod=task&item=new');
} else {
	cleartaskstatus();
	showmessage('task_completed', 'home.php?mod=task&item=done');
}
	