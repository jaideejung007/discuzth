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

shownav('forum', 'menu_forums_portal');

$operation = $operation ? $operation : 'list';

require_once childfile('forumportal/class');

switch($operation) {
	case 'list':
		showsubmenu('menu_forums_portal', [
			['forumportal_nav_list', 'forumportal', 1],
			['forumportal_nav_setting', 'forumportal&operation=setting', 0],
		]);
		fp::list();
		break;
	case 'add':
		showchildmenu([['menu_forums_portal', 'forumportal']], cplang('add'));
		fp::add();
		break;
	case 'edit':
		fp::edit();
		break;
	case 'setting':
		showsubmenu('menu_forums_portal', [
			['forumportal_nav_list', 'forumportal', 0],
			['forumportal_nav_setting', 'forumportal&operation=setting', 1],
		]);
		fp::setting();
		break;
}