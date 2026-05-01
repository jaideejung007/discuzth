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
if($_G['setting']['seccodedata']['type'] == 2 || $_G['setting']['seccodedata']['type'] == 3) {
	$message = '<img onclick="updateseccode(\''.$idhash.'\')" width="'.$_G['setting']['seccodedata']['width'].'" height="'.$_G['setting']['seccodedata']['height'].'" src="misc.php?mod=seccode&mobile='.constant('IN_MOBILE').'&update='.$rand.'&idhash='.$idhash.'" class="seccodeimg" alt="" style="display: inline; visibility: visible;" />';
} else {
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
		$message = '<img onclick="updateseccode(\''.$idhash.'\')" width="'.$_G['setting']['seccodedata']['width'].'" height="'.$_G['setting']['seccodedata']['height'].'" src="misc.php?mod=seccode&mobile='.constant('IN_MOBILE').'&update='.$rand.'&idhash='.$idhash.'" class="seccodeimg" alt="" style="display: inline; visibility: visible;" />';
	}
}
$imemode = $_G['setting']['seccodedata']['type'] != 1 ? 'ime-mode:disabled;' : '';
$message = str_replace("'", "\'", $message);
$seclang = lang('forum/misc');
header('Content-Type: application/javascript');
echo <<<EOF
if(document.getElementById('$showid')) {
	if(!document.getElementById('v$showid')) {
		var sectpl = seccheck_tpl['$idhash'] != '' ? seccheck_tpl['$idhash'].replace(/<hash>/g, 'code$idhash') : '';
		var sectplcode = sectpl != '' ? sectpl.split('<sec>') : Array('<br />',': ','<br />','');
		var string = '<input name="seccodehash" type="hidden" value="$idhash" /><input name="seccodemodid" type="hidden" value="$modid" /><input type="text" class="txt px vm" style="ime-mode:disabled;width:115px;background:white;" autocomplete="off" value="" id="seccodeverify_$idhash" name="seccodeverify" placeholder="$seclang[seccode]" fwin="seccode"><span id="v$showid">$message</span>';
		evalscript(string);
		document.getElementById('$showid').innerHTML = string;
	} else {
		var string = '$message';
		evalscript(string);
		document.getElementById('v$showid').innerHTML = string;
	}
	$htmlcode
}
EOF;
	