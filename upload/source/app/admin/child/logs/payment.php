<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showtableheader('', 'fixpadding');

showtablerow('class="header"', ['width="30%"', 'class="td23"', 'class="td23"', 'class="td24"', 'class="td23"', 'class="td24"', 'class="td24"'], [
	cplang('subject'),
	cplang('logs_payment_amount'),
	cplang('logs_payment_seller'),
	cplang('logs_payment_buyer'),
	cplang('logs_payment_dateline'),
	cplang('logs_payment_buydateline'),
]);

$tpp = $_GET['lpp'] ? intval($_GET['lpp']) : $_G['tpp'];
$start_limit = ($page - 1) * $tpp;

$threadcount = table_common_credit_log::t()->count_by_operation('BTC');
if($threadcount) {
	$multipage = multi($threadcount, $tpp, $page, ADMINSCRIPT."?action=logs&operation=payment&lpp=$lpp", 0, 3);
	$logs = table_common_credit_log::t()->fetch_all_by_operation('BTC', $start_limit, $tpp);
	$ltids = $luid = [];
	foreach($logs as $log) {
		$luid[$log['uid']] = $log['uid'];
		$ltids[$log['relatedid']] = $log['relatedid'];
	}
	$members = table_common_member::t()->fetch_all($luid);
	$threads = table_forum_thread::t()->fetch_all($ltids);
	foreach($logs as $paythread) {
		$thread = $threads[$paythread['relatedid']];
		$paythread['username'] = $members[$paythread['uid']]['username'];
		$paythread['tid'] = $thread['tid'];
		$paythread['subject'] = $thread['subject'];
		$paythread['postdateline'] = $thread['dateline'];
		$paythread['author'] = $thread['author'];
		$paythread['tauthorid'] = $thread['authorid'];

		$paythread['seller'] = $paythread['tauthorid'] ? "<a href=\"home.php?mod=space&uid={$paythread['tauthorid']}\">{$paythread['author']}</a>" : cplang('logs_payment_del')."(<a href=\"home.php?mod=space&uid={$paythread['authorid']}\">".cplang('logs_payment_view').'</a>)';
		$paythread['buyer'] = "<a href=\"home.php?mod=space&uid={$paythread['uid']}\">{$paythread['username']}</a>";
		$paythread['subject'] = $paythread['subject'] ? "<a href=\"forum.php?mod=viewthread&tid={$paythread['tid']}\">{$paythread['subject']}</a>" : cplang('logs_payment_del');
		$paythread['dateline'] = dgmdate($paythread['dateline'], 'Y-n-j H:i');
		$paythread['postdateline'] = $paythread['postdateline'] ? dgmdate($paythread['postdateline'], 'Y-n-j H:i') : cplang('logs_payment_del');
		foreach($_G['setting']['extcredits'] as $id => $credits) {
			if($paythread['extcredits'.$id]) {
				$paythread['amount'] = $credits['title'].':'.abs($paythread['extcredits'.$id]);
				break;
			}
		}
		showtablerow('', ['', 'class="bold"'], [
			$paythread['subject'],
			$paythread['amount'],
			$paythread['seller'],
			$paythread['buyer'],
			$paythread['postdateline'],
			$paythread['dateline']
		]);
	}
}
	