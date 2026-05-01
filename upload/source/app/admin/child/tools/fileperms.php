<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$step = max(1, intval($_GET['step']));

shownav('tools', 'nav_fileperms');
showsubmenusteps('nav_fileperms', [
	['nav_fileperms_confirm', $step == 1],
	['nav_fileperms_verify', $step == 2],
	['nav_fileperms_completed', $step == 3]
]);

if($step == 1) {
	cpmsg(cplang('fileperms_check_note'), 'action=tools&operation=fileperms&step=2', 'button', '', FALSE);
} elseif($step == 2) {
	cpmsg(cplang('fileperms_check_waiting'), 'action=tools&operation=fileperms&step=3', 'loading', '', FALSE);
} elseif($step == 3) {

	showtips('fileperms_tips');

	$entryarray = [
		'data',
		'data/attachment',
		'data/attachment/album',
		'data/attachment/category',
		'data/attachment/common',
		'data/attachment/forum',
		'data/attachment/group',
		'data/attachment/portal',
		'data/attachment/profile',
		'data/attachment/swfupload',
		'data/attachment/temp',
		'data/cache',
		'data/log',
		'data/template',
		'data/threadcache',
		'data/diy'
	];

	$result = '';
	foreach($entryarray as $entry) {
		$fullentry = DISCUZ_ROOT.'./'.$entry;
		if(!is_dir($fullentry) && !file_exists($fullentry)) {
			continue;
		} else {
			if(!dir_writeable($fullentry)) {
				$result .= '<li class="error">'.(is_dir($fullentry) ? $lang['dir'] : $lang['file'])." ./$entry {$lang['fileperms_unwritable']}</li>";
			}
		}
	}
	$result = $result ? $result : '<li>'.$lang['fileperms_check_ok'].'</li>';
	echo '<div class="colorbox"><ul class="fileperms">'.$result.'</ul></div>';
}
	