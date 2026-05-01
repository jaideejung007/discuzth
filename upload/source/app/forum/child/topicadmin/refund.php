<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowrefund'] || $thread['price'] <= 0) {
	showmessage('undefined_action', NULL);
}

if(!isset($_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]])) {
	showmessage('credits_transaction_disabled');
}

if($thread['special'] != 0) {
	showmessage('special_refundment_invalid');
}

if(!submitcheck('modsubmit')) {

	$payment = table_common_credit_log::t()->count_stc_by_relatedid($_G['tid'], $_G['setting']['creditstransextra'][1]);
	$payment['payers'] = intval($payment['payers']);
	$payment['income'] = intval($payment['income']);

	include template('forum/topicadmin_action');

} else {

	$modaction = 'RFD';
	$modpostsnum++;

	$reason = checkreasonpm();

	$totalamount = 0;
	$amountarray = [];

	$logarray = [];
	foreach(table_common_credit_log::t()->fetch_all_by_uid_operation_relatedid(0, 'BTC', $_G['tid']) as $log) {
		$amount = abs($log['extcredits'.$_G['setting']['creditstransextra'][1]]);
		$totalamount += $amount;
		$amountarray[$amount][] = $log['uid'];
	}

	updatemembercount($thread['authorid'], [$_G['setting']['creditstransextra'][1] => -$totalamount]);
	table_forum_thread::t()->update($_G['tid'], ['price' => -1, 'moderated' => 1]);

	foreach($amountarray as $amount => $uidarray) {
		updatemembercount($uidarray, [$_G['setting']['creditstransextra'][1] => $amount]);
	}

	table_common_credit_log::t()->delete_by_operation_relatedid(['BTC', 'STC'], $_G['tid']);

	$resultarray = [
		'redirect' => "forum.php?mod=viewthread&tid={$_G['tid']}",
		'reasonpm' => ($sendreasonpm ? ['data' => [$thread], 'var' => 'thread', 'item' => 'reason_moderate', 'notictype' => 'post'] : []),
		'reasonvar' => ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason],
		'modtids' => $thread['tid'],
		'modlog' => $thread
	];

}

