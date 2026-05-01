<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class discuz_extend extends discuz_container {

	public $setting;
	public $member;
	public $group;
	public $param;

	public function __construct($obj) {
		parent::__construct($obj);
	}

	public function __call($name, $p) {
		if(method_exists($this->_obj, $name)) {
			return match (count($p)) {
				0 => $this->_obj->{$name}(),
				1 => $this->_obj->{$name}($p[0]),
				2 => $this->_obj->{$name}($p[0], $p[1]),
				3 => $this->_obj->{$name}($p[0], $p[1], $p[2]),
				4 => $this->_obj->{$name}($p[0], $p[1], $p[2], $p[3]),
				5 => $this->_obj->{$name}($p[0], $p[1], $p[2], $p[3], $p[4]),
				default => call_user_func_array([$this->_obj, $name], $p),
			};
		} else {
			return parent::__call($name, $p);
		}
	}

	public function init_base_var() {
		$this->setting = &$this->_obj->setting;
		$this->member = &$this->_obj->member;
		$this->group = &$this->_obj->group;
		$this->param = &$this->_obj->param;
	}


}

