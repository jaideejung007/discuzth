<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$refererhost = parse_url($_SERVER['HTTP_REFERER'] ?? '');
$refererhost['host'] = ($refererhost['host'] ?? '').(!empty($refererhost['port']) ? (':'.$refererhost['port']) : '');

if(($_G['setting']['seccodedata']['type'] < 2 && ($refererhost['host'] != $_SERVER['HTTP_HOST'])) || ((defined('IN_MOBILE') && in_array($_G['setting']['seccodedata']['type'], [2, 3]) && ($refererhost['host'] != $_SERVER['HTTP_HOST'])) && ($_G['setting']['seccodedata']['type'] == 2 && !extension_loaded('ming') && $_POST['fromFlash'] != 1 || $_G['setting']['seccodedata']['type'] == 3 && $_GET['fromFlash'] != 1))) {
	
	
	
	
	if(!defined('IN_RESTFUL')) {
		exit('Access Denied');
	}
}

if(is_numeric($_G['setting']['seccodedata']['type']) || !preg_match('/^[\w\d:_]+$/i', $_G['setting']['seccodedata']['type'])) {

	$seccode = make_seccode();

	if(!$_G['setting']['nocacheheaders']) {
		@header('Expires: -1');
		@header('Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0', FALSE);
		@header('Pragma: no-cache');
	}

	require_once libfile('class/seccode');

	$code = new seccode();
	$code->code = $seccode;
	$code->type = (in_array($_G['setting']['seccodedata']['type'], [2, 3]) && defined('IN_MOBILE')) ? 0 : $_G['setting']['seccodedata']['type'];
	$code->width = $_G['setting']['seccodedata']['width'];
	$code->height = $_G['setting']['seccodedata']['height'];
	$code->background = $_G['setting']['seccodedata']['background'];
	$code->adulterate = $_G['setting']['seccodedata']['adulterate'];
	$code->ttf = $_G['setting']['seccodedata']['ttf'];
	$code->angle = $_G['setting']['seccodedata']['angle'];
	$code->warping = $_G['setting']['seccodedata']['warping'];
	$code->scatter = $_G['setting']['seccodedata']['scatter'];
	$code->color = $_G['setting']['seccodedata']['color'];
	$code->size = $_G['setting']['seccodedata']['size'];
	$code->shadow = $_G['setting']['seccodedata']['shadow'];
	$code->animator = $_G['setting']['seccodedata']['animator'];
	$code->fontpath = DISCUZ_ROOT.'./source/data/seccode/font/';
	$code->datapath = DISCUZ_ROOT.'./source/data/seccode/';
	$code->includepath = DISCUZ_ROOT.'./source/class/';
	$code->shuffer = $_G['setting']['seccodedata']['shuffer_order'];

	$code->display();

} else {
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
			make_seccode();
			$code = new $class();
			$image = $code->image($idhash, $modid);
			if($image) {
				dheader('location: '.$image);
			}
		}
	}
}
	