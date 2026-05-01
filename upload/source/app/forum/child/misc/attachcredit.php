<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_GET['formhash'] != FORMHASH || !$_G['uid']) {
	showmessage('undefined_action', NULL);
}

$aid = intval($_GET['aid']);

$attach = table_forum_attachment_n::t()->fetch_attachment('aid:'.$aid, $aid);
$thread = table_forum_thread::t()->fetch_by_tid_displayorder($attach['tid'], 0);

checklowerlimit('getattach', 0, 1, $thread['fid']);
$getattachcredits = updatecreditbyaction('getattach', $_G['uid'], [], '', 1, 1, $thread['fid']);
$_G['policymsg'] = $p = '';
if($getattachcredits['updatecredit']) {
	if($getattachcredits['updatecredit']) for($i = 1; $i <= 8; $i++) {
		if($policy = $getattachcredits['extcredits'.$i]) {
			$_G['policymsg'] .= $p.($_G['setting']['extcredits'][$i]['img'] ? $_G['setting']['extcredits'][$i]['img'].' ' : '').$_G['setting']['extcredits'][$i]['title'].' '.$policy.' '.$_G['setting']['extcredits'][$i]['unit'];
			$p = ', ';
		}
	}
}

$ck = substr(md5($aid.TIMESTAMP.md5($_G['config']['security']['authkey'])), 0, 8);
$aidencode = aidencode($aid, 0, $attach['tid']);
showmessage('attachment_credit', "forum.php?mod=attachment&aid=$aidencode&ck=$ck", ['policymsg' => $_G['policymsg'], 'filename' => $attach['filename']], ['redirectmsg' => 1, 'login' => 1]);
	