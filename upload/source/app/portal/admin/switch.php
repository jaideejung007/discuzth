<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

class app_portal_switch {

	const Icon = STATICURL.'image/app/portal.svg';
	const Name = 'setting_functions_curscript_portal';
	const Desc = 'setting_functions_curscript_portal_intro';
	const OrderId = 4;

	public static function getStatus() {
		return getglobal('setting/portalstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=portal" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}