<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$message = '&nbsp;';
$savepost = table_forum_post::t()->fetch_post(0, $_GET['pid']);
if($savepost && $_G['uid'] == $savepost['authorid']) {
	$message = $savepost['message'];
	if($_GET['type']) {
		require_once libfile('function/discuzcode');
		$message = discuzcode($message, $savepost['smileyoff'], $savepost['bbcodeoff'], $savepost['htmlon']);
	}
	$message = $message ? $message : '&nbsp;';
}
include template('common/header_ajax');
echo $message;
include template('common/footer_ajax');
exit;
	