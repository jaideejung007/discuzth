<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('MITFRAME_APP')) {
	exit('Access Denied');
}

const APPTYPEID = 2;

require './source/class/class_core.php';


require './source/function/function_forum.php';


$modarray = ['ajax', 'announcement', 'attachment', 'forumdisplay',
	'group', 'image', 'index', 'misc', 'modcp', 'post', 'redirect',
	'rss', 'topicadmin', 'trade', 'viewthread', 'tag', 'collection', 'guide', 'member'
];

$modcachelist = [
	'index' => ['announcements', 'onlinelist', 'forumlinks',
		'heats', 'historyposts', 'onlinerecord', 'userstats', 'diytemplatenameforum'],
	'forumdisplay' => ['smilies', 'announcements_forum', 'globalstick', 'forums',
		'onlinelist', 'forumstick', 'threadtable_info', 'threadtableids', 'stamps', 'diytemplatenameforum'],
	'viewthread' => ['smilies', 'smileytypes', 'forums', 'usergroups',
		'stamps', 'bbcodes', 'smilies', 'custominfo', 'groupicon', 'stamps',
		'threadtableids', 'threadtable_info', 'posttable_info', 'diytemplatenameforum'],
	'redirect' => ['threadtableids', 'threadtable_info', 'posttable_info'],
	'post' => ['bbcodes_display', 'bbcodes', 'smileycodes', 'smilies', 'smileytypes',
		'domainwhitelist', 'albumcategory'],
	'space' => ['fields_required', 'fields_optional', 'custominfo'],
	'group' => ['grouptype', 'diytemplatenamegroup'],
	'topicadmin' => ['usergroups'],
];

$mod = !in_array(C::app()->var['mod'], $modarray) ? 'index' : C::app()->var['mod'];

define('CURMODULE', $mod);
$cachelist = [];
if(isset($modcachelist[CURMODULE])) {
	$cachelist = $modcachelist[CURMODULE];

	$cachelist[] = 'plugin';
	$cachelist[] = 'pluginlanguage_system';
}
if(C::app()->var['mod'] == 'group') {
	$_G['basescript'] = 'group';
}

C::app()->cachelist = $cachelist;
C::app()->init();

loadforum();

set_rssauth();

runhooks();

if(!$_G['setting']['forumstatus'] && !in_array($mod, ['ajax', 'misc', 'modcp'])) {
	showmessage('forum_status_off');
}

$navtitle = str_replace('{bbname}', $_G['setting']['bbname'], $_G['setting']['seotitle']['forum']);
$_G['setting']['threadhidethreshold'] = 1;

require_once appfile('module/'.$mod);

