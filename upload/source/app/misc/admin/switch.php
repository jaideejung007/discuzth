<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

class app_misc_switch {

	const Icon = STATICURL.'image/app/ranklist.svg';
	const Name = 'setting_functions_curscript_ranklist';
	const Desc = 'setting_functions_curscript_ranklist_intro';
	const OrderId = 2;

	public static function getStatus() {
		return getglobal('setting/rankliststatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=ranklist" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}