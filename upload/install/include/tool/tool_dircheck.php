<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('RUN_MODE') || RUN_MODE != 'tool') {
	show_msg('method_undefined', $method, 0);
}

if(!empty($_GET['getExport']) && preg_match('/^\w+$/', $_GET['getExport'])) {
	$f = sys_get_temp_dir().'/'.$_GET['getExport'];
	$export = file_get_contents($f);
	header('Content-type: application/octet-stream');
	header('Accept-Ranges: bytes');
	header('Content-Length: '.strlen($export));
	header('Content-Disposition: attachment; filename=filecheck.txt');
	echo "\n".$export;
	@unlink($f);
	exit;
}

$entryarray = [
	'data',
	'data/attachment',
	'data/attachment/album',
	'data/attachment/category',
	'data/attachment/common',
	'data/attachment/forum',
	'data/attachment/group',
	'data/attachment/portal',
	'data/attachment/profile',
	'data/attachment/swfupload',
	'data/attachment/temp',
	'data/cache',
	'data/log',
	'data/template',
	'data/threadcache',
	'data/diy'
];

$result = '';
foreach($entryarray as $entry) {
	$fullentry = ROOT_PATH.'./'.$entry;
	if(!is_dir($fullentry) && !file_exists($fullentry)) {
		continue;
	} else {
		if(!dir_writeable($fullentry)) {
			show_msg('tool_dircheck_unwritable', $entry, 0);
		}
	}
}

if(!$discuzfiles = @file(ROOT_PATH.'./source/data/admincp/discuzfiles.md5')) {
	show_msg('tool_dircheck_checkfile_notexists', '', 0);
}

$md5data = $md5datanew = $addlist = $dellist = $modifylist = $showlist = [];
$cachelist = checkcachefiles('data/sysdata/');
checkfiles('./', '', 0);
checkfiles('config/', '', 1, 'config_global.php,config_ucenter.php');
checkfiles('data/', '\.xml', 0);
checkfiles('data/', '\.htm', 0);
checkfiles('data/log/', '\.htm', 0);
checkfiles('data/plugindata/', '\.htm', 0);
checkfiles('data/download/', '\.htm', 0);
checkfiles('data/addonmd5/', '\.htm', 0);
checkfiles('data/avatar/', '\.htm', 0);
checkfiles('data/cache/', '\.htm', 0);
checkfiles('data/ipdata/', '\.htm|\.dat', 0);
checkfiles('data/template/', '\.htm', 0);
checkfiles('data/threadcache/', '\.htm', 0);
checkfiles('template/', '');
checkfiles('api/', '');
checkfiles('source/', '', 1, 'discuzfiles.md5,plugin');
checkfiles('source/app/plugin/', '', 1);
checkfiles('static/', '');
checkfiles('archiver/', '');

foreach($discuzfiles as $line) {
	$file = trim(substr($line, 34));
	$md5datanew[$file] = substr($line, 0, 32);
	if($md5datanew[$file] != $md5data[$file]) {
		$modifylist[$file] = $md5data[$file];
	}
	$md5datanew[$file] = $md5data[$file];
}

$weekbefore = time() - 604800;
$md5data = is_array($md5data) ? $md5data : [];
$md5datanew = is_array($md5datanew) ? $md5datanew : [];
$addlist = array_merge(array_diff_assoc($md5data, $md5datanew), is_array($cachelist[2]) ? $cachelist[2] : []);
$dellist = array_diff_assoc($md5datanew, $md5data);
$modifylist = array_merge(array_diff_assoc($modifylist, $dellist), is_array($cachelist[1]) ? $cachelist[1] : []);
$showlist = array_merge($md5data, $md5datanew, $cachelist[0]);
$dirlist = $dirlog = [];
foreach($showlist as $file => $md5) {
	$dir = dirname($file);
	if(is_array($modifylist) && array_key_exists($file, $modifylist)) {
		$fileststus = 'modify';
	} elseif(is_array($dellist) && array_key_exists($file, $dellist)) {
		$fileststus = 'del';
	} elseif(is_array($addlist) && array_key_exists($file, $addlist)) {
		$fileststus = 'add';
	} else {
		$fileststus = '';
	}
	if(file_exists(ROOT_PATH.'./'.$file)) {
		$filemtime = @filemtime(ROOT_PATH.'./'.$file);
		$fileststus && $dirlist[$fileststus][$dir][basename($file)] = [number_format(filesize(ROOT_PATH.'./'.$file)).' Bytes', gmdate($filemtime)];
	} else {
		$fileststus && $dirlist[$fileststus][$dir][basename($file)] = ['', ''];
	}
}

$c1 = count($modifylist);
$c2 = count($dellist);
$c3 = count($addlist);

$export = '[Modify: '.$c1."]\n";
$export .= implode("\n", array_keys($modifylist))."\n\n";
$export .= '[Delete: '.$c2."]\n";
$export .= implode("\n", array_keys($dellist))."\n\n";
$export .= '[Add: '.$c3."]\n";
$export .= implode("\n", array_keys($addlist));

if($c1 || $c2 || $c3) {
	$f = 'dzt_'.random(16);
	file_put_contents(sys_get_temp_dir().'/'.$f, $export);

	show_header();
	echo '</div><div class="main">';
	echo '<div class="box">';
	show_tips(sprintf(lang('tool_dircheck_result_download'), $f));
	echo '</div>
		<div class="btnbox">
			<em>'.lang('tool_tips').'</em>
			<div class="inputbox">
			<input type="button" name="oldbtn" value="'.lang('old_step').'" class="btn oldbtn" onclick="location.href=\'?\'">
			<input type="button" value="'.lang('done').'" class="btn" onclick="location.href=\'?method=done\'">
	      	</div></div>';
	show_footer();
}

show_header();
echo '</div><div class="main">';
echo '<div class="box">';
show_tips('tool_dircheck_result_noerror');
echo '</div>
	<div class="btnbox">
		<em>'.lang('tool_tips').'</em>
		<div class="inputbox">
		<input type="button" name="oldbtn" value="'.lang('old_step').'" class="btn oldbtn" onclick="location.href=\'?\'">
		<input type="button" value="'.lang('done').'" class="btn" onclick="location.href=\'?method=done\'">
        </div></div>';
show_footer();