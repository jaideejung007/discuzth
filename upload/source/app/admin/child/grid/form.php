<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$grid = table_common_setting::t()->fetch_setting('grid', true);
shownav('forum', 'forums_grid');
showsubmenu('forums_grid');
showtips('forums_grid_tips');
showformheader('grid');
showtableheader('');
showsetting('forums_grid_show_grid', 'grid[showgrid]', $grid['showgrid'], 'radio', '', 1);
showsetting('forums_grid_style_type', [0 => 'grid[gridtype]', [['0', $lang['forums_grid_style_image']], [1, $lang['forums_grid_style_text']]]], $grid['gridtype'], 'select');
showsetting('forums_grid_text_length', 'grid[textleng]', $grid['textleng'], 'text');
include_once libfile('function/forumlist');
$forumselect = '<select name="grid[fids][]" multiple="multiple" size="10"><option value="0"'.(in_array(0, $grid['fids']) ? ' selected' : '').'>'.$lang['all'].'</option>'.forumselect(FALSE, 0, $grid['fids'], TRUE).'</select>';
showsetting('forums_grid_data_source', '', '', $forumselect);
showsetting('forums_grid_high_light', 'grid[highlight]', $grid['highlight'], 'radio');
showsetting('forums_grid_target_blank', 'grid[targetblank]', $grid['targetblank'], 'radio');
showsetting('forums_grid_show_tips', 'grid[showtips]', $grid['showtips'], 'radio');
showsetting('forums_grid_cache_life', 'grid[cachelife]', $grid['cachelife'], 'text');
showtagfooter('tbody');
showsubmit('gridssubmit');
showtablefooter();
showformfooter();
	