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

$ucenter = $_G['setting']['ucenter'];

$mask = '**********';

switch($operation) {
	case 'ucenter':
	default:
		if(!submitcheck('submit')) {
			showformheader('account&method=ucenter');
			/*search={"ucenter":"action=account&method=ucenter","ucenter_base":"action=account&method=ucenter"}*/
			showtableheader();
			showtitle('ucenter_baseSetting');
			$ucenter['secret'] = $ucenter['secret'] ?
				substr($ucenter['secret'], 0, 5).$mask.substr($ucenter['secret'], -3) : '';
			showsetting('ucenter_allow', 'ucenter[allow]', $ucenter['allow'], 'radio');
			showsetting('ucenter_url', 'ucenter[url]', $ucenter['url'], 'text');
			showsetting('ucenter_clientId', 'ucenter[appid]', $ucenter['appid'], 'text');
			showsetting('ucenter_clientSecret', 'ucenter[secret]', $ucenter['secret'], 'text');
			showsetting('ucenter_callbackUrl', 'ucenter[callbackUrl]', $ucenter['callbackUrl'], 'text');

			showsetting('ucenter_loginUsernameRule', ['ucenter[loginUsernameRule]', [
				[0, cplang('ucenter_loginUsernameRule_username')],
				[2, cplang('ucenter_loginUsernameRule_userDefine')],
			]], intval($ucenter['loginUsernameRule']), 'mradio');

			showsetting('ucenter_name', 'ucenter[name]', $ucenter['name'], 'text');
			showsetting('ucenter_icon', 'ucenter[icon]', $ucenter['icon'], 'text');

			showsubmit('submit');
			showtablefooter();
			/*search*/
			showformfooter();

		} else {

			if(!empty($_GET['ucenter']['secret']) && str_contains($_GET['ucenter']['secret'], $mask)) {
				$_GET['ucenter']['secret'] = $ucenter['secret'];
			}

			if(empty($_GET['ucenter']['callbackUrl'])) {
				$_GET['ucenter']['callbackUrl'] = $_G['siteurl'].'api/ucenter/callback.php';
			}

			if(!empty($_GET['ucenter']['callbackUrl']) && !isHttpOrHttps($_GET['ucenter']['callbackUrl'])) {
				cpmsg('account_msg_urlerr', '', 'error');
			}

			$settings = [
				'ucenter' => $_GET['ucenter'],
			];
			table_common_setting::t()->update_batch($settings);
			updatecache('setting');

			cpmsg('setting_update_succeed', 'action=account&method=ucenter', 'succeed');

		}

}