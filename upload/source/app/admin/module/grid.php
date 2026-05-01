<?php
/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
cpheader();

if(!submitcheck('gridssubmit')) {
	require_once childfile('grid/form');
} else {
	require_once childfile('grid/submit');
}
