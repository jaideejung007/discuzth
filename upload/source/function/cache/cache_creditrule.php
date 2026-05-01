<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_creditrule() {
	$data = $sub_data = [];

	foreach(table_common_credit_rule::t()->fetch_all_rule() as $rule) {
		$rule['rulenameuni'] = urlencode(diconv($rule['rulename'], CHARSET, 'UTF-8', true));
		list($action, $sub) = explode('/', $rule['action']);
		if($sub) {
			$sub_data[$action][$rule['action']] = $rule;
		} else {
			$data[$rule['action']] = $rule;
		}
	}

	savecache('creditrule_sub', $sub_data);
	savecache('creditrule', $data);
}

