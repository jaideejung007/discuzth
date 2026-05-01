<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

include template('common/header_ajax');
echo helper_seccheck::check_seccode($_GET['secverify'], $_GET['idhash'], 1, $modid, true) ? 'succeed' : 'invalid';
include template('common/footer_ajax');
	