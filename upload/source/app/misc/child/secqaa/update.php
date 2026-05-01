<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!defined('IN_RESTFUL')) {
	$refererhost = parse_url($_SERVER['HTTP_REFERER']);
	$refererhost['host'] .= !empty($refererhost['port']) ? (':'.$refererhost['port']) : '';

	if($refererhost['host'] != $_SERVER['HTTP_HOST']) {
		exit('Access Denied');
	}
}

$message = $question = '';
$showid = 'secqaa_'.$idhash;
if($_G['setting']['secqaa']) {
	$question = make_secqaa();
}
if(defined('IN_RESTFUL')) {
	echo $question;
	exit;
}

if($_G['inajax']) {
	include template('common/header_ajax');
	echo $question;
	include template('common/footer_ajax');
	exit;
}

$message = preg_replace("/\r|\n/", '', $question);
$message = str_replace("'", "\'", $message);
$seclang = lang('forum/misc');
header('Content-Type: application/javascript');
echo <<<EOF
if($('$showid')) {
	var sectpl = seccheck_tpl['$idhash'] != '' && typeof seccheck_tpl['$idhash'] != 'undefined' ? seccheck_tpl['$idhash'].replace(/<hash>/g, 'code$idhash') : '';
	var sectplcode = sectpl != '' ? sectpl.split('<sec>') : Array('<br />',': ','<br />','');
	var string = '<input name="secqaahash" type="hidden" value="$idhash" />' + sectplcode[0] + '{$seclang['secqaa']}' + sectplcode[1] + '<input name="secanswer" id="secqaaverify_$idhash" type="text" autocomplete="off" style="{$imemode}width:100px" class="txt px vm" onblur="checksec(\'qaa\', \'$idhash\')" />' +
		' <a href="javascript:;" onclick="updatesecqaa(\'$idhash\');doane(event);" class="xi2">{$seclang['seccode_update']}</a>' +
		'<span id="checksecqaaverify_$idhash"><img src="' + STATICURL + 'image/common/none.gif" width="16" height="16" class="vm" /></span>' +
		sectplcode[2] + '$message' + sectplcode[3];
	evalscript(string);
	$('$showid').innerHTML = string;
}
EOF;
	