<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

class app_group_switch {

	const Icon = STATICURL.'image/app/group.svg';
	const Name = 'setting_functions_curscript_group';
	const Desc = 'setting_functions_curscript_group_intro';
	const OrderId = 1;

	public static function getStatus() {
		return getglobal('setting/groupstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=group" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}