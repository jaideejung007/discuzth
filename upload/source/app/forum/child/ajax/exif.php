<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$exif = table_forum_attachment_exif::t()->fetch($_GET['aid']);
$s = $exif['exif'];
if(!$s) {
	require_once libfile('function/attachment');
	$s = getattachexif($_GET['aid']);
	table_forum_attachment_exif::t()->insert_exif($_GET['aid'], $s);
}
include template('common/header_ajax');
echo $s;
include template('common/footer_ajax');
exit;
	