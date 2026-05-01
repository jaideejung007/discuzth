<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

$operation = $_GET['operation'] ?? '';

$wechat = $_G['setting']['wechat'];

$mask = '**********';

switch($operation) {
	case 'wechat':
	default:
		if(!submitcheck('submit')) {
			showtips('wechat_tips');
			showformheader('account&method=wechat');
			/*search={"wechat":"action=account&method=wechat","wechat_base":"action=account&method=wechat"}*/
			showtableheader();
			showtitle('wechat_baseSetting');
			$wechat['appSecret'] = $wechat['appSecret'] ?
				substr($wechat['appSecret'], 0, 8).$mask.substr($wechat['appSecret'], -3) : '';
			showsetting('wechat_allow', 'wechat[allow]', $wechat['allow'], 'radio');
			showsetting('wechat_appId', 'wechat[appId]', $wechat['appId'], 'text');
			showsetting('wechat_appSecret', 'wechat[appSecret]', $wechat['appSecret'], 'text');
			showsetting('wechat_callbackUrl', 'wechat[callbackUrl]', $wechat['callbackUrl'], 'text');

			showsetting('wechat_loginUsernameRule', ['wechat[loginUsernameRule]', [
				[0, cplang('wechat_loginUsernameRule_username')],
				[2, cplang('wechat_loginUsernameRule_userDefine')],
			]], intval($wechat['loginUsernameRule']), 'mradio');

			showsubmit('submit');
			showtablefooter();
			/*search*/
			showformfooter();

		} else {

			if(!empty($_GET['wechat']['appSecret']) && str_contains($_GET['wechat']['appSecret'], $mask)) {
				$_GET['wechat']['appSecret'] = $wechat['appSecret'];
			}

			if(empty($_GET['wechat']['callbackUrl'])) {
				$_GET['wechat']['callbackUrl'] = $_G['siteurl'].'api/wechat/callback.php';
			}

			if(!empty($_GET['wechat']['callbackUrl']) && !isHttpOrHttps($_GET['wechat']['callbackUrl'])) {
				cpmsg('account_msg_urlerr', '', 'error');
			}

			$settings = [
				'wechat' => $_GET['wechat'],
			];
			table_common_setting::t()->update_batch($settings);
			updatecache('setting');

			cpmsg('setting_update_succeed', 'action=account&method=wechat', 'succeed');

		}
}