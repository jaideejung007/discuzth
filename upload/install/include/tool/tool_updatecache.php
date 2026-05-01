<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('RUN_MODE') || RUN_MODE != 'tool') {
	show_msg('method_undefined', $method, 0);
}

@unlink(ROOT_PATH.'./data/install.lock');
@unlink(ROOT_PATH.'./data/update.lock');
$url = '../misc.php?mod=initsys&force='.rawurlencode(authcode(time(), 'ENCODE', $_config['security']['authkey']));
show_header();
echo '</div><div class="main">';
echo '<div class="box">';
echo '<span id="msg" class="desc">'.lang('tool_updateceche_doing').'<script>
const script = document.createElement(\'script\');
script.src = \''.$url.'\';
script.onload = function() {
    document.getElementById("msg").innerHTML = \''.lang('tool_updatecache_done').'\';
    document.getElementById("done").style.display = \'\';
};
document.head.appendChild(script);
</script>';
echo '</div>
	<div class="btnbox" id="done" style="display: none;">
	<em>'.lang('tool_tips').'</em>
	<div class="inputbox">
		<input type="button" name="oldbtn" value="'.lang('old_step').'" class="btn oldbtn" onclick="location.href=\'?\'">
		<input type="button" value="'.lang('done').'" class="btn" onclick="location.href=\'?method=done\'">
        </div></div>';
show_footer();