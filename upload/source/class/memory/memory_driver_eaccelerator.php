<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class memory_driver_eaccelerator {

	public $cacheName = 'eAccelerator';
	public $enable;

	public function env() {
		return function_exists('eaccelerator_get');
	}

	public function init($config) {
		$this->enable = $this->env();
	}

	public function get($key) {
		return eaccelerator_get($key);
	}

	public function set($key, $value, $ttl = 0) {
		return eaccelerator_put($key, $value, $ttl);
	}

	public function rm($key) {
		return eaccelerator_rm($key);
	}

	public function clear() {
		return @eaccelerator_clear();
	}

}

