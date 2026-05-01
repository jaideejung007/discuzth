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

$discuz = $_G['setting']['discuz'];

$mask = '**********';

switch($operation) {
	case 'discuz':
	default:
		if(!submitcheck('submit')) {
			showformheader('account&method=discuz');
			/*search={"discuz":"action=account&method=discuz","discuz_base":"action=account&method=discuz"}*/
			showtableheader();
			showtitle('discuz_baseSetting');
			$discuz['secret'] = $discuz['secret'] ?
				substr($discuz['secret'], 0, 5).$mask.substr($discuz['secret'], -3) : '';
			showsetting('discuz_allow', 'discuz[allow]', $discuz['allow'], 'radio');
			showsetting('discuz_url', 'discuz[url]', $discuz['url'], 'text');
			showsetting('discuz_clientId', 'discuz[appid]', $discuz['appid'], 'text');
			showsetting('discuz_clientSecret', 'discuz[secret]', $discuz['secret'], 'text');
			showsetting('discuz_callbackUrl', 'discuz[callbackUrl]', $discuz['callbackUrl'], 'text');

			showsetting('discuz_loginUsernameRule', ['discuz[loginUsernameRule]', [
				[0, cplang('discuz_loginUsernameRule_username')],
				[2, cplang('discuz_loginUsernameRule_userDefine')],
			]], intval($discuz['loginUsernameRule']), 'mradio');

			showsetting('discuz_name', 'discuz[name]', $discuz['name'], 'text');
			showsetting('discuz_icon', 'discuz[icon]', $discuz['icon'], 'text');

			showsubmit('submit');
			showtablefooter();
			/*search*/
			showformfooter();

		} else {

			if(!empty($_GET['discuz']['secret']) && str_contains($_GET['discuz']['secret'], $mask)) {
				$_GET['discuz']['secret'] = $discuz['secret'];
			}

			if(empty($_GET['discuz']['callbackUrl'])) {
				$_GET['discuz']['callbackUrl'] = $_G['siteurl'].'api/discuz/callback.php';
			}

			if(!empty($_GET['discuz']['callbackUrl']) && !isHttpOrHttps($_GET['discuz']['callbackUrl'])) {
				cpmsg('account_msg_urlerr', '', 'error');
			}

			$settings = [
				'discuz' => $_GET['discuz'],
			];
			table_common_setting::t()->update_batch($settings);
			updatecache('setting');

			cpmsg('setting_update_succeed', 'action=account&method=discuz', 'succeed');

		}

}