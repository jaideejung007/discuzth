<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$refererhost = parse_url($_SERVER['HTTP_REFERER']);
$refererhost['host'] .= !empty($refererhost['port']) ? (':'.$refererhost['port']) : '';

if($refererhost['host'] != $_SERVER['HTTP_HOST']) {
	exit('Access Denied');
}

$message = '';
$showid = 'secqaa_'.$idhash;
if($_G['setting']['secqaa']) {
	$question = make_secqaa();
}

$message = preg_replace("/\r|\n/", '', $question);
$message = str_replace("'", "\'", $message);
$seclang = lang('forum/misc');
header("Content-Type: application/javascript");
echo <<<EOF
if(document.getElementById('$showid')) {
	if(!document.getElementById('v$showid')) {
		var sectpl = seccheck_tpl['$idhash'] != '' && typeof seccheck_tpl['$idhash'] != 'undefined' ? seccheck_tpl['$idhash'].replace(/<hash>/g, 'code$idhash') : '';
		var sectplcode = sectpl != '' ? sectpl.split('<sec>') : Array('<br />',': ','','');
		var string = '<input name="secqaahash" type="hidden" value="$idhash" /><input type="text" class="txt px vm" style="ime-mode:disabled;width:115px;background:white;" autocomplete="off" value="" name="secanswer" id="secqaaverify_$idhash" placeholder="$seclang[secqaa]" /><span id="v$showid"><a href="javascript:;" onclick="updatesecqaa(\'$idhash\');" class="xi2">' + '$message' + 
			'</a></span>' +
			'<span id="checksecqaaverify_$idhash"></span>';
		evalscript(string);
		document.getElementById('$showid').innerHTML = string;
	} else {
		var string = '<a href="javascript:;" onclick="updatesecqaa(\'$idhash\');" class="xi2">' + '$message' + 
			'</a>';
		evalscript(string);
		document.getElementById('v$showid').innerHTML = string;
	}
}
EOF;

	