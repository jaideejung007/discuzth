<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once libfile('function/portalcp');

cpheader();
$operation = 'list';

shownav('portal', 'topic');
$searchctrl = '<span style="float: right; padding-right: 40px;">'
	.'<a href="javascript:;" onclick="$(\'tb_search\').style.display=\'\';$(\'a_search_show\').style.display=\'none\';$(\'a_search_hide\').style.display=\'\';" id="a_search_show" style="display:none">'.cplang('show_search').'</a>'
	.'<a href="javascript:;" onclick="$(\'tb_search\').style.display=\'none\';$(\'a_search_show\').style.display=\'\';$(\'a_search_hide\').style.display=\'none\';" id="a_search_hide">'.cplang('hide_search').'</a>'
	.'</span>';
showsubmenu('topic', [
	['list', 'topic', 1],
	['topic_add', 'portal.php?mod=portalcp&ac=topic', 0, 1, 1]
], $searchctrl);

if(submitcheck('opsubmit')) {
	require_once childfile('topic/submit');
} else {
	require_once childfile('topic/form');
}

