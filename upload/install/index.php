<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

error_reporting(E_ERROR | E_PARSE);
@set_time_limit(1000);

define('IN_DISCUZ', true);
define('ROOT_PATH', dirname(__DIR__).'/');
define('INST_LOG_PATH', realpath(ROOT_PATH.'data/log/').'/install.log');
define('DISCUZ_DATA', ROOT_PATH.'./data');

define('_FILE_', basename(__FILE__));
if(basename(dirname(__FILE__)) != 'install') {
	exit('method undefined');
}
define('RUN_MODE', _FILE_ == 'index.php' ? 'install' : 'tool');

require ROOT_PATH.'./source/discuz_version.php';
require ROOT_PATH.'./source/mitframe_version.php';
require ROOT_PATH.'./install/include/install_var.php';
require ROOT_PATH.'./install/include/install_mysqli.php';
require ROOT_PATH.'./install/include/install_function.php';
set_lang();
if(!file_exists($_langfile = ROOT_PATH.'./source/i18n/'.INSTALL_LANG.'/install/lang_install.php')) {
	exit('language undefined');
}

require $_langfile;

$view_off = getgpc('view_off');
define('VIEW_OFF', (bool)$view_off);
timezone_set();

header('Content-Type: text/html; charset='.CHARSET);

require ROOT_PATH.'./install/include/install_run_'.RUN_MODE.'.php';