<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$allowmanage = $allowadd = 0;
if($_G['group']['allowaddtopic'] || $_G['group']['allowmanagetopic']) {
	$allowadd = 1;
}

$op = $_GET['op'] == 'edit' ? $_GET['op'] : 'add';

$topicid = $_GET['topicid'] ? intval($_GET['topicid']) : 0;
$topic = '';
if($topicid) {
	$topic = table_portal_topic::t()->fetch($topicid);
	if(empty($topic)) {
		showmessage('topic_not_exist');
	}
	if($_G['group']['allowmanagetopic'] || ($_G['group']['allowaddtopic'] && $topic['uid'] == $_G['uid'])) {
		$allowmanage = 1;
	}
	$coverpath = $topic['picflag'] == '0' ? $topic['cover'] : '';

	if($topic['cover']) {
		if($topic['picflag'] == '1') {
			$topic['cover'] = $_G['setting']['attachurl'].$topic['cover'];
		} elseif($topic['picflag'] == '2') {
			$topic['cover'] = $_G['setting']['ftp']['attachurl'].$topic['cover'];
		}
	}
}

if(($topicid && !$allowmanage) || (!$topicid && !$allowadd)) {
	showmessage('topic_edit_nopermission', dreferer());
}

$tpls = [];

foreach($alltemplate = table_common_template::t()->range() as $template) {
	if(($dir = dir(DISCUZ_TEMPLATE($template['directory']).'/portal/'))) {
		while(false !== ($file = $dir->read())) {
			$file = strtolower($file);
			if((fileext($file) == 'htm' || fileext($file) == 'php') && str_starts_with($file, 'portal_topic_')) {
				$tpls[$template['directory'].':portal/'.str_replace('.htm', '', $file)] = getprimaltplname($template['directory'].':portal/'.$file);
			}
		}
	}
}

if(empty($tpls)) showmessage('topic_has_on_template', dreferer());

if(submitcheck('editsubmit')) {
	include_once libfile('function/portalcp');
	if(is_numeric($topicid = updatetopic($topic))) {
		showmessage('do_success', 'portal.php?mod=topic&topicid='.$topicid);
	} else {
		showmessage($topicid, dreferer());
	}
}

include_once template('portal/portalcp_topic');


