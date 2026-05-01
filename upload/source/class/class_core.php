<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

error_reporting(E_ALL);

const IN_DISCUZ = true;
define('DISCUZ_ROOT', substr(dirname(__FILE__), 0, -12));
const DISCUZ_DATA = DISCUZ_ROOT.'data/';
const DISCUZ_ROOT_STATIC = DISCUZ_ROOT;
const DISCUZ_CORE_DEBUG = false;
const DISCUZ_TABLE_EXTENDABLE = false;

if(PHP_VERSION_ID < 80000) {
	exit('PHP version must be greater than 8.0, current version: '.PHP_VERSION);
}

set_exception_handler(['core', 'handleException']);

if(DISCUZ_CORE_DEBUG) {
	set_error_handler(['core', 'handleError']);
	register_shutdown_function(['core', 'handleShutdown']);
}

spl_autoload_register(['core', 'autoload']);

C::creatapp();

class core {
	private static $_tables;
	private static $_imports;
	private static $_app;
	private static $_memory;

	public static function app() {
		return self::$_app;
	}

	public static function creatapp() {
		if(!is_object(self::$_app)) {
			self::$_app = discuz_application::instance();
		}
		return self::$_app;
	}

	public static function t($name) {
		return self::_make_obj($name, 'table', DISCUZ_TABLE_EXTENDABLE);
	}

	public static function m($name) {
		$args = [];
		if(func_num_args() > 1) {
			$args = func_get_args();
			unset($args[0]);
		}
		return self::_make_obj($name, 'model', true, $args);
	}

	protected static function _make_obj($name, $type, $extendable = false, $p = []) {
		$pluginid = $cname = null;
		if($name[0] === '#') {
			[, $pluginid, $name] = explode('#', $name);
		} elseif($name[0] === '\\' && class_exists($name)) {
			$cname = $name;
		}
		if($cname === null) {
			$cname = $type.'_'.$name;
		}
		if(!isset(self::$_tables[$cname])) {
			if(!class_exists($cname, false)) {
				if($pluginid) {
					self::importPlugin($pluginid, $type, $name);
				} else {
					self::import('class/'.$type.'/'.$name);
				}
			}
			if($extendable) {
				self::$_tables[$cname] = new discuz_container();
				switch(count($p)) {
					case 0:
						self::$_tables[$cname]->obj = new $cname();
						break;
					case 1:
						self::$_tables[$cname]->obj = new $cname($p[1]);
						break;
					case 2:
						self::$_tables[$cname]->obj = new $cname($p[1], $p[2]);
						break;
					case 3:
						self::$_tables[$cname]->obj = new $cname($p[1], $p[2], $p[3]);
						break;
					case 4:
						self::$_tables[$cname]->obj = new $cname($p[1], $p[2], $p[3], $p[4]);
						break;
					case 5:
						self::$_tables[$cname]->obj = new $cname($p[1], $p[2], $p[3], $p[4], $p[5]);
						break;
					default:
						$ref = new ReflectionClass($cname);
						self::$_tables[$cname]->obj = $ref->newInstanceArgs($p);
						unset($ref);
						break;
				}
			} else {
				self::$_tables[$cname] = new $cname();
			}
		}
		return self::$_tables[$cname];
	}

	public static function memory() {
		if(!self::$_memory) {
			self::$_memory = new discuz_memory();
			self::$_memory->init(self::app()->config['memory']);
		}
		return self::$_memory;
	}

	public static function importPlugin($pluginid, $type, $name) {
		$key = $pluginid.':'.$type.$name;
		if(!isset(self::$_imports[$key])) {
			$path = DISCUZ_PLUGIN($pluginid).'/';
			$pre = basename($type);
			$filename = $type.'/'.$pre.'_'.basename($name).'.php';
			if(is_file($path.'/'.$filename)) {
				include $path.'/'.$filename;
				self::$_imports[$key] = true;

				return true;
			} else {
				throw new Exception('Oops! System file lost: '.$filename);
			}
		}
		return true;
	}

	public static function import($name, $folder = '', $force = true) {
		$key = $folder.$name;
		if(!isset(self::$_imports[$key])) {
			$path = DISCUZ_ROOT.'/source/'.$folder;
			if(str_contains($name, '/')) {
				$pre = basename(dirname($name));
				$filename = dirname($name).'/'.$pre.'_'.basename($name).'.php';
			} else {
				$filename = $name.'.php';
			}

			if(is_file($path.'/'.$filename)) {
				include $path.'/'.$filename;
				self::$_imports[$key] = true;

				return true;
			} elseif(!$force) {
				return false;
			} else {
				throw new Exception('Oops! System file lost: '.$filename);
			}
		}
		return true;
	}

	public static function handleException($exception) {
		discuz_error::exception_error($exception);
	}


	public static function handleError($errno, $errstr, $errfile, $errline) {
		if($errno & DISCUZ_CORE_DEBUG) {
			discuz_error::system_error($errstr, false, true, false);
		}
	}

	public static function handleShutdown() {
		if(($error = error_get_last()) && $error['type'] & DISCUZ_CORE_DEBUG) {
			discuz_error::system_error($error['message'], false, true, false);
		}
	}

	public static function autoload($class) {
		$pathfile = null;
		$class = strtolower($class);
		if(str_contains($class, '_')) {
			$_p = strpos($class, '\\');
			if($_p === false) {
				[$folder] = explode('_', $class);
				$file = 'class/'.$folder.'/'.substr($class, strlen($folder) + 1);
			} else {
				$class = str_replace('\\', '/', $class);
				$_rp = strrpos($class, '/');
				$plugin = substr($class, 0, $_rp);
				$class = substr($class, $_rp + 1);
				[$folder] = explode('_', $class);
				$pathfile = DISCUZ_PLUGIN($plugin).'/'.$folder.'/'.$class.'.php';
				if(!file_exists($pathfile)) {
					$pathfile = MITFRAME_APP($plugin).'/'.$folder.'/'.$class.'.php';
					if(!file_exists($pathfile)) {
						return false;
					}
				}
			}
		} else {
			$file = 'class/'.$class;
		}

		try {

			if($pathfile) {
				include $pathfile;
			} else {
				self::import($file);
			}
			return true;

		} catch (Exception $exc) {

			$trace = $exc->getTrace();
			foreach($trace as $log) {
				if(empty($log['class']) && $log['function'] == 'class_exists') {
					return false;
				}
			}
			discuz_error::exception_error($exc);
		}
	}

	public static function analysisStart($name) {
		$key = 'other';
		if($name[0] === '#') {
			[, $key, $name] = explode('#', $name);
		}
		if(!isset($_ENV['analysis'])) {
			$_ENV['analysis'] = [];
		}
		if(!isset($_ENV['analysis'][$key])) {
			$_ENV['analysis'][$key] = [];
			$_ENV['analysis'][$key]['sum'] = 0;
		}
		$_ENV['analysis'][$key][$name]['start'] = microtime(TRUE);
		$_ENV['analysis'][$key][$name]['start_memory_get_usage'] = memory_get_usage();
		$_ENV['analysis'][$key][$name]['start_memory_get_real_usage'] = memory_get_usage(true);
		$_ENV['analysis'][$key][$name]['start_memory_get_peak_usage'] = memory_get_peak_usage();
		$_ENV['analysis'][$key][$name]['start_memory_get_peak_real_usage'] = memory_get_peak_usage(true);
	}

	public static function analysisStop($name) {
		$key = 'other';
		if($name[0] === '#') {
			[, $key, $name] = explode('#', $name);
		}
		if(isset($_ENV['analysis'][$key][$name]['start'])) {
			$diff = round((microtime(TRUE) - $_ENV['analysis'][$key][$name]['start']) * 1000, 5);
			$_ENV['analysis'][$key][$name]['time'] = $diff;
			$_ENV['analysis'][$key]['sum'] = $_ENV['analysis'][$key]['sum'] + $diff;
			unset($_ENV['analysis'][$key][$name]['start']);
			$_ENV['analysis'][$key][$name]['stop_memory_get_usage'] = memory_get_usage();
			$_ENV['analysis'][$key][$name]['stop_memory_get_real_usage'] = memory_get_usage(true);
			$_ENV['analysis'][$key][$name]['stop_memory_get_peak_usage'] = memory_get_peak_usage();
			$_ENV['analysis'][$key][$name]['stop_memory_get_peak_real_usage'] = memory_get_peak_usage(true);
		}
		return $_ENV['analysis'][$key][$name];
	}
}

class C extends core {
}

class DB extends discuz_database {
}

