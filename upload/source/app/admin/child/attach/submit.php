<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($_GET['delete']) {

	$tids = $pids = [];
	for($attachi = 0; $attachi < 10; $attachi++) {
		foreach(table_forum_attachment_n::t()->fetch_all_attachment($attachi, $_GET['delete']) as $attach) {
			dunlink($attach);
			$tids[$attach['tid']] = $attach['tid'];
			$pids[$attach['pid']] = $attach['pid'];
		}
		table_forum_attachment_n::t()->delete_attachment($attachi, $_GET['delete']);

		$attachtids = [];
		foreach(table_forum_attachment_n::t()->fetch_all_by_id($attachi, 'tid', $tids) as $attach) {
			unset($tids[$attach['tid']]);
		}
		if($tids) {
			table_forum_thread::t()->update($tids, ['attachment' => 0]);
		}

		$attachpids = [];
		foreach(table_forum_attachment_n::t()->fetch_all_by_id($attachi, 'pid', $pids) as $attach) {
			$attachpids[$attach['pid']] = $attach['pid'];
		}
	}

	if($attachpids) {
		$pids = array_diff($pids, $attachpids);
	}
	loadcache('posttableids');
	$posttableids = $_G['cache']['posttableids'] ? $_G['cache']['posttableids'] : ['0'];
	foreach($posttableids as $id) {
		table_forum_post::t()->update_post($id, $pids, ['attachment' => '0']);
	}

	$cpmsg = cplang('attach_edit_succeed');

} else {

	$cpmsg = cplang('attach_edit_invalid');

}

echo "<script type=\"text/JavaScript\">alert('$cpmsg');parent.\$('attachmentforum').searchsubmit.click();</script>";
	