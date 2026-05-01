<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('MITFRAME_APP')) {
	exit('Access Denied');
}

const APPTYPEID = 0;

require './source/class/class_core.php';

$discuz = C::app();

$modarray = ['getpasswd', 'groupexpiry', 'logging', 'lostpasswd', 'register', 'regverify', 'switchstatus'];


$mod = !in_array($discuz->var['mod'], $modarray) && (!preg_match('/^\w+$/', $discuz->var['mod']) || !file_exists(DISCUZ_ROOT.'./source/module/member/member_'.$discuz->var['mod'].'.php')) ? 'register' : $discuz->var['mod'];

define('CURMODULE', $mod);

$discuz->init();
if($mod == 'register' && $discuz->var['mod'] != $_G['setting']['regname']) {
	showmessage('undefined_action');
}

if(isset($_GET['method'])) {
	if($_GET['method'] == 'system' && defined('IN_RESTFUL')) {
		$authtoken = random(10);
		$returntype = !empty($_GET['returntype']) ? '/'.$_GET['returntype'] : '';
		dheader('location: '.$_G['siteurl'].'login.php?referer='.rawurlencode($_G['siteurl'].'api/restful?/callback/'.$authtoken.$returntype));
	} elseif(!empty($_G['setting']['account']['loginLink']) && in_array($_GET['method'], $_G['setting']['account']['loginLink'])) {
		if(!defined('IN_RESTFUL')) {
			if($_GET['formhash'] != FORMHASH) {
				showmessage('undefined_action');
			}
			$referer = dreferer();
		} else {
			cells_account_icons::process();
			$authtoken = random(10);
			$returntype = !empty($_GET['returntype']) ? '/'.$_GET['returntype'] : '';
			$referer = $_G['siteurl'].'api/restful?/callback/'.$authtoken.$returntype;
		}
		if($_G['uid']) {
			dsetcookie('accountsign', authcode($_G['uid']."\t".$_G['clientip'], 'ENCODE', expiry: 300), 300);
		}
		account_base::callClass($_GET['method'], 'login', [$referer]);
	}
}

require libfile('function/member');
require libfile('class/member');
runhooks();

require_once appfile('module/'.$mod);
