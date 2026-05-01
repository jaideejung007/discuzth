<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$orderurl = [
	'alipay' => 'https://www.alipay.com/trade/query_trade_detail.htm?trade_no=',
	'tenpay' => 'https://www.tenpay.com/med/tradeDetail.shtml?trans_id=',
];

if(!$_G['setting']['creditstrans'] || !$_G['setting']['ec_ratio']) {
	cpmsg('orders_disabled', '', 'error');
}

if(!submitcheck('ordersubmit')) {

	echo '<script type="text/javascript" src="'.STATICURL.'js/calendar.js"></script>';

	/*search={"nav_ec":"action=ec&operation=base","nav_ec_orders":"action=ec&operation=orders"}*/
	showtips('ec_orders_tips');
	showtagheader('div', 'ordersearch', !submitcheck('searchsubmit', 1));
	showformheader('ec&operation=orders');
	showtableheader('ec_orders_search');
	showsetting('ec_orders_search_status', ['orderstatus', [
		['', $lang['ec_orders_search_status_all']],
		[1, $lang['ec_orders_search_status_pending']],
		[2, $lang['ec_orders_search_status_auto_finished']],
		[3, $lang['ec_orders_search_status_manual_finished']]
	]], intval($orderstatus), 'select');
	showsetting('ec_orders_search_id', 'orderid', $orderid, 'text');
	showsetting('ec_orders_search_users', 'users', $users, 'text');
	showsetting('ec_orders_search_buyer', 'buyer', $buyer, 'text');
	showsetting('ec_orders_search_admin', 'admin', $admin, 'text');
	showsetting('ec_orders_search_submit_date', ['sstarttime', 'sendtime'], [$sstarttime, $sendtime], 'daterange');
	showsetting('ec_orders_search_confirm_date', ['cstarttime', 'cendtime'], [$cstarttime, $cendtime], 'daterange');
	showsubmit('searchsubmit');
	showtablefooter();
	showformfooter();
	showtagfooter('div');
	/*search*/

	if(submitcheck('searchsubmit', 1)) {

		$start_limit = ($page - 1) * $_G['tpp'];


		$ordercount = table_forum_order::t()->count_by_search(null, $_GET['orderstatus'], $_GET['orderid'], null, ($_GET['users'] ? explode(',', str_replace(' ', '', $_GET['users'])) : null), $_GET['buyer'], $_GET['admin'], strtotime($_GET['sstarttime']), strtotime($_GET['sendtime']), strtotime($_GET['cstarttime']), strtotime($_GET['cendtime']));
		$multipage = multi($ordercount, $_G['tpp'], $page, ADMINSCRIPT."?action=ec&operation=orders&searchsubmit=yes&orderstatus={$_GET['orderstatus']}&orderid={$_GET['orderid']}&users={$_GET['users']}&buyer={$_GET['buyer']}&admin={$_GET['admin']}&sstarttime={$_GET['sstarttime']}&sendtime={$_GET['sendtime']}&cstarttime={$_GET['cstarttime']}&cendtime={$_GET['cendtime']}");

		showtagheader('div', 'orderlist', true);
		showformheader('ec&operation=orders');
		showtableheader('result');
		showsubtitle(['', 'ec_orders_id', 'ec_orders_status', 'ec_orders_buyer', 'ec_orders_amount', 'ec_orders_price', 'ec_orders_submitdate', 'ec_orders_confirmdate']);


		foreach(table_forum_order::t()->fetch_all_by_search(null, $_GET['orderstatus'], $_GET['orderid'], null, ($_GET['users'] ? explode(',', str_replace(' ', '', $_GET['users'])) : null), $_GET['buyer'], $_GET['admin'], strtotime($_GET['sstarttime']), strtotime($_GET['sendtime']), strtotime($_GET['cstarttime']), strtotime($_GET['cendtime']), $start_limit, $_G['tpp']) as $order) {
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
				"<a href=\"home.php?mod=space&uid={$order['uid']}\" target=\"_blank\">{$order['username']}</a>",
				"{$_G['setting']['extcredits'][$_G['setting']['creditstrans']]['title']} {$order['amount']} {$_G['setting']['extcredits'][$_G['setting']['creditstrans']]['unit']}",
				"{$lang['rmb']} {$order['price']} {$lang['rmb_yuan']}",
				$order['submitdate'],
				$order['confirmdate']
			]);
		}

		showsubmit('ordersubmit', 'submit', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'validate\')" /><label for="chkall">'.cplang('ec_orders_validate').'</label>', '<a href="#" onclick="$(\'orderlist\').style.display=\'none\';$(\'ordersearch\').style.display=\'\';">'.cplang('research').'</a>', $multipage);
		showtablefooter();
		showformfooter();
		showtagfooter('div');
	}

} else {

	$numvalidate = 0;
	if($_GET['validate']) {
		$orderids = [];
		$confirmdate = dgmdate(TIMESTAMP);

		foreach(table_forum_order::t()->fetch_all_order($_GET['validate'], '1') as $order) {
			updatemembercount($order['uid'], [$_G['setting']['creditstrans'] => $order['amount']]);
			$orderids[] = $order['orderid'];

			$submitdate = dgmdate($order['submitdate']);
			notification_add($order['uid'], 'system', 'addfunds', [
				'orderid' => $order['orderid'],
				'price' => $order['price'],
				'from_id' => 0,
				'from_idtype' => 'buycredit',
				'value' => $_G['setting']['extcredits'][$_G['setting']['creditstrans']]['title'].' '.$order['amount'].' '.$_G['setting']['extcredits'][$_G['setting']['creditstrans']]['unit']
			], 1);
		}
		if($orderids) {
			table_forum_order::t()->update($orderids, ['status' => '3', 'admin' => $_G['username'], 'confirmdate' => $_G['timestamp']]);
		}
	}

	cpmsg('orders_validate_succeed', "action=ec&operation=orders&searchsubmit=yes&orderstatus={$_GET['orderstatus']}&orderid={$_GET['orderid']}&users={$_GET['users']}&buyer={$_GET['buyer']}&admin={$_GET['admin']}&sstarttime={$_GET['sstarttime']}&sendtime={$_GET['sendtime']}&cstarttime={$_GET['cstarttime']}&cendtime={$_GET['cendtime']}", 'succeed');

}
	