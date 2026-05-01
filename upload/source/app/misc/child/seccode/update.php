<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$message = '';
$showid = 'seccode_'.$idhash;
$rand = random(5, 1);
$htmlcode = '';
$ani = $_G['setting']['seccodedata']['animator'] ? '_ani' : '';
if($_G['setting']['seccodedata']['type'] == 2) {
} elseif($_G['setting']['seccodedata']['type'] == 3) {
} else {
	if(!empty($_G['setting']['seccodedata']['shuffer_order'])) {
		$ani = '_shuffer';
	}
	if(!is_numeric($_G['setting']['seccodedata']['type']) && preg_match('/^[\w\d:_]+$/i', $_G['setting']['seccodedata']['type'])) {
		$etype = explode(':', $_G['setting']['seccodedata']['type']);
		if(count($etype) > 1) {
			$codefile = DISCUZ_PLUGIN($etype[0]).'/seccode/seccode_'.$etype[1].'.php';
			$class = $etype[1];
		} else {
			$codefile = libfile('seccode/'.$_G['setting']['seccodedata']['type'], 'class');
			$class = $_G['setting']['seccodedata']['type'];
		}
		if(file_exists($codefile)) {
			@include_once $codefile;
			$class = 'seccode_'.$class;
			if(class_exists($class)) {
				$code = new $class();
				if(method_exists($code, 'make')) {
					ob_start();
					$seccode = $code->make($idhash, $modid);
					make_seccode($seccode);
					$message = preg_replace("/\r|\n/", '', ob_get_contents());
					ob_end_clean();
				}
			}
		}
	} else {
		$message = lang('core', 'seccode_image'.$ani.'_tips').'<img onclick="updateseccode(\''.$idhash.'\')" width="'.$_G['setting']['seccodedata']['width'].'" height="'.$_G['setting']['seccodedata']['height'].'" src="misc.php?mod=seccode&update='.$rand.'&idhash='.$idhash.'" class="vm" alt="" />';
	}
}
$imemode = $_G['setting']['seccodedata']['type'] != 1 ? 'ime-mode:disabled;' : '';
$message = str_replace("'", "\'", $message);
$seclang = lang('forum/misc');
header('Content-Type: application/javascript');
echo <<<EOF
if($('$showid')) {
	if(!$('v$showid')) {
		var sectpl = seccheck_tpl['$idhash'] != '' ? seccheck_tpl['$idhash'].replace(/<hash>/g, 'code$idhash') : '';
		var sectplcode = sectpl != '' ? sectpl.split('<sec>') : Array('<br />',': ','<br />','');
		var string = '<input name="seccodehash" type="hidden" value="$idhash" /><input name="seccodemodid" type="hidden" value="$modid" />' + sectplcode[0] + '{$seclang['seccode']}' + sectplcode[1] + '<input name="seccodeverify" id="seccodeverify_$idhash" type="text" autocomplete="off" style="{$imemode}width:100px" class="txt px vm" onblur="checksec(\'code\', \'$idhash\', 0, null, \'$modid\')" />' +
			' <a href="javascript:;" onclick="updateseccode(\'$idhash\');doane(event);" class="xi2">{$seclang['seccode_update']}</a>' +
			'<span id="checkseccodeverify_$idhash"><i class="fico-checkbox fic4 fc-t fnmr vm"></i></span>' +
			sectplcode[2] + '<span id="v$showid">$message</span>' + sectplcode[3];
		evalscript(string);
		$('$showid').innerHTML = string;
	} else {
		var string = '$message';
		evalscript(string);
		$('v$showid').innerHTML = string;
	}
	$htmlcode
}
EOF;
	