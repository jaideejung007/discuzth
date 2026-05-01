<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!$admincp->isfounder) {
	cpmsg('noaccess_isfounder', '', 'error');
}
$templatearray = table_common_template::t()->fetch_all_data();
if(!$templatearray) {
	cpmsg('plugin_not_found', '', 'error');
} else {
	$addonids = $result = $errarray = $newarray = [];
	foreach($templatearray as $k => $row) {
		if(preg_match('/^.?\/template\/([a-z]+[a-z0-9_]*)$/', $row['directory'], $a) && $a[1] != 'default') {
			$addonids[$k] = $a[1].'.template';
		}
	}
	$checkresult = dunserialize(cloudaddons_upgradecheck($addonids));
	savecache('addoncheck_template', $checkresult);
	foreach($addonids as $k => $addonid) {
		if(isset($checkresult[$addonid])) {
			list($return, $newver) = explode(':', $checkresult[$addonid]);
			$result[$addonid]['result'] = $return;
			$result[$addonid]['id'] = $k;
			if($newver) {
				$result[$addonid]['newver'] = $newver;
			}
		}
	}
}
foreach($result as $id => $row) {
	if($row['result'] == 0) {
		$errarray[] = '<a href="'.ADMINSCRIPT.'?action=cloudaddons&frame=no&id='.$id.'&from=newver" target="_blank">'.$templatearray[$row['id']]['name'].'</a>';
	} elseif($row['result'] == 2) {
		$newarray[] = '<a href="'.ADMINSCRIPT.'?action=cloudaddons&frame=no&id='.$id.'&from=newver" target="_blank">'.$templatearray[$row['id']]['name'].($row['newver'] ? ' -> '.$row['newver'] : '').'</a>';
	}
}
if(!$newarray && !$errarray) {
	cpmsg('styles_validator_noupdate', '', 'error');
} else {
	shownav('template', 'plugins_validator');
	showsubmenu('styles_admin', [
		['styles_list', 'styles', 0],
		['styles_import', 'styles&operation=import', 0],
		['plugins_validator', 'styles&operation=upgradecheck', 1],
		['cloudaddons_style_link', 'cloudaddons&frame=no&operation=templates&from=more', 0, 1],
	], '<a href="https://www.dismall.com/?from=templates_question" target="_blank" class="rlink">'.$lang['templates_question'].'</a>');
	showtableheader();
	if($newarray) {
		showtitle('styles_validator_newversion');
		foreach($newarray as $row) {
			showtablerow('class="hover"', [], [$row]);
		}
	}
	if($errarray) {
		showtitle('styles_validator_error');
		foreach($errarray as $row) {
			showtablerow('class="hover"', [], [$row]);
		}
	}
	showtablefooter();
}
	