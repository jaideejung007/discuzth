<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$trades = [];
$query = table_forum_trade::t()->fetch_all_thread_goods($_G['tid']);
if($thread['authorid'] != $_G['uid'] && !$_G['group']['allowedittrade']) {
	showmessage('no_privilege_tradeorder');
}

if(!submitcheck('tradesubmit')) {

	$stickcount = 0;
	$trades = $tradesstick = [];
	foreach($query as $trade) {
		$stickcount = $trade['displayorder'] > 0 ? $stickcount + 1 : $stickcount;
		$trade['displayorderview'] = $trade['displayorder'] < 0 ? 128 + $trade['displayorder'] : $trade['displayorder'];
		if($trade['expiration']) {
			$trade['expiration'] = ($trade['expiration'] - TIMESTAMP) / 86400;
			if($trade['expiration'] > 0) {
				$trade['expirationhour'] = floor(($trade['expiration'] - floor($trade['expiration'])) * 24);
				$trade['expiration'] = floor($trade['expiration']);
			} else {
				$trade['expiration'] = -1;
			}
		}
		if($trade['displayorder'] < 0) {
			$trades[] = $trade;
		} else {
			$tradesstick[] = $trade;
		}
	}
	$trades = array_merge($tradesstick, $trades);
	include template('forum/trade_displayorder');

} else {

	$count = 0;
	foreach($query as $trade) {
		$displayordernew = abs(intval($_GET['displayorder'][$trade['pid']]));
		$displayordernew = $displayordernew > 128 ? 0 : $displayordernew;
		if($_GET['stick'][$trade['pid']]) {
			$count++;
			$displayordernew = $displayordernew == 0 ? 1 : $displayordernew;
		}
		if(!$_GET['stick'][$trade['pid']] || $displayordernew > 0 && $_G['group']['tradestick'] < $count) {
			$displayordernew = -1 * (128 - $displayordernew);
		}
		table_forum_trade::t()->update_trade($_G['tid'], $trade['pid'], ['displayorder' => $displayordernew]);
	}

	showmessage('trade_displayorder_updated', "forum.php?mod=viewthread&tid={$_G['tid']}".($_GET['from'] ? '&from='.$_GET['from'] : ''));

}
	