<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_secqaa() {
	global $_G;

	$data = [];
	$secqaanum = table_common_secquestion::t()->count();

	$start_limit = $secqaanum <= 10 ? 0 : mt_rand(0, $secqaanum - 10);
	$i = 1;
	foreach(table_common_secquestion::t()->fetch_all_secquestion($start_limit) as $secqaa) {
		if(!$secqaa['type']) {
			if(empty($_G['setting']['secqaa']['allowqa'])) {
				continue;
			}
			$secqaa['answer'] = md5($secqaa['answer']);
		}
		$data[$i] = $secqaa;
		$i++;
	}
	if(!empty($data)) {
		while(($secqaas = count($data)) < 9) {
			$data[$secqaas + 1] = $data[array_rand($data)];
		}
	}
	savecache('secqaa', $data);
}

