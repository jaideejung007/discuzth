<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('cardsubmit')) {
	showformheader('card&operation=set&');
	showtableheader();
	/*search={"card_config_open":"action=card"}*/
	showsetting('card_config_open', 'card_config_open', ($card_setting['open'] ? $card_setting['open'] : 0), 'radio');
	/*search*/
	showsubmit('cardsubmit');
	showtablefooter();
	showformfooter();
} else {
	table_common_setting::t()->update_setting('card', ['open' => $_POST['card_config_open']]);
	updatecache('setting');
	cpmsg('card_config_succeed', 'action=card&operation=set', 'succeed');
}
	