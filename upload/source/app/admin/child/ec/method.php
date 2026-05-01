<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

[$plugin, $class] = explode(':', $_GET['id']);

if(!in_array($plugin, $_G['setting']['plugins']['available'])) {
	cpmsg('undefined_action');
}

$class_name = $plugin.'\\admin\\payment_'.$class;
if(!class_exists($class_name) || !method_exists($class_name, 'admincp')) {
	cpmsg('undefined_action');
}
$c = new $class_name();

$c->admincp();