<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

require_once '../../source/class/class_core.php';

$discuz = C::app();
$discuz->init();

if(getgpc('m') !== 'user' || getgpc('a') !== 'rectavatar') {
	exit;
}

$ftp = getglobal('setting/ftp');
$oss = getglobal('setting/oss');

if(!empty($ftp['on']) && $ftp['on'] == 2 && $oss['oss_avatar']) {
	@header('Expires: 0');
	@header('Cache-Control: private, post-check=0, pre-check=0, max-age=0', FALSE);
	@header('Pragma: no-cache');

	define("UC_DATADIR", DISCUZ_DATA.'attachment/');
	define("UC_UPAVTDIR", 'avatar/');

	@chmod(UC_DATADIR.UC_UPAVTDIR, 0777);

	$avatartype = getgpc('avatartype', 'G') == 'real' ? 'real' : 'virtual';
	$bigavatarfile = UC_UPAVTDIR.get_avatar($_G['uid'], 'big', $avatartype);
	dmkdir(dirname(UC_DATADIR.$bigavatarfile));
	$middleavatarfile = UC_UPAVTDIR.get_avatar($_G['uid'], 'middle', $avatartype);
	dmkdir(dirname(UC_DATADIR.$middleavatarfile));
	$smallavatarfile = UC_UPAVTDIR.get_avatar($_G['uid'], 'small', $avatartype);
	dmkdir(dirname(UC_DATADIR.$smallavatarfile));
	$bigavatar = base64_decode(getgpc('avatar1', 'P'));
	$middleavatar = base64_decode(getgpc('avatar2', 'P'));
	$smallavatar = base64_decode(getgpc('avatar3', 'P'));
	if(!$bigavatar || !$middleavatar || !$smallavatar) {
		return '<root><message type="error" value="-2" /></root>';
	}

	$success = 1;
	$fp = @fopen(UC_DATADIR.$bigavatarfile, 'wb');
	@fwrite($fp, $bigavatar);
	@fclose($fp);

	$fp = @fopen(UC_DATADIR.$middleavatarfile, 'wb');
	@fwrite($fp, $middleavatar);
	@fclose($fp);

	$fp = @fopen(UC_DATADIR.$smallavatarfile, 'wb');
	@fwrite($fp, $smallavatar);
	@fclose($fp);

	ftpcmd('upload', $bigavatarfile);
	ftpcmd('upload', $middleavatarfile);
	ftpcmd('upload', $smallavatarfile);

	@unlink(UC_DATADIR.$bigavatarfile);
	@unlink(UC_DATADIR.$middleavatarfile);
	@unlink(UC_DATADIR.$smallavatarfile);

	if(!$_G['member']['avatarstatus']) {
		table_common_member::t()->update($_G['uid'], ['avatarstatus' => '1']);
	}

	if($success) {
		echo "<script>window.parent.postMessage('success','*');</script>";
	} else {
		echo "<script>window.parent.postMessage('failure','*');</script>";
	}
} else {
	loaducenter();
	if(!UC_AVTPATH) {
		$avtpath = './data/avatar/';
	} else {
		$avtpath = str_replace('..', '', UC_AVTPATH);
	}
	define('UC_UPAVTDIR', realpath(DISCUZ_ROOT.$avtpath).'/');
	if(!empty($_G['uid'])) {
		echo uc_rectavatar($_G['uid']);
	} else {
		echo uc_rectavatar(0);
	}
}

function get_avatar($uid, $size = 'big', $type = '') {
	$size = in_array($size, ['big', 'middle', 'small']) ? $size : 'big';
	$uid = abs(intval($uid));
	$uid = sprintf('%09d', $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$typeadd = $type == 'real' ? '_real' : '';
	return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
}