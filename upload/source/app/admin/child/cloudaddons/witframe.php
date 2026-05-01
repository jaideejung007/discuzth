<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

@include_once DISCUZ_ROOT.'./source/discuz_version.php';
loadwitframe();

$baseConf = Lib\Core::GetSetting();
if(!$baseConf) {
	$appid = '9'.sprintf('%07d', random(7, true));
	$secret = strtoupper(random(16));
	$name = $appid;
	table_restful_app::t()->insert([
		'appid' => $appid,
		'secret' => $secret,
		'name' => $name,
		'status' => 1,
		'dateline' => TIMESTAMP,
	]);

	$conf = Lib\Site::Discuz_GetConf($_G['setting']['siteuniqueid'], [
		'website' => $_G['siteurl'],
		'ver' => DISCUZ_VERSION,
		'restful' => [
			'appid' => $appid,
			'secret' => $secret,
		],
	]);
}

$r = Lib\Site::Discuz_LoginWit($_G['setting']['siteuniqueid']);
if(!empty($r['url'])) {
	echo '<script type="text/javascript">top.location.href=\''.$r['url'].'&pid='.$baseConf['witPid'].'\';</script>';
	exit;
} else {
	cpmsg('cloudaddons_witframe_error', '', 'error');
}
	