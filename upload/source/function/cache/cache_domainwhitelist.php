<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_domainwhitelist() {

	$data = table_common_setting::t()->fetch_setting('domainwhitelist');
	$data = $data ? explode("\r\n", $data) : [];
	savecache('domainwhitelist', $data);
}

