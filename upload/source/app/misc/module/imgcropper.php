<?php
/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$_GET['img'] = htmlspecialchars($_GET['img']);
$_GET['bid'] = intval($_GET['bid']);
$_GET['picflag'] = intval($_GET['picflag']);
$_GET['ictype'] = !empty($_GET['ictype']) ? 'block' : '';
$_GET['width'] = intval($_GET['width']);
$_GET['height'] = intval($_GET['height']);
$prefix = '';

if(!submitcheck('imgcroppersubmit')) {
	if($_GET['op'] == 'loadcropper') {
		$cboxwidth = $_GET['width'] > 50 ? $_GET['width'] : 300;
		$cboxheight = $_GET['height'] > 50 ? $_GET['height'] : 300;

		$cbgboxwidth = $_GET['nw'] ? $_GET['nw'] : $cboxwidth + 300;
		$cbgboxheight = $_GET['nh'] ? $_GET['nh'] : $cboxheight + 300;
		$dragpl = ($cbgboxwidth - $cboxwidth) / 2;
		$dragpt = ($cbgboxheight - $cboxheight) / 2;
		$rWidth = $cbgboxwidth > 1200 ? 1200 : $cbgboxwidth;
		$rHeight = $cbgboxheight * $rWidth / $cbgboxwidth;
	} else {
		if($_GET['picflag']) {
			$prefix = $_GET['picflag'] == 2 ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl'];
		}
		if($_G['setting']['ftp']['on'] == 2) {
			$u = parse_url($_GET['img']);
			if(empty($u['scheme'])) {
				$prefix = $_G['setting']['ftp']['attachurl'];
			}
		}
	}
	$urlHash = authcode($_SERVER['QUERY_STRING'], 'ENCODE');
	include_once template('common/misc_imgcropper');
} else {
	$pathinfos = pathinfo($_GET['cutimg']);
	if(!in_array(strtolower($pathinfos['extension']), ['jpg', 'jpeg', 'gif', 'png', 'bmp'])) {
		showmessage('imagepreview_errorcode_0', null, null, ['showdialog' => true, 'closetime' => true]);
	}
	$cropfile = md5($_GET['cutimg']).'.jpg';
	$ictype = $_GET['ictype'];

	if($ictype == 'block') {
		require_once libfile('function/block');
		$block = table_common_block::t()->fetch($_GET['bid']);
		$cropfile = block_thumbpath($block, ['picflag' => intval($_GET['picflag']), 'pic' => $_GET['cutimg']]);
		$cutwidth = $block['picwidth'];
		$cutheight = $block['picheight'];
	} else {
		$cutwidth = $_GET['cutwidth'];
		$cutheight = $_GET['cutheight'];
	}
	$top = intval($_GET['cuttop'] < 0 ? 0 : $_GET['cuttop']);
	$left = intval($_GET['cutleft'] < 0 ? 0 : $_GET['cutleft']);
	$picwidth = $cutwidth > $_GET['picwidth'] ? $cutwidth : $_GET['picwidth'];
	$picheight = $cutheight > $_GET['picheight'] ? $cutheight : $_GET['picheight'];

	require_once libfile('class/image');
	$image = new image();
	if($_GET['picflag']) {
		$prefix = $_GET['picflag'] == 2 ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl'];
	}
	if($_G['setting']['ftp']['on'] == 2) {
		$u = parse_url($_GET['cutimg']);
		if(empty($u['scheme'])) {
			$prefix = $_G['setting']['ftp']['attachurl'];
		}
	}
	if(!$image->Thumb($prefix.$_GET['cutimg'], $cropfile, $picwidth, $picheight)) {
		showmessage('imagepreview_errorcode_'.$image->errorcode, null, null, ['showdialog' => true, 'closetime' => true]);
	}
	$image->Cropper($image->target, $cropfile, $cutwidth, $cutheight, $left, $top);
	$params = authcode($_GET['urlHash'], 'DECODE');
	if($_G['setting']['ftp']['on'] == 2) {
		parse_str($params, $get);
		ftpcmd('upload', $cropfile);
		if($get['itemid']) {
			table_common_block_item::t()->update($get['itemid'], [
				'makethumb' => 1,
				'thumbpath' => $cropfile
			]);
		}
	}
	showmessage('do_success', 'misc.php?'.$params);
}

