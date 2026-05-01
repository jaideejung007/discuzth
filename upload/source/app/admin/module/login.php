<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($this->core->var['inajax']) {
	ajaxshowheader();
	ajaxshowfooter();
}

global $_G;
$charset = CHARSET;
$cptitle = !empty($_G['cache']['admin']['platform'][PLATFORM]['name']) ? $_G['cache']['admin']['platform'][PLATFORM]['name'] : lang('admincp_login', 'admincp_title', ['bbname' => $_G['setting']['bbname']]);
$title = lang('admincp_login', 'login_title');
$tips = !empty($_G['cache']['admin']['platform'][PLATFORM]['desc']) ? $_G['cache']['admin']['platform'][PLATFORM]['desc'] : lang('admincp_login', 'login_tips');
$staticurl = STATICURL;
$light_mode = lang('admincp_login', 'login_dk_light_mode');
$by_system = lang('admincp_login', 'login_dk_by_system');
$normal_mode = lang('admincp_login', 'login_dk_normal_mode');
$dark_mode = lang('admincp_login', 'login_dk_dark_mode');

$mustlogin = getglobal('config/admincp/mustlogin');
[$uid, $username] = get_userinfo();
$lang = lang('admincp_login');
$sid = getglobal('sid');
$formhash = getglobal('formhash');
$_SERVER['QUERY_STRING'] = str_replace('&amp;', '&', dhtmlspecialchars($_SERVER['QUERY_STRING']));
$extra = ADMINSCRIPT.'?'.(getgpc('action') && getgpc('frames') ? 'frames=yes&' : '').$_SERVER['QUERY_STRING'];
$forcesecques = '<option value="0">'.($_G['config']['admincp']['forcesecques'] || $_G['group']['forcesecques'] ? $lang['forcesecques'] : $lang['security_question_0']).'</option>';

$version = getglobal('setting/version');
$cookiepre = getglobal('config/cookie/cookiepre');
$copy = lang('admincp_login', 'copyright');

$cpaccess = $this->cpaccess;

require_once template('admin/login');

exit();

function get_userinfo() {
	if(getglobal('config/admincp/mustlogin')) {
		return [getglobal('uid'), getglobal('member/username')];
	}
	$user = site_userinfo();
	if($user) {
		return [$user['uid'], $user['username']];
	}

	return [0, ''];
}

