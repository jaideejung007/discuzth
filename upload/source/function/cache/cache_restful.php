<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_restful() {
	global $_G;
	$m = new memory_driver_redis();
	$m->init($_G['config']['memory']['redis']);
	if($m->enable) {
		$apis = table_restful_api::t()->fetch_all_data(true);
		foreach($apis as $data) {
			restful::cache('api', 'add', $data['baseuri'].'|'.$data['ver']);
		}

		$apps = table_restful_app::t()->fetch_all_data();
		foreach($apps as $data) {
			restful::cache('app', 'add', $data['appid']);
		}
	}
}