<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($_GET['op'] == 'query') {
	$transfer_no = daddslashes($_GET['transfer_no']);

	$result = payment::transfer_status($transfer_no);
	if($result['code'] == 200) {
		cpmsg('payment_transfer_succeed', $_G['siteurl'].ADMINSCRIPT.'?action=ec&operation=transferorders&out_biz_no='.$transfer_no, 'succeed');
	} else {
		cpmsg($result['message'], $_G['siteurl'].ADMINSCRIPT.'?action=ec&operation=transferorders&out_biz_no='.$transfer_no, 'error');
	}
} elseif($_GET['op'] == 'retry') {
	$order_id = intval($_GET['order_id']);
	$order = table_common_payment_transfer::t()->fetch($order_id);

	$result = payment::transfer($order['channel'], $order['out_biz_no'], $order['amount'], $order['uid'], $order['realname'], $order['account'], $order['subject'], $order['description']);
	if($result['code'] == 200) {
		cpmsg('payment_transfer_succeed', $_G['siteurl'].ADMINSCRIPT.'?action=ec&operation=transferorders&out_biz_no='.$order['out_biz_no'], 'succeed');
	} else {
		cpmsg($result['message'], $_G['siteurl'].ADMINSCRIPT.'?action=ec&operation=transferorders&out_biz_no='.$order['out_biz_no'], 'error');
	}
} else {
	$start_limit = ($page - 1) * $_G['tpp'];

	/*search={"nav_ec":"action=ec&operation=base","nav_ec_qpay":"action=ec&operation=transferorders"}*/
	echo '<style type="text/css">.order-status-1 td { color: #555; } .order-status-2 td { color: green; } .order-status-3 td { color: red; }</style>';
	echo '<script src="static/js/calendar.js" type="text/javascript"></script>';
	$queryparams = [
		'out_biz_no' => daddslashes($_GET['out_biz_no']),
		'user' => daddslashes($_GET['user']),
		'channel' => daddslashes($_GET['channel']),
		'status' => daddslashes($_GET['status']),
		'starttime' => daddslashes($_GET['starttime']),
		'endtime' => daddslashes($_GET['endtime']),
	];

	showformheader('ec&operation=transferorders');
	showtableheader('ec_transferorders_search');
	showtablerow('', [],
		[
			lang('admincp', 'ec_orders_search_id'), '<input type="text" class="txt" name="out_biz_no" value="'.$queryparams['out_biz_no'].'" />',
			lang('admincp', 'ec_transferorders_user'), '<input type="text" class="txt" name="user" value="'.$queryparams['user'].'" />',
		]
	);

	$channels = payment::channels();
	$channeloptions = [];
	$channeloptions[] = '<option value="">'.$lang['all'].'</option>';
	foreach($channels as $channel) {
		$channeloptions[] = '<option value="'.$channel['id'].'"'.($queryparams['channel'] == $channel['id'] ? ' selected' : '').'>'.$channel['title'].'</option>';
	}
	$statusoptions = [];
	$statusoptions[] = '<option value="">'.$lang['all'].'</option>';
	$statusoptions[] = '<option value="0"'.($queryparams['status'] === '1' ? ' selected' : '').'>'.$lang['ec_transferorders_status_1'].'</option>';
	$statusoptions[] = '<option value="1"'.($queryparams['status'] === '2' ? ' selected' : '').'>'.$lang['ec_transferorders_status_2'].'</option>';
	$statusoptions[] = '<option value="2"'.($queryparams['status'] === '3' ? ' selected' : '').'>'.$lang['ec_transferorders_status_3'].'</option>';
	showtablerow('', [
		'style="width:100px"', 'style="width:200px"',
		'style="width:100px"', 'style="width:200px"',
		'style="width:100px"', ''
	],
		[
			lang('admincp', 'ec_transferorders_channel'), '<select name="channel">'.implode('', $channeloptions).'</select>',
			lang('admincp', 'ec_paymentorders_status'), '<select name="status">'.implode('', $statusoptions).'</select>',
			lang('admincp', 'ec_paymentorders_date'), '<input type="text" class="txt" name="starttime" value="'.$queryparams['starttime'].'" style="width: 108px;" onclick="showcalendar(event, this)"> - <input type="text" class="txt" name="endtime" value="'.$queryparams['endtime'].'" style="width: 108px;" onclick="showcalendar(event, this)">',
		]
	);
	showtablefooter();
	showtableheader('', 'notop');
	showsubmit('searchsubmit');
	showtablefooter();
	/** list */
	if($queryparams['user']) {
		if(preg_match('/^\d+$/', $queryparams['user'])) {
			$queryparams['uid'] = $queryparams['user'];
		} else {
			$uid = table_common_member::t()->fetch_uid_by_username($queryparams['user']);
			if($uid) {
				$queryparams['uid'] = $uid;
			} else {
				$queryparams['uid'] = -1;
			}
		}
	}
	$ordercount = table_common_payment_transfer::t()->count_by_search($queryparams['uid'], $queryparams['starttime'], $queryparams['endtime'], $queryparams['out_biz_no'], $queryparams['channel'], $queryparams['status']);
	$multipage = multi($ordercount, $_G['tpp'], $page, ADMINSCRIPT.'?action=ec&operation=transferorders&'.http_build_query($queryparams));

	$tdstyles = [
		'style="width: 220px;"',
		'style="width: 100px; text-align: center"',
		'style="width: 100px; text-align: center"',
		'',
		'style="width: 130px; text-align: right"',
		'style="width: 100px; text-align: center"',
		'',
		'style="width: 100px; text-align: right"',
		'style="width: 100px; text-align: right"',
		'style="width: 25px; text-align: right"'
	];
	showtableheader('result');
	showsubtitle(['ec_paymentorders_no', 'ec_transferorders_user', 'ec_transferorders_channel', 'ec_transferorders_desc', 'ec_paymentorders_amount', 'ec_paymentorders_status', 'ec_transferorders_error', 'ec_orders_submitdate', 'ec_orders_confirmdate', ''], 'header', $tdstyles);
	if($ordercount > 0) {
		$order_list = table_common_payment_transfer::t()->fetch_all_by_search($queryparams['uid'], $queryparams['type'], $queryparams['starttime'], $queryparams['endtime'], $queryparams['out_biz_no'], $queryparams['channel'], $queryparams['status'], $start_limit, $_G['tpp']);
		foreach($order_list as $order) {
			$user = getuserbyuid($order['uid']);
			if($order['status'] == 1) {
				$operations = '<a href="'.ADMINSCRIPT.'?action=ec&operation=transferorders&op=query&transfer_no='.$order['out_biz_no'].'">'.$lang['ec_paymentorders_op_status'].'</a>';
			} elseif($order['status'] == 3) {
				$operations = '<a href="'.ADMINSCRIPT.'?action=ec&operation=transferorders&op=retry&order_id='.$order['id'].'">'.$lang['ec_transferorders_op_retry'].'</a>';
			}
			showtablerow('class="order-status-'.$order['status'].'"', $tdstyles, [
				$order['out_biz_no'],
				$user['username'].' ('.$order['uid'].')'.'<br/>'.$order['clientip'].':'.$order['remoteport'],
				$channels[$order['channel']]['title'],
				$order['subject'].($order['description'] ? '<br/>'.$order['description'] : ''),
				number_format($order['amount'] / 100, 2, '.', ','),
				$lang['ec_transferorders_status_'.$order['status']],
				$order['status'] == 3 ? $order['error'] : '',
				dgmdate($order['dateline']),
				$order['trade_time'] ? dgmdate($order['trade_time']) : 'N/A',
				$operations
			]);
		}
		showsubmit('', '', '', '', $multipage);
	} else {
		showtablerow('', ['class="center" colspan="25"'], [$lang['ec_transferorders_no_data']]);
	}
	showtablefooter();
	showformfooter();
	/*search*/
}
	