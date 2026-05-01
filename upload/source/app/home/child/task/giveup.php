<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$tasklib->giveup($id);
showmessage('task_giveup', 'home.php?mod=task&item=view&id='.$id);
	