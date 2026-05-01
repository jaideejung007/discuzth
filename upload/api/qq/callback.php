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

if(!account_base::validatorSign()) {
	dheader('location: '.$_G['siteurl']);
}

(new account_qq())->getLoginUser();