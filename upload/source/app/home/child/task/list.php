<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$_GET['item'] = empty($_GET['item']) ? 'new' : $_GET['item'];
$actives = [$_GET['item'] => ' class="a"'];
$tasklist = $tasklib->tasklist($_GET['item']);
$listdata = $tasklib->listdata;
if($_GET['item'] == 'doing' && empty($tasklist)) {
	dsetcookie('taskdoing_'.$_G['uid']);
}
	