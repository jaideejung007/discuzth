<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

class app_forum_switch {
	public static function getModules() {
		return ['forum', 'collection', 'guide'];
	}

}

class app_forum_switch_forum {

	const Icon = STATICURL.'image/app/forum.svg';
	const Name = 'setting_functions_curscript_forum';
	const Desc = 'setting_functions_curscript_forum_intro';
	const OrderId = 1;

	public static function getStatus() {
		return getglobal('setting/forumstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=forum" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_forum_switch_collection {

	const Icon = STATICURL.'image/app/collection.svg';
	const Name = 'setting_functions_curscript_collection';
	const Desc = 'setting_functions_curscript_collection_intro';
	const OrderId = 1;

	public static function getStatus() {
		return getglobal('setting/collectionstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=collection" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_forum_switch_guide {

	const Icon = STATICURL.'image/app/guide.svg';
	const Name = 'setting_functions_curscript_guide';
	const Desc = 'setting_functions_curscript_guide_intro';
	const OrderId = 1;

	public static function getStatus() {
		return getglobal('setting/guidestatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=guide" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}