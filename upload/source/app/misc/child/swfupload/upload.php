<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(empty($_GET['simple'])) {
	$_FILES['Filedata']['name'] = diconv(urldecode($_FILES['Filedata']['name']), 'UTF-8');
	$_FILES['Filedata']['type'] = $_GET['filetype'];
}else{
	$_FILES['Filedata']['name'] = urldecode($_FILES['Filedata']['name']);
}
$forumattachextensions = '';
$fid = intval($_GET['fid']);
if($fid) {
	$forum = $fid != $_G['fid'] ? table_forum_forum::t()->fetch_info_by_fid($fid) : $_G['forum'];
	if($forum['status'] == 3 && $forum['level']) {
		$levelinfo = table_forum_grouplevel::t()->fetch($forum['level']);
		if($postpolicy = $levelinfo['postpolicy']) {
			$postpolicy = dunserialize($postpolicy);
			$forumattachextensions = $postpolicy['attachextensions'];
		}
	} else {
		$forumattachextensions = $forum['attachextensions'];
	}
	if($forumattachextensions) {
		$_G['group']['attachextensions'] = $forumattachextensions;
	}
}
$ftpcmd = !empty($_GET['simple']) || $_G['setting']['ftp']['on'] == 2 ? 1 : 0;
$upload = new forum_upload(0, $ftpcmd, $_GET['thumbBase64']);
	