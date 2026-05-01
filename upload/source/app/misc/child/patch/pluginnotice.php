<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/admincp');
require_once libfile('function/plugin');
require_once libfile('function/cloudaddons');
$pluginarray = table_common_plugin::t()->fetch_all_data();
$addonids = $vers = [];
foreach($pluginarray as $row) {
	if(ispluginkey($row['identifier'])) {
		$addonids[] = $row['identifier'].'.plugin';
		$vers[$row['identifier'].'.plugin'] = $row['version'];
	}
}
$checkresult = dunserialize(cloudaddons_upgradecheck($addonids));
savecache('addoncheck_plugin', $checkresult);
$newversion = 0;
if(is_array($checkresult)) {
	foreach($checkresult as $addonid => $value) {
		list(, $newver, $sysver) = explode(':', $value);
		if($sysver && $sysver > $vers[$addonid] || $newver) {
			$newversion++;
		}
	}
}
include template('common/header_ajax');
if($newversion) {
	$lang = lang('forum/misc');
	echo '<div class="bm"><div class="bm_h cl"><a href="javascript:;" onclick="$(\'plugin_notice\').style.display=\'none\';setcookie(\'pluginnotice\', 1, 86400)" class="y" title="'.$lang['patch_close'].'">'.$lang['patch_close'].'</a>';
	echo '<h2 class="i">'.$lang['plugin_title'].'</h2></div><div class="bm_c">';
	echo '<div class="cl bbda pbm">'.lang('forum/misc', 'plugin_memo', ['number' => $newversion]).'</div>';
	echo '<div class="ptn cl"><a href="admin.php?action=plugins" class="xi2 y">'.$lang['plugin_link'].' &raquo;</a></div>';
	echo '</div></div>';
}
include template('common/footer_ajax');
exit;
	