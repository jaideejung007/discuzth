<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/ec_credit');

if($_GET['op'] == 'list') {

	$from = !empty($_GET['from']) && in_array($_GET['from'], ['buyer', 'seller', 'myself']) ? $_GET['from'] : '';
	$uid = !empty($_GET['uid']) ? intval($_GET['uid']) : $_G['uid'];


	$filter = !empty($_GET['filter']) ? $_GET['filter'] : '';
	$dateline = match ($filter) {
		'thisweek' => intval($_G['timestamp'] - 604800),
		'thismonth' => intval($_G['timestamp'] - 2592000),
		'halfyear', 'before' => intval($_G['timestamp'] - 15552000),
		default => false,
	};

	$level = !empty($_GET['level']) ? $_GET['level'] : '';
	$score = match ($level) {
		'good' => 1,
		'soso' => 0,
		'bad' => -1,
		default => false,
	};

	$page = max(1, intval($_GET['page']));
	$start_limit = ($page - 1) * 10;
	$num = table_forum_tradecomment::t()->count_list($from, $uid, $dateline, $score);
	$multipage = multi($num, 10, $page, "home.php?mod=spacecp&ac=list&uid=$uid".($from ? "&from=$from" : NULL).($filter ? "&filter=$filter" : NULL).($level ? "&level=$level" : NULL));

	$comments = [];
	foreach(table_forum_tradecomment::t()->fetch_all_list($from, $uid, $dateline, $score, $start_limit) as $comment) {
		$comment['expiration'] = dgmdate($comment['dateline'] + 30 * 86400, 'u');
		$comment['dbdateline'] = $comment['dateline'];
		$comment['dateline'] = dgmdate($comment['dateline'], 'u');
		$comment['baseprice'] = sprintf('%0.2f', $comment['baseprice']);
		$comments[] = $comment;
	}

	include template('home/spacecp_ec_list');

} elseif($_GET['op'] == 'rate' && ($orderid = $_GET['orderid']) && isset($_GET['type'])) {

	require_once libfile('function/trade');

	$type = intval($_GET['type']);
	if(!$type) {
		$raterid = 'buyerid';
		$ratee = 'seller';
		$rateeid = 'sellerid';
	} else {
		$raterid = 'sellerid';
		$ratee = 'buyer';
		$rateeid = 'buyerid';
	}
	$order = table_forum_tradelog::t()->fetch($orderid);
	if(!$order || $order[$raterid] != $_G['uid']) {
		showmessage('eccredit_order_notfound');
	} elseif($order['ratestatus'] == 3 || ($type == 0 && $order['ratestatus'] == 1) || ($type == 1 && $order['ratestatus'] == 2)) {
		showmessage('eccredit_rate_repeat');
	} elseif(!trade_typestatus('successtrades', $order['status']) && !trade_typestatus('refundsuccess', $order['status'])) {
		showmessage('eccredit_nofound');
	}

	$uid = $_G['uid'] == $order['buyerid'] ? $order['sellerid'] : $order['buyerid'];

	if(!submitcheck('ratesubmit')) {

		include template('home/spacecp_ec_rate');

	} else {

		$score = intval($_GET['score']);
		$message = cutstr(dhtmlspecialchars($_GET['message']), 200);
		$level = $score == 1 ? 'good' : ($score == 0 ? 'soso' : 'bad');
		$pid = intval($order['pid']);
		$order = daddslashes($order, 1);

		table_forum_tradecomment::t()->insert([
			'pid' => $pid,
			'orderid' => $orderid,
			'type' => $type,
			'raterid' => $_G['uid'],
			'rater' => $_G['username'],
			'ratee' => $order[$ratee],
			'rateeid' => $order[$rateeid],
			'score' => $score,
			'message' => $message,
			'dateline' => $_G['timestamp']
		]);

		if(!$order['offline'] || $order['credit']) {
			if(table_forum_tradecomment::t()->get_month_score($_G['uid'], $type, $order[$rateeid]) < $_G['setting']['ec_credit']['maxcreditspermonth']) {
				updateusercredit($uid, $type ? 'sellercredit' : 'buyercredit', $level);
			}
		}

		if($type == 0) {
			$ratestatus = $order['ratestatus'] == 2 ? 3 : 1;
		} else {
			$ratestatus = $order['ratestatus'] == 1 ? 3 : 2;
		}

		table_forum_tradelog::t()->update($order['orderid'], ['ratestatus' => $ratestatus]);

		if($ratestatus != 3) {
			notification_add($order[$rateeid], 'goods', 'eccredit', [
				'orderid' => $orderid,
			], 1);
		}

		showmessage('eccredit_succeed', 'home.php?mod=space&uid='.$_G['uid'].'&do=trade&view=eccredit');

	}

} elseif($_GET['op'] == 'explain' && $_GET['id']) {

	$id = intval($_GET['id']);
	$ajaxmenuid = $_GET['ajaxmenuid'];
	if(!submitcheck('explainsubmit', 1)) {
		include template('home/spacecp_ec_explain');
	} else {
		$comment = table_forum_tradecomment::t()->fetch($id);
		if(!$comment || $comment['rateeid'] != $_G['uid']) {
			showmessage('eccredit_nofound');
		} elseif($comment['explanation']) {
			showmessage('eccredit_reexplanation_repeat');
		} elseif($comment['dateline'] < TIMESTAMP - 30 * 86400) {
			showmessage('eccredit_reexplanation_closed');
		}

		$explanation = cutstr(dhtmlspecialchars($_GET['explanation']), 200);

		table_forum_tradecomment::t()->update($id, ['explanation' => $explanation]);

		$language = lang('forum/misc');
		showmessage($language['eccredit_explain'].'&#58; '.$explanation, '', [], ['msgtype' => 3, 'showmsg' => 1]);
	}

}
