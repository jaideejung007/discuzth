<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!preg_match('/cli/i', php_sapi_name())) {
	exit;
}

// 添加系统计划任务，每隔1分钟执行一次
// */1 * * * * [php_path]php [discuz_path]api/remote/cron.php
// [php_path] 和 [discuz_path] 自行补充
//
// 修改 config_global.php
// $_config['remote']['on'] = 1;
// $_config['remote']['cron'] = 1;

const IN_API = true;
const CURSCRIPT = 'phpcli_cron';
const APPTYPEID = 200;
const DISABLEDEFENSE = true;

$_ENV['remote'] = new discuz_remote();
$_ENV['remote']->init();
$_ENV['remote']->loadservice();

class discuz_remote {

	var $mod;

	var $core;

	function init() {

		$path = dirname(__DIR__, 2);
		require_once($path.'/source/class/class_core.php');

		$cachelist = [];
		$this->core = C::app();

		$this->core->cachelist = $cachelist;


		$this->core->init_setting = true;

		$this->core->init_cron = false;
		$this->core->init_user = false;
		$this->core->init_session = false;
		$this->core->init_misc = false;
		$this->core->init_mobile = false;

		$this->core->init();

		define('SERVICE_DIR', getglobal('config/remote/dir') ? getglobal('config/remote/dir') : 'remote');
		$this->core->reject_robot();
	}

	function loadservice() {

		if(!$this->core->config['remote']['on']) {
			$this->error(1, 'remote service is down');
		}

		$this->mod = 'cron';

		$modfile = DISCUZ_ROOT.'./api/'.SERVICE_DIR.'/mod/mod_'.$this->mod.'.php';
		if(!is_file($modfile)) {
			$this->error(3, 'mod file is missing');
		}

		require $modfile;
		$classname = 'mod_'.$this->mod;
		if(class_exists($classname)) {
			$service = new $classname;
			$service->run();
		}
	}

	function error($code, $msg) {
		$code = sprintf('%04d', $code);
		echo $code.':'.ucfirst($msg);
		exit();
	}

}

class remote_service {

	var $version = '2.0.0';
	var $config;

	function __construct() {
		$this->config = getglobal('config/remote');
	}

	function run() {
		remote_service::success('service is done.');
	}

	function error($code, $msg) {
		$code = sprintf('%04d', $code);
		echo $code.':'.ucfirst($msg);
		exit();
	}

	function success($msg) {
		remote_service::error(0, $msg);
	}

}

