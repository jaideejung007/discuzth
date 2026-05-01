<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

const ALLOWGUEST = true;
const IN_API = true;

require_once '../../source/class/class_core.php';

$discuz = C::app();
$discuz->init_cron = false;
$discuz->init_session = false;
$discuz->init();

if(!in_array($_GET['id'], $_G['setting']['plugins']['available']) || !account_base::validatorSign()) {
	dheader('location: '.$_G['siteurl']);
}

$atype = account_base::getAccountType($_GET['id']);
if(!$atype) {
	dheader('location: '.$_G['siteurl']);
}

if(@!file_exists($modfile = DISCUZ_PLUGIN($_GET['id']).'/account.class.php')) {
	dheader('location: '.$_G['siteurl']);
}

include $modfile;

$c = 'account_'.$_GET['id'];
if(!method_exists($c, 'getLoginUser')) {
	dheader('location: '.$_G['siteurl']);
}

(new $c())->getLoginUser();