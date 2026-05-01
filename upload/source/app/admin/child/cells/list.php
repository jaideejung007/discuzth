<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$files = cells::getCells(DISCUZ_TEMPLATE($style['directory']).'/cells');

if(!$files) {
	cpmsg('cells_not_found', '', 'error');
}

showchildmenu([['styles_admin', 'styles'], [$style['name'].' ', '']], cplang('cells'));

showtableheader($style['name']);
foreach($files as $file) {
	$cellId = cells::getClass($file);
	if($cellId) {
		echo '<tr><td><a class="files bold" href="'.ADMINSCRIPT.'?action=cells&id='.$id.'&cellId='.$cellId.'">'.cells::className($cellId)::$name.'</a></td></tr>';
	}
}
showtablefooter();
	