<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class discuz_container extends discuz_base {

	protected $_obj;

	protected $_objs = [];

	public function __construct($obj = null) {
		if(isset($obj)) {
			if(is_object($obj)) {
				$this->_obj = $obj;
			} else if(is_string($obj)) {
				try {
					if(func_num_args()) {
						$p = func_get_args();
						unset($p[0]);
						$ref = new ReflectionClass($obj);
						$this->_obj = $ref->newInstanceArgs($p);
						unset($ref);
					} else {
						$this->_obj = new $obj;
					}
				} catch (Exception $e) {
					throw new Exception('Class "'.$obj.'" does not exists.');
				}
			}
		}
		parent::__construct();
	}

	public function getobj() {
		return $this->_obj;
	}

	public function setobj($value) {
		$this->_obj = $value;
	}

	public function __call($name, $p) {
		if(method_exists($this->_obj, $name)) {
			if(isset($this->_obj->methods[$name][0])) {
				$this->_call($name, $p, 0);
			}
			$this->_obj->data = match (count($p)) {
				0 => $this->_obj->{$name}(),
				1 => $this->_obj->{$name}($p[0]),
				2 => $this->_obj->{$name}($p[0], $p[1]),
				3 => $this->_obj->{$name}($p[0], $p[1], $p[2]),
				4 => $this->_obj->{$name}($p[0], $p[1], $p[2], $p[3]),
				5 => $this->_obj->{$name}($p[0], $p[1], $p[2], $p[3], $p[4]),
				default => call_user_func_array([$this->_obj, $name], $p),
			};
			if(isset($this->_obj->methods[$name][1])) {
				$this->_call($name, $p, 1);
			}

			return $this->_obj->data;
		} else {
			throw new Exception('Class "'.get_class($this->_obj).'" does not have a method named "'.$name.'".');
		}
	}

	protected function _call($name, $p, $type) {
		$ret = null;
		if(isset($this->_obj->methods[$name][$type])) {
			foreach($this->_obj->methods[$name][$type] as $extend) {
				if(is_array($extend) && isset($extend['class'])) {
					$obj = $this->_getobj($extend['class'], $this->_obj);
					$ret = match (count($p)) {
						0 => $obj->{$extend['method']}(),
						1 => $obj->{$extend['method']}($p[0]),
						2 => $obj->{$extend['method']}($p[0], $p[1]),
						3 => $obj->{$extend['method']}($p[0], $p[1], $p[2]),
						4 => $obj->{$extend['method']}($p[0], $p[1], $p[2], $p[3]),
						5 => $obj->{$extend['method']}($p[0], $p[1], $p[2], $p[3], $p[4]),
						default => call_user_func_array([$obj, $extend['method']], $p),
					};
				} elseif(is_callable($extend, true)) {
					if(is_array($extend)) {
						list($obj, $method) = $extend;
						if(method_exists($obj, $method)) {
							if(is_object($obj)) {
								$obj->obj = $this->_obj;
								$ret = match (count($p)) {
									0 => $obj->{$method}(),
									1 => $obj->{$method}($p[0]),
									2 => $obj->{$method}($p[0], $p[1]),
									3 => $obj->{$method}($p[0], $p[1], $p[2]),
									4 => $obj->{$method}($p[0], $p[1], $p[2], $p[3]),
									5 => $obj->{$method}($p[0], $p[1], $p[2], $p[3], $p[4]),
									default => call_user_func_array([$obj, $method], $p),
								};
							} else {
								$p[] = $this;
								$ret = call_user_func_array($extend, $p);
							}
						}
					} else {
						$p[] = $this->_obj;
						$ret = call_user_func_array($extend, $p);
					}
				}
			}
		}
		return $ret;
	}

	protected function _getobj($class, $obj) {
		if(!isset($this->_objs[$class])) {
			$this->_objs[$class] = new $class($obj);
			if(method_exists($this->_objs[$class], 'init_base_var')) {
				$this->_objs[$class]->init_base_var();
			}
		}
		return $this->_objs[$class];
	}

	public function __get($name) {
		if(isset($this->_obj) && property_exists($this->_obj, $name) === true) {
			return $this->_obj->$name;
		} else {
			return parent::__get($name);
		}
	}

	public function __set($name, $value) {
		if(isset($this->_obj) && property_exists($this->_obj, $name) === true) {
			return $this->_obj->$name = $value;
		} else {
			return parent::__set($name, $value);
		}
	}

	public function __isset($name) {
		return isset($this->_obj->$name);
	}

}

