<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['forumstatus']) {
	showmessage('forum_status_off');
}

$minhot = $_G['setting']['feedhotmin'] < 1 ? 3 : $_G['setting']['feedhotmin'];
$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
if($page < 1) $page = 1;
$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
$opactives['trade'] = 'class="a"';

$_GET['view'] = in_array($_GET['view'], ['we', 'me', 'tradelog', 'eccredit', 'onlyuser']) ? $_GET['view'] : 'we';
$_GET['order'] = in_array($_GET['order'], ['hot', 'dateline']) ? $_GET['order'] : 'dateline';

$perpage = 20;
$perpage = mob_perpage($perpage);
$start = ($page - 1) * $perpage;
ckstart($start, $perpage);

$list = [];
$userlist = [];
$count = 0;

$gets = [
	'mod' => 'space',
	'uid' => $space['uid'],
	'do' => 'trade',
	'view' => $_GET['view'],
	'order' => $_GET['order'],
	'type' => $_GET['type'],
	'status' => $_GET['status'],
	'fuid' => $_GET['fuid'],
	'searchkey' => $_GET['searchkey']
];
$theurl = 'home.php?'.url_implode($gets);
$multi = '';

$wheresql = '1';
$apply_sql = '';

$f_index = '';
$ordersql = 't.dateline DESC';
$need_count = true;

if($_GET['view'] == 'me') {

	$wheresql = "t.sellerid = '{$space['uid']}'";

} elseif($_GET['view'] == 'tradelog') {

	$viewtype = in_array($_GET['type'], ['sell', 'buy']) ? $_GET['type'] : 'sell';
	$filter = $_GET['filter'] ? $_GET['filter'] : 'all';
	$sqlfield = $viewtype == 'sell' ? 'sellerid' : 'buyerid';
	$sqlfilter = '';
	$ratestatus = 0;
	$item = $viewtype == 'sell' ? 'selltrades' : 'buytrades';

	switch($filter) {
		case 'attention':
			$typestatus = $item;
			break;
		case 'eccredit'        :
			$typestatus = 'eccredittrades';
			$ratestatus = $item == 'selltrades' ? 1 : 2;
			break;
		case 'all':
			$typestatus = '';
			break;
		case 'success':
			$typestatus = 'successtrades';
			break;
		case 'closed'        :
			$typestatus = 'closedtrades';
			break;
		case 'refund'        :
			$typestatus = 'refundtrades';
			break;
		case 'unstart'        :
			$typestatus = 'unstarttrades';
			break;
		default:
			$typestatus = 'tradingtrades';
			break;
	}
	require_once libfile('function/trade');

	$typestatus = $typestatus ? trade_typestatus($typestatus) : [];

	$srchkey = stripsearchkey($_GET['searchkey']);


	$tid = intval($_GET['tid']);
	$pid = intval($_GET['pid']);
	$sqltid = $tid ? 'tl.tid=\''.$tid.'\' AND '.($pid ? 'tl.pid=\''.$pid.'\' AND ' : '') : '';
	$extra .= $srchfid ? '&amp;filter='.$filter : '';
	$extratid = $tid ? "&amp;tid=$tid".($pid ? "&amp;pid=$pid" : '') : '';
	$num = table_forum_tradelog::t()->count_log($viewtype, $_G['uid'], $tid, $pid, $ratestatus, $typestatus);

	$multi = multi($num, $perpage, $page, $theurl);
	$tradeloglist = [];
	foreach(table_forum_tradelog::t()->fetch_all_log($viewtype, $_G['uid'], $tid, $pid, $ratestatus, $typestatus, $start, $perpage) as $tradelog) {
		$tradelog['lastupdate'] = dgmdate($tradelog['lastupdate'], 'u', 1);
		$tradelog['attend'] = trade_typestatus($item, $tradelog['status']);
		$tradelog['status'] = trade_getstatus($tradelog['status']);
		$tradeloglist[] = $tradelog;
	}
	$creditid = 0;
	if($_G['setting']['creditstransextra'][5]) {
		$creditid = intval($_G['setting']['creditstransextra'][5]);
	} elseif($_G['setting']['creditstrans']) {
		$creditid = intval($_G['setting']['creditstrans']);
	}
	$extcredits = $_G['setting']['extcredits'];
	$orderactives = [$viewtype => ' class="a"'];
	$need_count = false;

} elseif($_GET['view'] == 'eccredit') {

	require_once libfile('function/ec_credit');
	$uid = !empty($_GET['uid']) ? intval($_GET['uid']) : $_G['uid'];

	loadcache('usergroups');

	$member = getuserbyuid($uid);
	if(!$member) {
		showmessage('member_nonexistence', NULL, [], ['login' => 1]);
	}
	$member = array_merge($member, table_common_member_profile::t()->fetch($uid), table_common_member_status::t()->fetch($uid), table_common_member_field_forum::t()->fetch($uid));
	$member['avatar'] = '<div class="avatar">'.avatar($member['uid']);
	if($_G['cache']['usergroups'][$member['groupid']]['groupavatar']) {
		$member['avatar'] .= '<br /><img src="'.$_G['cache']['usergroups'][$member['groupid']]['groupavatar'].'" border="0" alt="" />';
	}
	$member['avatar'] .= '</div>';

	$member['taobaoas'] = str_replace("'", '', addslashes($member['taobao']));
	$member['regdate'] = dgmdate($member['regdate'], 'd');
	$member['usernameenc'] = rawurlencode($member['username']);
	$member['buyerrank'] = 0;
	if($member['buyercredit']) {
		foreach($_G['setting']['ec_credit']['rank'] as $level => $credit) {
			if($member['buyercredit'] <= $credit) {
				$member['buyerrank'] = $level;
				break;
			}
		}
	}
	$member['sellerrank'] = 0;
	if($member['sellercredit']) {
		foreach($_G['setting']['ec_credit']['rank'] as $level => $credit) {
			if($member['sellercredit'] <= $credit) {
				$member['sellerrank'] = $level;
				break;
			}
		}
	}

	$caches = [];
	foreach(table_forum_spacecache::t()->fetch_all_spacecache($uid, ['buyercredit', 'sellercredit']) as $cache) {
		$caches[$cache['variable']] = dunserialize($cache['value']);
		$caches[$cache['variable']]['expiration'] = $cache['expiration'];
	}

	foreach(['buyercredit', 'sellercredit'] as $type) {
		if(!isset($caches[$type]) || TIMESTAMP > $caches[$type]['expiration']) {
			$caches[$type] = updatecreditcache($uid, $type, 1);
		}
	}
	@$buyerpercent = $caches['buyercredit']['all']['total'] ? sprintf('%0.2f', $caches['buyercredit']['all']['good'] * 100 / $caches['buyercredit']['all']['total']) : 0;
	@$sellerpercent = $caches['sellercredit']['all']['total'] ? sprintf('%0.2f', $caches['sellercredit']['all']['good'] * 100 / $caches['sellercredit']['all']['total']) : 0;
	$need_count = false;

	include template('home/space_eccredit');
	exit;

} elseif($_GET['view'] == 'onlyuser') {
	$uid = !empty($_GET['uid']) ? intval($_GET['uid']) : $_G['uid'];
	$wheresql = "t.sellerid = '$uid'";
} else {

	space_merge($space, 'field_home');

	if($space['feedfriend']) {

		$fuid_actives = [];

		require_once libfile('function/friend');
		$fuid = intval($_GET['fuid']);
		if($fuid && friend_check($fuid, $space['uid'])) {
			$wheresql = 't.'.DB::field('sellerid', $fuid);
			$fuid_actives = [$fuid => ' selected'];
		} else {
			$wheresql = 't.'.DB::field('sellerid', $space['feedfriend']);
			$theurl = "home.php?mod=space&uid={$space['uid']}&do=$do&view=we";
		}

		$query = table_home_friend::t()->fetch_all_by_uid($space['uid'], 0, 100, true);
		foreach($query as $value) {
			$userlist[] = $value;
		}

	} else {
		$need_count = false;
	}
}

$actives = [$_GET['view'] => ' class="a"'];

if($need_count) {
	if($searchkey = stripsearchkey($_GET['searchkey'])) {
		$wheresql .= ' AND t.'.DB::field('subject', '%'.$searchkey.'%', 'like');
	}
	$havecache = false;

	$count = table_forum_trade::t()->fetch_all_for_space($wheresql, '', 1);
	if($count) {
		$query = table_forum_trade::t()->fetch_all_for_space($wheresql, $ordersql, 0, $start, $perpage);
		$pids = $aids = $thidden = [];
		foreach($query as $value) {
			$aids[$value['aid']] = $value['aid'];
			$value['dateline'] = dgmdate($value['dateline']);
			$pids[] = (float)$value['pid'];
			$list[$value['pid']] = $value;
		}


		$multi = multi($count, $perpage, $page, $theurl);
	}

}

if($count) {
	$emptyli = [];
	if(count($list) % 5 != 0) {
		for($i = 0; $i < 5 - count($list) % 5; $i++) {
			$emptyli[] = $i;
		}
	}
}

if($_G['uid']) {
	$_GET['view'] = !$_GET['view'] ? 'we' : $_GET['view'];
	$navtitle = lang('core', 'title_'.$_GET['view'].'_trade');
	if($navtitle == 'title_'.$_GET['view'].'_trade') {
		$navtitle = lang('core', 'title_trade');
	}
} else {
	$navtitle = lang('core', 'title_trade');
}

include_once template('diy:home/space_trade');

