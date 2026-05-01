<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$value = $_G['setting']['editor_global_css'];

if(!submitcheck('submit')) {

	showsubmenu('global_css');
	showformheader('editorblock&operation=global_css', 'target="hframe"');
	showtableheader('<a href="'.ADMINSCRIPT.'?action=editorblock&operation=list">'.cplang('list').'</a> &raquo; <a href="'.ADMINSCRIPT.'?action=editorblock&operation=global_css">'.$lang['global_css'].'</a>', 'tb2');
	echo '<tr style="height: 600px">';
	echo '<td valign="top" width="70%">';
	echo '<div id="ace_editor" style="width:98%;height: 90%;border:1px solid #cdcdcd;min-height:980px"></div>';
	echo '<textarea id="cell" style="display:none;" name="contentnew" spellcheck="false">'.dhtmlspecialchars($value).'</textarea></td>';
	echo '<td valign="top" class="tipsblock"><div class="infotitle1">'.cplang('editorblock_usage').'</div>'.cplang('editorblock_usage_css_content').'<br /></td>';
	echo '</tr>';
	showsubmit('submit', 'submit', '',
		cplang('cells_notice'));
	showtablefooter();
	showformfooter();
	echo '<iframe id="hframe" name="hframe" style="display: none"></iframe>';
	echo <<<EOF
<script src="static/ace/ace.js?date=7.2.0" type="text/javascript" charset="UTF-8"></script>
<style>.ace_print-margin {display: none}</style>
<script>
var cookiepre = '{$_G['config']['cookie']['cookiepre']}', cookiedomain = '{$_G['config']['cookie']['cookiedomain']}', cookiepath = '{$_G['config']['cookie']['cookiepath']}';
var editor = ace.edit("ace_editor");
if(getcookie('darkmode') == 'd')
{
	editor.setTheme("ace/theme/monokai");
}
editor.session.setMode("ace/mode/html");
editor.setOption("wrap", "free");
editor.setOption("wrapBehavioursEnabled", true);
editor.setOption("displayIndentGuides", true);
editor.setOption("useWorker", false);
editor.setValue(document.getElementById('cell').value);
editor.getSession().on('change', function(e) {
    $('cell').value = editor.getValue(); 
});
editor.gotoLine(0);
</script>
EOF;

} else {
	$contentnew = $_GET['contentnew'];

	if(!editor::checkTemplate($contentnew)) {
		frame_cpmsg('cells_format_error');
	}

	$settings = [
		'editor_global_css' => $contentnew,
	];
	table_common_setting::t()->update_batch($settings);
	updatecache('setting');

	frame_cpmsg('editorblock_succeed', true);

}
	