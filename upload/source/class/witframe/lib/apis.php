<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace Lib;

use Exception;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class Apis {

	public static function __callStatic($name, $arguments) {
		list($plugin, $identifier, $interface, $action) = explode('_', $name);
		if(!preg_match('/^[A-Z]\w+$/', $plugin) ||
			!preg_match('/^\w+$/', $identifier) ||
			!preg_match('/^\w+$/', $interface)) {
			throw new Exception('plugin identifier is invalid', -1);
		}

		if(!$action) {
			$action = 'index';
		} elseif(!preg_match('/^\w+$/', $action)) {
			throw new Exception('plugin identifier is invalid', -1);
		}

		return Core::RequestWit(__CLASS__, $name, $arguments, Core::Type_ApisMethod);
	}

}