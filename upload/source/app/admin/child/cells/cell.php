<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$type = !empty($_GET['type']) ? 1 : 0;
$_cellId = cells::getClass(DISCUZ_TEMPLATE($style['directory']).'/cells/'.$cellId.'.php');
if(!$_cellId) {
	if($id != 1) {
		$id = 1;
		$style = table_common_style::t()->fetch_by_styleid($id);
		if(!$style) {
			frame_cpmsg('style_not_found');
		}
		$_cellId = cells::getClass(DISCUZ_TEMPLATE($style['directory']).'/cells/'.$cellId.'.php');
		if(!$_cellId) {
			frame_cpmsg('cells_not_found');
		}
	} else {
		frame_cpmsg('cells_not_found');
	}
}
$cellId = $_cellId;
$c = cells::className($cellId);

if(property_exists($c, 'mobileonly')) {
	$type = 1;
}
if(property_exists($c, 'pconly')) {
	$type = 0;
}

$_G['setting']['cells'] = $cellValue = table_common_setting::t()->fetch_setting('cells', true);

if(submitcheck('submit')) {
	if(strlen($_GET['cell']) == 0 || !empty($_GET['del'])) {
		$_GET['cell'] = $c::getDefault($type);
	}
	if(!cells::checkRequire($cellId, $_GET['cell'])) {
		frame_cpmsg('cells_require_error');
	}
	if(!cells::checkTemplate($_GET['cell'])) {
		frame_cpmsg('cells_format_error');
	}
	cells::saveTemplate($id, $cellId, $type, $_GET['cell']);
	frame_cpmsg('cells_edit_succeed', true);
}

if(submitcheck('updatesubmit')) {
	require_once libfile('function/cache');
	updatecache('setting');
	cleartemplatecache();
	frame_cpmsg('cells_cache_succeed', true);
}

$list = '';
foreach($c::$cellList as $cell => $memo) {
	$list .= '<li><a href="javascript:editor.session.insert(editor.session.selection.getRange().start, \'{cell '.$cell.'}\')" ondragstart="return false;">'.$memo.'</a></li>';
}

loadcache('styleconsts');
if(!empty($_G['cache']['styleconsts']) && !empty($_G['cache']['styleconsts'][$id])) {
	$list .= '<li><select onchange="editor.session.insert(editor.session.selection.getRange().start, this.value);this.value=\'\'"><option value="">- '.cplang('cells_select_styleconsts').' -</option>';
	foreach($_G['cache']['styleconsts'][$id] as $k => $v) {
		$list .= '<option value="'.$k.'">'.$k.'</option>';
	}
	$list .= '</select>';
}

showchildmenu([['styles_admin', 'styles'], [$style['name'].' ', ''], [cplang('cells'), 'cells&id='.$id]], $c::$name);

$value = $_G['setting']['cells'][cells::getTplKey($type)][$id][$cellId] ?: $c::getDefault($type);
showformheader('cells&id='.$id.'&cellId='.$cellId.'&type='.$type, 'target="hframe"');
showtableheader('', 'tb2');
echo '<tr style="height: 600px">';
echo '<td valign="top" width="70%">';
echo '<div class="itemtitle">';
echo '<ul class="tab1">';
if(!property_exists($c, 'mobileonly')) {
	echo '<li'.(!$type ? ' class="current"' : '').'><a href="'.ADMINSCRIPT.'?action=cells&id='.$id.'&cellId='.$cellId.'&type=0"><span>PC</span></a></li>';
}
if(!property_exists($c, 'pconly')) {
	echo '<li'.($type ? ' class="current"' : '').'><a href="'.ADMINSCRIPT.'?action=cells&id='.$id.'&cellId='.$cellId.'&type=1"><span>Mobile</span></a></li>';
}
echo '</ul></div>';
echo '<div id="ace_editor" style="width:98%;height: 90%;border:1px solid #cdcdcd;min-height:980px"></div>';
echo '<textarea id="cell" style="display:none;" name="cell" spellcheck="false">'.dhtmlspecialchars($value).'</textarea></td>';
echo '<td valign="top" class="tipsblock"><div class="infotitle1">'.cplang('cell_item').'</div><ul>'.$list.'</ul><br /><div class="infotitle1">'.cplang('cell_usage').'</div>'.$c::$useage.'</td>';
echo '</tr>';
showsubmit('submit', 'submit', '',
	'<input type="submit" class="btn" name="updatesubmit" value="'.cplang('tools_updatecache').'" /><label><input name="del" class="checkbox" value="1" type="checkbox" />'.cplang('to_default').'</label> &nbsp; '.cplang('cells_notice'));
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
	