<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($_GET['blockid'])) {
	cpmsg('undefined_action');
}

$blockid = $_GET['blockid'];

$type = !empty($_GET['type']) && in_array($_GET['type'], [0, 1, 2]) ? intval($_GET['type']) : 0;

$editorblock = table_common_editorblock::t()->fetch($blockid);
if(!$editorblock) {
	cpmsg('editorblock_nonexistence', '', 'error');
}
$class = $editorblock['class'];

if(!empty($editorblock['plugin'])) {
	include_once DISCUZ_PLUGIN($editorblock['plugin']).'/editorblock/editorblock_'.$editorblock['class'].'.php';
	$editorblockclass = 'editorblock_'.$class;
} else {
	require_once libfile('editorblock/'.$editorblock['class'], 'class');
	$editorblockclass = 'editorblock_'.$class;
}
$editorblockclass = new $editorblockclass;

if(empty($editorblock['parser'])) {
	$editorblock['parser'] = $editorblockclass->getParser();
}
if(empty($editorblock['style'])) {
	$editorblock['style'] = $editorblockclass->getStyle();
}

if(!submitcheck('editorblocksubmit')) {
	$parameter = $editorblockclass->getParameter();

	showchildmenu([['setting_editor', 'setting&operation=editor'], ['setting_editor_block', 'editorblock&operation=list']], $editorblock['name']);

	showformheader('editorblock&operation=parser&blockid='.$blockid.'&type='.$type, 'target="hframe"');
	showtableheader('', 'tb2');
	echo '<tr style="height: 600px">';
	echo '<td valign="top" width="70%">';
	echo '<div class="itemtitle">';
	echo '<ul class="tab1">';
	echo '<li'.($type == 0 ? ' class="current"' : '').'><a href="'.ADMINSCRIPT.'?action=editorblock&operation=parser&blockid='.$blockid.'&type=0"><span>HTML</span></a></li>';
	echo '<li'.($type == 1 ? ' class="current"' : '').'><a href="'.ADMINSCRIPT.'?action=editorblock&operation=parser&blockid='.$blockid.'&type=1"><span>CSS</span></a></li>';
	echo '<li'.($type == 2 ? ' class="current"' : '').'><a href="'.ADMINSCRIPT.'?action=editorblock&operation=parser&blockid='.$blockid.'&type=2"><span>Config</span></a></li>';
	echo '</ul></div>';
	$value = match ($type) {
		2 => $editorblock['config'],
		1 => $editorblock['style'],
		default => $editorblock['parser'],
	};
	echo '<div id="ace_editor" style="width:98%;height: 90%;border:1px solid #cdcdcd;min-height:980px"></div>';
	echo '<textarea id="cell" style="display:none;" name="contentnew" spellcheck="false">'.dhtmlspecialchars($value).'</textarea></td>';
	echo '<td valign="top" class="tipsblock"><div class="infotitle1">'.cplang('editorblock_parametertip').'</div><ul><pre>'.$parameter.'</pre></ul><br /><br/><div class="infotitle1">'.cplang('editorblock_usage').'</div>'.cplang('editorblock_usage_content').'<br /></td>';
	echo '</tr>';
	showsubmit('editorblocksubmit', 'submit', '',
		'<label><input name="del" class="checkbox" value="1" type="checkbox" />'.cplang('to_default').'</label> &nbsp; '.cplang('cells_notice'));
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

	if(empty($contentnew) && empty($_GET['del'])) {
		cpmsg('editorblock_parameter_null', '', 'error');
	}

	if(!empty($_GET['del'])) {
		if($type == 0) {
			$contentnew = $editorblockclass->getParser();
		} elseif($type == 1) {
			$contentnew = $editorblockclass->getStyle();
		} elseif($type == 2) {
			$contentnew = $editorblockclass->getConfig();
		}
	}

	if(!editor::checkTemplate($contentnew)) {
		frame_cpmsg('cells_format_error');
	}

	if($type == 0) {
		table_common_editorblock::t()->update($blockid, [
			'parser' => $contentnew,
		]);
		$editorblock['parser'] = $contentnew;
	} elseif($type == 1) {
		table_common_editorblock::t()->update($blockid, [
			'style' => $contentnew,
		]);
		$editorblock['style'] = $contentnew;
	} elseif($type == 2) {
		table_common_editorblock::t()->update($blockid, [
			'config' => $contentnew,
		]);
		$editorblock['config'] = $contentnew;
	}

	memory('set', 'editorblock_'.$editorblock['class'], $editorblock);
	frame_cpmsg('editorblock_succeed', true);

}
	