<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$ac = in_array(getgpc('ac'), ['comment', 'article', 'related', 'block', 'portalblock', 'blockdata', 'topic', 'diy', 'upload', 'category', 'plugin', 'logout']) ? $_GET['ac'] : 'index';

if(!$_G['setting']['portalstatus'] && !in_array($ac, ['index', 'block', 'portalblock', 'blockdata', 'diy', 'logout'])) {
	showmessage('portal_status_off');
}

$_G['disabledwidthauto'] = 0;

$admincp2 = getstatus($_G['member']['allowadmincp'], 2);
$admincp3 = getstatus($_G['member']['allowadmincp'], 3);
$admincp4 = getstatus($_G['member']['allowadmincp'], 4);
$admincp5 = getstatus($_G['member']['allowadmincp'], 5);
$admincp6 = getstatus($_G['member']['allowadmincp'], 6);

if(!$_G['inajax'] && in_array($ac, ['index', 'portalblock', 'blockdata', 'category', 'plugin']) && ($_G['group']['allowdiy'] || $_G['group']['allowmanagearticle'] || $admincp2 || $admincp3 || $admincp4 || $admincp6)) {
	$modsession = new discuz_panel(PORTALCP_PANEL);
	if(getgpc('login_panel') && getgpc('cppwd') && submitcheck('submit')) {
		$modsession->dologin($_G['uid'], getgpc('cppwd'), true);
	}

	if(!$modsession->islogin) {
		include template('portal/portalcp_login');
		dexit();
	}
}

if($ac == 'logout') {
	$modsession = new discuz_panel(PORTALCP_PANEL);
	$modsession->dologout();
	showmessage('modcp_logout_succeed', 'index.php');
}

$navtitle = lang('core', 'title_'.$ac.'_management').' - '.lang('core', 'title_portal_management');

require_once libfile('function/portalcp');
require_once childfile($ac);
