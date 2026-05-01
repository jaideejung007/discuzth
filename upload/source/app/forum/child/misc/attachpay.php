<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$aid = intval($_GET['aid']);
if(!$aid) {
	showmessage('parameters_error');
} elseif(!isset($_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]])) {
	showmessage('credits_transaction_disabled');
} elseif(!$_G['uid']) {
	showmessage('group_nopermission', NULL, ['grouptitle' => $_G['group']['grouptitle']], ['login' => 1]);
} else {
	$attachtable = !empty($_GET['tid']) ? 'tid:'.dintval($_GET['tid']) : 'aid:'.$aid;
	$attach = table_forum_attachment_n::t()->fetch_attachment($attachtable, $aid);
	$attachmember = getuserbyuid($attach['uid']);
	$attach['author'] = $attachmember['username'];
	if($attach['price'] <= 0) {
		showmessage('undefined_action');
	}
}

if($attach['readperm'] && $attach['readperm'] > $_G['group']['readaccess']) {
	showmessage('attachment_forum_nopermission', NULL, [], ['login' => 1]);
}

$balance = getuserprofile('extcredits'.$_G['setting']['creditstransextra'][1]);
$status = $balance < $attach['price'] ? 1 : 0;

if($_G['adminid'] == 3) {
	$fid = table_forum_thread::t()->fetch_thread($attach['tid']);
	$fid = $fid['fid'];
	$ismoderator = table_forum_moderator::t()->fetch_uid_by_fid_uid($fid, $_G['uid']);
} elseif(in_array($_G['adminid'], [1, 2])) {
	$ismoderator = 1;
} else {
	$ismoderator = 0;
}
$exemptvalue = $ismoderator ? 64 : 8;
if($_G['uid'] == $attach['uid'] || $_G['group']['exempt'] & $exemptvalue) {
	$status = 2;
} else {
	$payrequired = $_G['uid'] ? !table_common_credit_log::t()->count_by_uid_operation_relatedid($_G['uid'], 'BAC', $attach['aid']) : 1;
	$status = $payrequired ? $status : 2;
}
$balance = $status != 2 ? $balance - $attach['price'] : $balance;

$sidauth = rawurlencode(authcode($_G['sid'], 'ENCODE', $_G['authkey']));

$aidencode = aidencode($aid, 0, $attach['tid']);

if(table_common_credit_log::t()->count_by_uid_operation_relatedid($_G['uid'], 'BAC', $aid)) {
	showmessage('attachment_yetpay', "forum.php?mod=attachment&aid=$aidencode", [], ['redirectmsg' => 1]);
}

$attach['netprice'] = $status != 2 ? round($attach['price'] * (1 - $_G['setting']['creditstax'])) : 0;
$lockid = 'attachpay_'.$_G['uid'];
if(!submitcheck('paysubmit')) {
	$post = table_forum_post::t()->fetch('tid:'.$attach['tid'], $attach['pid']);
	if($post['anonymous'] && !$_G['forum']['ismoderator']) {
		$attach['uid'] = 0;
		$attach['author'] = $_G['setting']['anonymoustext'];
	}
	include template('forum/attachpay');
} elseif(!discuz_process::islocked($lockid)) {
	if(!empty($_GET['buyall'])) {
		$aids = $prices = [];
		$tprice = 0;
		foreach(table_forum_attachment_n::t()->fetch_all_by_id('aid:'.$aid, 'pid', $attach['pid'], '', false, true) as $tmp) {
			$aids[$tmp['aid']] = $tmp['aid'];
			$prices[$tmp['aid']] = $status != 2 ? [$tmp['price'], round($tmp['price'] * (1 - $_G['setting']['creditstax']))] : [0, 0];
		}
		if($aids) {
			foreach(table_common_credit_log::t()->fetch_all_by_uid_operation_relatedid($_G['uid'], 'BAC', $aids) as $tmp) {
				unset($aids[$tmp['relatedid']]);
			}
		}
		foreach($aids as $aid) {
			$tprice += $prices[$aid][0];
		}
		$status = getuserprofile('extcredits'.$_G['setting']['creditstransextra'][1]) < $tprice ? 1 : 0;
	} else {
		$aids = [$aid];
		$prices[$aid] = $status != 2 ? [$attach['price'], $attach['netprice']] : [0, 0];
	}

	if($status == 1) {
		discuz_process::unlock($lockid);
		showmessage('credits_balance_insufficient', '', ['title' => $_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]]['title'], 'minbalance' => (empty($_GET['buyall']) ? $attach['price'] : $tprice)]);
	}
	foreach($aids as $aid) {
		$updateauthor = 1;
		$authorEarn = $prices[$aid][1];
		if($_G['setting']['maxincperthread'] > 0) {
			$extcredit = 'extcredits'.$_G['setting']['creditstransextra'][1];
			$alog = table_common_credit_log::t()->count_credit_by_uid_operation_relatedid($attach['uid'], 'SAC', $aid, $_G['setting']['creditstransextra'][1]);
			if($alog >= $_G['setting']['maxincperthread']) {
				$updateauthor = 0;
			} else {
				$authorEarn = min($_G['setting']['maxincperthread'] - $alog, $prices[$aid][1]);
			}
		}
		if($updateauthor) {
			updatemembercount($attach['uid'], [$_G['setting']['creditstransextra'][1] => $authorEarn], 1, 'SAC', $aid);
		}
		updatemembercount($_G['uid'], [$_G['setting']['creditstransextra'][1] => -$prices[$aid][0]], 1, 'BAC', $aid);

		$aidencode = aidencode($aid, 0, $_GET['tid']);
	}
	discuz_process::unlock($lockid);
	if(defined('IN_MOBILE')) {
		showmessage('attachment_mobile_buy', 'forum.php?mod=redirect&goto=findpost&ptid='.$attach['tid'].'&pid='.$attach['pid']);
	} else {
		if(count($aids) > 1) {
			showmessage('attachment_buyall', 'forum.php?mod=redirect&goto=findpost&ptid='.$attach['tid'].'&pid='.$attach['pid']);
		} else {
			$_G['forum_attach_filename'] = $attach['filename'];
			showmessage('attachment_buy', "forum.php?mod=attachment&aid=$aidencode", ['filename' => $_G['forum_attach_filename']], ['redirectmsg' => 1]);
		}
	}
} else {
	showmessage('attachment_locked');
}
	