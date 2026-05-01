<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

class app_home_switch {
	public static function getModules() {
		return ['follow', 'feed', 'blog', 'friend', 'follower', 'album', 'share', 'doing', 'wall', 'task', 'medal', 'magic', 'favorite', 'pm'];
	}

}

class app_home_switch_follow {

	const Icon = STATICURL.'image/app/follow.svg';
	const Name = 'setting_functions_curscript_follow';
	const Desc = 'setting_functions_curscript_follow_intro';
	const OrderId = 3;

	public static function getStatus() {
		return getglobal('setting/followstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=follow" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_follower {

	const Icon = STATICURL.'image/app/friend.svg';
	const Name = 'setting_functions_curscript_follower';
	const Desc = 'setting_functions_curscript_follower_intro';
	const OrderId = 3;

	public static function getStatus() {
		return getglobal('setting/followerstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=follower" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_feed {

	const Icon = STATICURL.'image/app/feed.svg';
	const Name = 'setting_functions_curscript_feed';
	const Desc = 'setting_functions_curscript_feed_intro';
	const OrderId = 3;

	public static function getStatus() {
		return getglobal('setting/feedstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=feed" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_blog {

	const Icon = STATICURL.'image/app/blog.svg';
	const Name = 'setting_functions_curscript_blog';
	const Desc = 'setting_functions_curscript_blog_intro';
	const OrderId = 3;

	public static function getStatus() {
		return getglobal('setting/blogstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=blog" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_friend {

	const Icon = STATICURL.'image/app/friend.svg';
	const Name = 'setting_functions_curscript_friend';
	const Desc = 'setting_functions_curscript_friend_intro';
	const OrderId = 3;

	public static function getStatus() {
		return getglobal('setting/friendstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=friend" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_album {

	const Icon = STATICURL.'image/app/album.svg';
	const Name = 'setting_functions_curscript_album';
	const Desc = 'setting_functions_curscript_album_intro';
	const OrderId = 3;

	public static function getStatus() {
		return getglobal('setting/albumstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=album" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_share {

	const Icon = STATICURL.'image/app/share.svg';
	const Name = 'setting_functions_curscript_share';
	const Desc = 'setting_functions_curscript_share_intro';
	const OrderId = 3;

	public static function getStatus() {
		return getglobal('setting/sharestatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=share" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_doing {

	const Icon = STATICURL.'image/app/doing.svg';
	const Name = 'setting_functions_curscript_doing';
	const Desc = 'setting_functions_curscript_doing_intro';
	const OrderId = 1;

	public static function getStatus() {
		return getglobal('setting/doingstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=doing" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_wall {

	const Icon = STATICURL.'image/app/wall.svg';
	const Name = 'setting_functions_curscript_message';
	const Desc = 'setting_functions_curscript_message_intro';
	const OrderId = 3;

	public static function getStatus() {
		return getglobal('setting/wallstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=wall" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_task {

	const Icon = STATICURL.'image/app/task.svg';
	const Name = 'setting_functions_curscript_task';
	const Desc = 'setting_functions_curscript_task_intro';
	const OrderId = 2;

	public static function getStatus() {
		return getglobal('setting/taskstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=task" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_medal {

	const Icon = STATICURL.'image/app/medal.svg';
	const Name = 'setting_functions_curscript_medal';
	const Desc = 'setting_functions_curscript_medal_intro';
	const OrderId = 2;

	public static function getStatus() {
		return getglobal('setting/medalstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=medal" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_magic {

	const Icon = STATICURL.'image/app/magic.svg';
	const Name = 'setting_functions_curscript_magic';
	const Desc = 'setting_functions_curscript_magic_intro';
	const OrderId = 2;

	public static function getStatus() {
		return getglobal('setting/magicstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=magic" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_favorite {

	const Icon = STATICURL.'image/app/favorite.svg';
	const Name = 'setting_functions_curscript_favorite';
	const Desc = 'setting_functions_curscript_favorite_intro';
	const OrderId = 1;

	public static function getStatus() {
		return getglobal('setting/favoritestatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=favorite" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_pm {

	const Icon = STATICURL.'image/app/pm.svg';
	const Name = 'setting_functions_curscript_pm';
	const Desc = 'setting_functions_curscript_pm_intro';
	const OrderId = 2;

	public static function getStatus() {
		return getglobal('setting/pmstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=pm&funcsubmit=yes&formhash='.formhash().'&t='.time().'">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}