<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('ordersubmit')) {
	$start_limit = ($page - 1) * $_G['tpp'];
	$orderurl = [
		'alipay' => 'https://www.alipay.com/trade/query_trade_detail.htm?trade_no=',
		'tenpay' => 'https://www.tenpay.com/med/tradeDetail.shtml?trans_id=',
	];

	$ordercount = table_forum_order::t()->count_by_search(0, $_GET['orderstatus'], $_GET['orderid'], $_GET['email']);
	$multipage = multi($ordercount, $_G['tpp'], $page, ADMINSCRIPT."?action=ec&operation=inviteorders&orderstatus={$_GET['orderstatus']}&orderid={$_GET['orderid']}&email={$_GET['email']}");

	/*search={"nav_ec":"action=ec&operation=base","nav_ec_config":"action=ec&operation=inviteorders"}*/
	showtagheader('div', 'orderlist', TRUE);
	showformheader('ec&operation=inviteorders');
	showtableheader('ec_inviteorders_search');
	$_G['showsetting_multirow'] = 1;
	showsetting('ec_orders_search_status', ['orderstatus', [
		['', $lang['ec_orders_search_status_all']],
		[1, $lang['ec_orders_search_status_pending']],
		[2, $lang['ec_orders_search_status_auto_finished']]
	]], intval($_GET['orderstatus']), 'select');
	showsetting('ec_orders_search_id', 'orderid', $_GET['orderid'], 'text');
	showsetting('ec_orders_search_email', 'email', $_GET['email'], 'text');
	showsubmit('searchsubmit', 'submit');
	showtablefooter();
	showtableheader('result');
	showsubtitle(['', 'ec_orders_id', 'ec_inviteorders_status', 'ec_inviteorders_buyer', 'ec_orders_amount', 'ec_orders_price', 'ec_orders_submitdate', 'ec_orders_confirmdate']);

	foreach(table_forum_order::t()->fetch_all_by_search(0, $_GET['orderstatus'], $_GET['orderid'], $_GET['email'], null, null, null, null, null, null, null, $start_limit, $_G['tpp']) as $order) {
		switch($order['status']) {
			case 1:
				$order['orderstatus'] = $lang['ec_orders_search_status_pending'];
				break;
			case 2:
				$order['orderstatus'] = '<b>'.$lang['ec_orders_search_status_auto_finished'].'</b>';
				break;
			case 3:
				$order['orderstatus'] = '<b>'.$lang['ec_orders_search_status_manual_finished'].'</b><br />(<a href="home.php?mod=space&username='.rawurlencode($order['admin']).'" target="_blank">'.$order['admin'].'</a>)';
				break;
		}
		$order['submitdate'] = dgmdate($order['submitdate']);
		$order['confirmdate'] = $order['confirmdate'] ? dgmdate($order['confirmdate']) : 'N/A';

		list($orderid, $apitype) = explode("\t", $order['buyer']);
		$apitype = $apitype ? $apitype : 'alipay';
		$orderid = '<a href="'.$orderurl[$apitype].$orderid.'" target="_blank">'.$orderid.'</a>';
		showtablerow('', '', [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"validate[]\" value=\"{$order['orderid']}\" ".($order['status'] != 1 ? 'disabled' : '').'>',
			"{$order['orderid']}<br />$orderid",
			$order['orderstatus'],
			"{$order['email']}<br>{$order['ip']}",
			$order['amount'],
			"{$lang['rmb']} {$order['price']} {$lang['rmb_yuan']}",
			$order['submitdate'],
			$order['confirmdate']
		]);
	}
	showtablerow('', ['colspan="7"'], [$multipage]);
	showsubmit('ordersubmit', 'ec_orders_validate', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'validate\')" />');
	showtablefooter();
	showformfooter();
	showtagfooter('div');
	/*search*/
} else {
	if($_GET['validate']) {
		if(table_forum_order::t()->fetch_all_order($_GET['validate'], '1')) {
			table_forum_order::t()->update($_GET['validate'], ['status' => '3', 'admin' => $_G['username'], 'confirmdate' => $_G['timestamp']]);
		}
	}
	cpmsg('orders_validate_succeed', "action=ec&operation=inviteorders&orderstatus={$_GET['orderstatus']}&orderid={$_GET['orderid']}&email={$_GET['email']}", 'succeed');
}
	