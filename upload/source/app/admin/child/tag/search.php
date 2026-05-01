<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

/*search={"tag":"action=tag"}*/
showformheader('tag&operation=admin');
showtableheader();
showsetting('tagname', 'tagname', $tagname, 'text');
showsetting('feed_search_perpage', '', $_GET['perpage'], "<select name='perpage'><option value='20'>{$lang['perpage_20']}</option><option value='50'>{$lang['perpage_50']}</option><option value='100'>{$lang['perpage_100']}</option></select>");
showsetting('misc_tag_status', ['status', [
	['', cplang('unlimited')],
	[0, cplang('misc_tag_status_0')],
	[1, cplang('misc_tag_status_1')],
], TRUE], '', 'mradio');
showsubmit('searchsubmit');
showtablefooter();
showformfooter();
showtagfooter('div');
/*search*/
		