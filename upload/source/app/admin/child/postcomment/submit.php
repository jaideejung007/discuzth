<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$cids = authcode($cids, 'DECODE');
$cidsadd = $cids ? explode(',', $cids) : $_GET['delete'];
$pids = [];
foreach(table_forum_postcomment::t()->fetch_all($cidsadd) as $postcomment) {
	$pids[$postcomment['pid']] = $postcomment['pid'];
}
table_forum_postcache::t()->delete($pids);
$cidsadd && table_forum_postcomment::t()->delete($cidsadd);
$cpmsg = cplang('postcomment_delete');

echo '<script type="text/JavaScript">alert(\''.$cpmsg.'\');parent.$(\'postcommentforum\').searchsubmit.click();</script>';
	