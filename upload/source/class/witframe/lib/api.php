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

class Api {

	public static function __callStatic($name, $arguments) {
		$return = Core::RequestWit(__CLASS__, $name, $arguments, Core::Type_NewClass);
		if(!isset($return['obj'])) {
			return null;
		}
		return new Api_Obj($return['obj']);
	}

}

class Api_Obj {

	public function __construct($obj) {
		$this->obj = $obj;
	}

	public function __call($name, $arguments) {
		$return = Core::RequestWit($this->obj, $name, $arguments, Core::Type_ObjMethod);
		$this->obj = $return['obj'];
		return $return['return'];
	}

}