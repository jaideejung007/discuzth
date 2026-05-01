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

$result = $tasklib->apply($id);

if($result === -1) {
	showmessage('task_relatedtask', 'home.php?mod=task&do=view&id='.$tasklib->task['relatedtaskid']);
} elseif($result === -2) {
	showmessage('task_grouplimit', 'home.php?mod=task&item=new');
} elseif($result === -3) {
	showmessage('task_duplicate', 'home.php?mod=task&item=new');
} elseif($result === -4) {
	showmessage('task_nextperiod', 'home.php?mod=task&item=new');
} elseif($result === -5) {
	showmessage('task_exclusivetask', 'home.php?mod=task&item=new');
} else {
	dsetcookie('taskdoing_'.$_G['uid'], 1, 7776000);
	showmessage('task_applied', 'home.php?mod=task&do=view&id='.$id);
}
	