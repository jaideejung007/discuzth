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

$qq = $_G['setting']['qq'];

$mask = '**********';

switch($operation) {
	case 'qq':
	default:
		if(!submitcheck('submit')) {
			showformheader('account&method=qq');
			/*search={"qq":"action=account&method=qq","qq_base":"action=account&method=qq"}*/
			showtableheader();
			showtitle('qq_baseSetting');
			$qq['clientSecret'] = $qq['clientSecret'] ?
				substr($qq['clientSecret'], 0, 8).$mask.substr($qq['clientSecret'], -3) : '';
			showsetting('qq_allow', 'qq[allow]', $qq['allow'], 'radio');
			showsetting('qq_clientId', 'qq[clientId]', $qq['clientId'], 'text');
			showsetting('qq_clientSecret', 'qq[clientSecret]', $qq['clientSecret'], 'text');
			showsetting('qq_callbackUrl', 'qq[callbackUrl]', $qq['callbackUrl'], 'text');

			showsetting('qq_loginUsernameRule', ['qq[loginUsernameRule]', [
				[0, cplang('qq_loginUsernameRule_username')],
				[2, cplang('qq_loginUsernameRule_userDefine')],
			]], intval($qq['loginUsernameRule']), 'mradio');

			showsubmit('submit');
			showtablefooter();
			/*search*/
			showformfooter();

		} else {

			if(!empty($_GET['qq']['clientSecret']) && str_contains($_GET['qq']['clientSecret'], $mask)) {
				$_GET['qq']['clientSecret'] = $qq['clientSecret'];
			}

			if(empty($_GET['qq']['callbackUrl'])) {
				$_GET['qq']['callbackUrl'] = $_G['siteurl'].'api/qq/callback.php';
			}

			if(!empty($_GET['qq']['callbackUrl']) && !isHttpOrHttps($_GET['qq']['callbackUrl'])) {
				cpmsg('account_msg_urlerr', '', 'error');
			}

			$settings = [
				'qq' => $_GET['qq'],
			];
			table_common_setting::t()->update_batch($settings);
			updatecache('setting');

			cpmsg('setting_update_succeed', 'action=account&method=qq', 'succeed');

		}

}