<?php

/**
 * [UCenter] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

!defined('IN_UC') && exit('Access Denied');

class cachemodel {

	var $db;
	var $base;
	var $map;

	function __construct(&$base) {
		$this->cachemodel($base);
	}

	function cachemodel(&$base) {
		$this->base = $base;
		$this->db = $base->db;
		$this->map = [
			'settings' => ['settings'],
			'badwords' => ['badwords'],
			'apps' => ['apps'],
		];
	}

	function updatedata($cachefile = '') {
		if($cachefile) {
			foreach((array)$this->map[$cachefile] as $modules) {
				$this->_write_cache($cachefile, $modules);
			}
		} else {
			foreach((array)$this->map as $file => $modules) {
				$this->_write_cache($file, $modules);
			}
		}
	}

	function _write_cache($name, $modules) {
		if(function_exists('memory') && memory('check')) {
			$_CACHE = [];
			foreach((array)$modules as $m) {
				$method = "_get_$m";
				$_CACHE[$m] = $this->$method();
			}
			memory('set', 'uccache_'.$name, $_CACHE);
			return;
		}
		$s = "<?php\r\n!defined('IN_UC') && exit('Access Denied');\n";
		foreach((array)$modules as $m) {
			$method = "_get_$m";
			$s .= '$_CACHE[\''.$m.'\'] = '.var_export($this->$method(), TRUE).";\r\n";
		}
		$s .= "\r\n?>";
		file_put_contents(UC_DATADIR.'./cache/'.$name.'.php', $s, LOCK_EX);
	}

	function updatetpl() {

	}

	function _get_badwords() {
		$data = $this->db->fetch_all('SELECT * FROM '.UC_DBTABLEPRE.'badwords');
		$return = [];
		if(is_array($data)) {
			foreach($data as $k => $v) {
				$return['findpattern'][$k] = $v['findpattern'];
				$return['replace'][$k] = $v['replacement'];
			}
		}
		return $return;
	}

	function _get_apps() {
		$this->base->load('app');
		$apps = $_ENV['app']->get_apps();
		$apps2 = [];
		if(is_array($apps)) {
			foreach($apps as $v) {
				if(!empty($v['extra'])) {
					$v['extra'] = is_array($v['extra']) ? $v['extra'] : unserialize($v['extra']);
				}
				$apps2[$v['appid']] = $v;
			}
		}
		return $apps2;
	}

	function _get_settings() {
		return $this->base->get_setting();
	}

}

