<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowrepairthread']) {
	showmessage('no_privilege_repairthread');
}

$posttable = getposttablebytid($_G['tid']);

$replies = table_forum_post::t()->count_visiblepost_by_tid($_G['tid']) - 1;

$attachcount = table_forum_attachment_n::t()->count_by_id('tid:'.$_G['tid'], 'tid', $_G['tid']);
$attachment = $attachcount ? (table_forum_attachment_n::t()->count_image_by_id('tid:'.$_G['tid'], 'tid', $_G['tid']) ? 2 : 1) : 0;

$firstpost = table_forum_post::t()->fetch_visiblepost_by_tid('tid:'.$_G['tid'], $_G['tid'], 0);
$firstpost['subject'] = addslashes(cutstr($firstpost['subject'], 79));
$firstpost['rate'] = intval(abs($firstpost['rate']) ? ($firstpost['rate'] / abs($firstpost['rate'])) : 0);

$lastpost = table_forum_post::t()->fetch_visiblepost_by_tid('tid:'.$_G['tid'], $_G['tid'], 0, 1);

table_forum_thread::t()->update($_G['tid'], ['subject' => $firstpost['subject'], 'replies' => $replies, 'lastpost' => $lastpost['dateline'], 'lastposter' => $lastpost['author'], 'rate' => $firstpost['rate'], 'attachment' => $attachment], true);
table_forum_post::t()->update_by_tid('tid:'.$_G['tid'], $_G['tid'], ['first' => 0], true);
table_forum_post::t()->update_post('tid:'.$_G['tid'], $firstpost['pid'], ['first' => 1, 'subject' => $firstpost['subject']], true);

showmessage('admin_repair_succeed', '', [], ['alert' => 'right']);

