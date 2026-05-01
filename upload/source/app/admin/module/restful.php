<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($admincp) || !is_object($admincp) || !$admincp->isfounder) {
	exit('Access Denied');
}

require_once childfile('restful/class');

cpheader();

shownav('founder', 'menu_founder_restful');

$operation = $operation ? $operation : 'list';

switch($operation) {
	case 'list':
		rp::showMenu($operation);
		rp::list();
		break;
	case 'add':
		rp::showMenu($operation);
		rp::add();
		break;
	case 'view':
		if(str_starts_with($_GET['id'], 'system:')) {
			rp::viewSystem();
		} else {
			rp::view();
		}
		break;
	case 'appList':
		rp::showMenu($operation);
		rp::appList();
		break;
	case 'appAdd':
		rp::appAdd();
		break;
	case 'app':
		rp::app();
		break;
	case 'stat':
		rp::stat();
		break;
}