<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_GET['action'] == 'pluginnotice') {

	require_once childfile('pluginnotice');

} elseif($_GET['action'] == 'ipnotice') {

	require_once childfile('ipnotice');

}
