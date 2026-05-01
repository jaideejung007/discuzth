<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace Lib;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class Site {

	public static function __callStatic($name, $arguments) {
		return Core::RequestWit(__CLASS__, $name, $arguments, Core::Type_StaticMethod);
	}

}