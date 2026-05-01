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
echo helper_seccheck::check_secqaa($_GET['secverify'], $idhash, true) ? 'succeed' : 'invalid';
include template('common/footer_ajax');
	