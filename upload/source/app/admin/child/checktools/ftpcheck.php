<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$alertmsg = '';
$testcontent = md5('Discuz!'.random(64));
$testfile = 'test/discuztest.txt';
$attach_dir = $_G['setting']['attachdir'];
@mkdir($attach_dir.'test', 0777);
if(file_put_contents($attach_dir.'/'.$testfile, $testcontent) === false) {
	$alertmsg = cplang('setting_attach_remote_wtferr');
}

if(!$alertmsg) {
	$settingnew = $_GET['settingnew'];
	$settings['ftp'] = table_common_setting::t()->fetch_setting('ftp', true);
	if($settings['ftp']['on'] == 1) {
		$settings['ftp']['password'] = authcode($settings['ftp']['password'], 'DECODE', md5($_G['config']['security']['authkey']));
		$pwlen = strlen($settingnew['ftp']['password']);
		if($settingnew['ftp']['password'][0] == $settings['ftp']['password'][0] && $settingnew['ftp']['password'][$pwlen - 1] == $settings['ftp']['password'][strlen($settings['ftp']['password']) - 1] && substr($settingnew['ftp']['password'], 1, $pwlen - 2) == '********') {
			$settingnew['ftp']['password'] = $settings['ftp']['password'];
		}
		$settingnew['ftp']['password'] = authcode($settingnew['ftp']['password'], 'ENCODE', md5($_G['config']['security']['authkey']));
	} elseif($settings['ftp']['on'] == 2) {
		$settings['oss'] = table_common_setting::t()->fetch_setting('oss', true);
		$settings['oss']['oss_key'] = authcode($settings['oss']['oss_key'], 'DECODE', md5($_G['config']['security']['authkey']));
		$pwlen = strlen($settingnew['oss']['oss_key']);
		if($settingnew['oss']['oss_key'][0] == $settings['oss']['oss_key'][0] && $settingnew['oss']['oss_key'][$pwlen - 1] == $settings['oss']['oss_key'][strlen($settings['oss']['oss_key']) - 1] && substr($settingnew['oss']['oss_key'], 1, $pwlen - 2) == '********') {
			$settingnew['oss']['oss_key'] = $settings['oss']['oss_key'];
		}
		$settingnew['oss']['oss_key'] = authcode($settingnew['oss']['oss_key'], 'ENCODE', md5($_G['config']['security']['authkey']));

		$ossTypeParts = explode('_', $settingnew['oss']['oss_type']);
		$lastPart = end($ossTypeParts);

		$subValue = str_starts_with($lastPart, 'sub') ? substr($lastPart, 3) : '';
		!empty($subValue) && ($settingnew['oss']['oss_type'] = str_replace('_'.$lastPart,'', $settingnew['oss']['oss_type']));
		$settingnew['oss']['oss_subtype'] = $subValue;

		$_G['setting']['oss'] = $settingnew['oss'];
		$settingnew['ftp']['attachurl'] = $settingnew['oss']['oss_url'].($settingnew['oss']['oss_rootpath'] ? '/'.$settingnew['oss']['oss_rootpath'] : '');
		if(empty($settingnew['ftp']['host'])) {
			$settingnew['ftp']['host'] = $settingnew['oss']['oss_url'];
		}
	}
	$settingnew['ftp']['attachurl'] .= !str_ends_with($settingnew['ftp']['attachurl'], '/') ? '/' : '';
	$_G['setting']['ftp'] = $settingnew['ftp'];

	ftpcmd('upload', $testfile);
	$ftp = ftpcmd('object');
	if(ftpcmd('error')) {
		$alertmsg = cplang('setting_attach_remote_'.ftpcmd('error'));
	}
	if(!$alertmsg) {
		$str = getremotefile($_G['setting']['ftp']['attachurl'].$testfile);
		if($str !== $testcontent) {
			$alertmsg = cplang('setting_attach_remote_geterr');
		}
	}
	if(!$alertmsg) {
		ftpcmd('delete', $testfile);
		ftpcmd('delete', 'test/index.htm');
		$ftp->ftp_rmdir('test');
		$str = getremotefile($_G['setting']['ftp']['attachurl'].$testfile);
		if($str === $testcontent) {
			$alertmsg = cplang('setting_attach_remote_delerr');
		}
		@unlink($attach_dir.'/'.$testfile);
		@rmdir($attach_dir.'test');
	}
}
if(!$alertmsg) {
	$alertmsg = cplang('setting_attach_remote_ok');
}

echo '<script language="javascript">alert(\''.str_replace('\'', '\\\'', $alertmsg).'\');parent.$(\'cpform\').action=\''.ADMINSCRIPT.'?action=setting&edit=yes\';parent.$(\'cpform\').target=\'_self\'</script>';
	