<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

switch($_GET['share']) {
	case 'tid':
		require_once './source/function/function_forum.php';
		require_once './source/function/function_post.php';

		loadforum();

		$firstpost = table_forum_post::t()->fetch_threadpost_by_tid_invisible($_G['tid']);
		if(!$firstpost) {
			echo '{}';
			exit;
		}

		$aid = 0;
		$attachments = table_forum_attachment_n::t()->fetch_all_by_id('pid:'.$firstpost['pid'], 'pid', [$firstpost['pid']]);
		$maxWidth = 0;
		foreach($attachments as $attachment) {
			if($attachment['isimage'] && $maxWidth < $attachment['width']) {
				$maxWidth = $attachment['width'];
				$aid = $attachment['aid'];
			}
		}
		echo json_encode([
			'param' => [
				'title' => $_G['forum_thread']['subject'],
				'desc' => messagecutstr($firstpost['message'], 100),
				'link' => $_G['siteurl'].'forum.php?mod=viewthread&tid='.$_G['tid'],
				'imgUrl' => $aid ? $_G['siteurl'].'forum.php?mod=attachment&aid='.aidencode($aid, 0, $_G['tid']) : '',
				'enableIdTrans' => 0,
			],
		]);
		break;
}
