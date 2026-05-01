<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$moderate = $_GET['moderate'];
$moderation = ['delete' => [], 'undelete' => [], 'ignore' => []];
if(is_array($moderate)) {
	foreach($moderate as $pid => $action) {
		$moderation[$action][] = intval($pid);
	}
}

$postsdel = $postsundel = 0;
if($moderation['delete']) {
	$postsdel = recyclebinpostdelete($moderation['delete'], $posttableid);
}
if($moderation['undelete']) {
	$postsundel = recyclebinpostundelete($moderation['undelete'], $posttableid);
}

if($operation == 'search') {
	$cpmsg = cplang('recyclebinpost_succeed', ['postsdel' => $postsdel, 'postsundel' => $postsundel]);
	echo '<script type="text/JavaScript">alert(\''.$cpmsg.'\');parent.$(\'rbsearchform\').searchsubmit.click();</script>';
} else {
	cpmsg('recyclebinpost_succeed', 'action=recyclebinpost&operation='.$operation, 'succeed', ['postsdel' => $postsdel, 'postsundel' => $postsundel]);
}
	