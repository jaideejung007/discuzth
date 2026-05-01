<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once libfile('function/forumlist');
$anchor = $_GET['anchor'] ?? '';
$anchor = in_array($anchor, ['search', 'admin']) ? $anchor : 'search';

shownav('topic', 'nav_attaches'.($operation ? '_'.$operation : ''));
showsubmenusteps('nav_attaches'.($operation ? '_'.$operation : ''), [
	['search', !$searchsubmit],
	['admin', $searchsubmit],
]);
showtips('attach_tips', 'attach_tips', $searchsubmit);
showtagheader('div', 'search', !$searchsubmit);
showformheader('attach'.($operation ? '&operation='.$operation : ''));
showtableheader();
showsetting('attach_nomatched', 'nomatched', 0, 'radio');
if($operation != 'group') {
	showsetting('attach_forum', '', '', '<select name="inforum"><option value="all">&nbsp;&nbsp;>'.cplang('all').'</option><option value="">&nbsp;</option>'.forumselect(FALSE, 0, 0, TRUE).'</select>');
}
showsetting('attach_search_perpage', '', $_GET['perpage'], "<select name='perpage'><option value='20'>{$lang['perpage_20']}</option><option value='50'>{$lang['perpage_50']}</option><option value='100'>{$lang['perpage_100']}</option></select>");
showsetting('attach_sizerange', ['sizeless', 'sizemore'], ['', ''], 'range');
showsetting('attach_dlcountrange', ['dlcountless', 'dlcountmore'], ['', ''], 'range');
showsetting('attach_daysold', 'daysold', '', 'text');
showsetting('filename', 'filename', '', 'text');
showsetting('attach_keyword', 'keywords', '', 'text');
showsetting('attach_author', 'author', '', 'text');
showsubmit('searchsubmit', 'search');
showtablefooter();
showformfooter();
showtagfooter('div');
	