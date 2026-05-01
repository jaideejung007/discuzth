<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('RUN_MODE') || RUN_MODE != 'tool') {
	show_msg('method_undefined', $method, 0);
}

show_header();
echo '</div><div class="main">';
show_setting('start');
echo '<div class="box">';
show_tips('tool_select_resetpw');
show_tips('tool_select_dircheck');
show_tips('tool_select_updatecache');
show_tips('tool_select_restore');
echo '</div>';
echo '<div class="btnbox">
		<em>'.lang('tool_tips').'</em>
		<div class="inputbox">
		<input type="button" value="'.lang('done').'" class="btn oldbtn" onclick="location.href=\'?method=done\'">
		<input type="submit" name="submitname" value="'.lang('tool_start').'" class="btn" onclick="location.href=\'?\'">
        </div></div>';
show_setting('end');
show_footer();