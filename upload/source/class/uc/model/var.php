<?php

/**
 * [UCenter] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

!defined('IN_UC') && exit('Access Denied');

class base_var {

	private static $instance;
	var $time;
	var $onlineip;
	var $db;
	var $settings = [];
	var $cache = [];
	var $_CACHE = [];
	var $app = [];

	public static function bind(&$class) {
		if(empty(self::$instance)) {
			self::$instance = new base_var();
		}
		$class->time =& self::$instance->time;
		$class->onlineip =& self::$instance->onlineip;
		$class->db =& self::$instance->db;
		$class->settings =& self::$instance->settings;
		$class->cache =& self::$instance->cache;
		$class->_CACHE =& self::$instance->_CACHE;
		$class->app =& self::$instance->app;
	}

}

