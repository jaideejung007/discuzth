<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!isset($_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]])) {
	showmessage('credits_transaction_disabled');
} elseif($thread['price'] <= 0 || $thread['special'] <> 0) {
	showmessage('thread_pay_error', NULL);
} elseif(!$_G['uid']) {
	showmessage('group_nopermission', NULL, ['grouptitle' => $_G['group']['grouptitle']], ['login' => 1]);
}

if(($balance = getuserprofile('extcredits'.$_G['setting']['creditstransextra'][1]) - $thread['price']) < ($minbalance = 0)) {
	if($_G['setting']['creditstrans'][0] == $_G['setting']['creditstransextra'][1]) {
		showmessage('credits_balance_insufficient_and_charge', '', ['title' => $_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]]['title'], 'minbalance' => $thread['price']]);
	} else {
		showmessage('credits_balance_insufficient', '', ['title' => $_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]]['title'], 'minbalance' => $thread['price']]);
	}
}

if(table_common_credit_log::t()->count_by_uid_operation_relatedid($_G['uid'], 'BTC', $_G['tid'])) {
	showmessage('credits_buy_thread', 'forum.php?mod=viewthread&tid='.$_G['tid'].($_GET['from'] ? '&from='.$_GET['from'] : ''));
}

$thread['netprice'] = floor($thread['price'] * (1 - $_G['setting']['creditstax']));

if(!submitcheck('paysubmit')) {

	if(empty($thread['author'])) {
		if($_G['forum']['ismoderator']) {
			$authorinfo = getuserbyuid($thread['authorid']);
			$thread['author'] = $authorinfo['username'];
		} else {
			$thread['authorid'] = 0;
			$thread['author'] = $_G['setting']['anonymoustext'];
		}
	}
	include template('forum/pay');

} else {

	$updateauthor = true;
	$authorEarn = $thread['netprice'];
	if($_G['setting']['maxincperthread'] > 0) {
		$extcredit = 'extcredits'.$_G['setting']['creditstransextra'][1];
		$log = table_common_credit_log::t()->count_credit_by_uid_operation_relatedid($thread['authorid'], 'STC', $_G['tid'], $_G['setting']['creditstransextra'][1]);
		if($log >= $_G['setting']['maxincperthread']) {
			$updateauthor = false;
		} else {
			$authorEarn = min($_G['setting']['maxincperthread'] - $log, $thread['netprice']);
		}
	}
	if($updateauthor) {
		updatemembercount($thread['authorid'], [$_G['setting']['creditstransextra'][1] => $authorEarn], 1, 'STC', $_G['tid']);
	}
	updatemembercount($_G['uid'], [$_G['setting']['creditstransextra'][1] => -$thread['price']], 1, 'BTC', $_G['tid']);

	showmessage('thread_pay_succeed', "forum.php?mod=viewthread&tid={$_G['tid']}".($_GET['from'] ? '&from='.$_GET['from'] : ''));

}
	