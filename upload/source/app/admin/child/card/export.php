<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$sqladd = cardsql();
$_GET['start'] = intval($_GET['start']);
$count = $sqladd ? table_common_card::t()->count_by_where($sqladd) : table_common_card::t()->count();
if($count) {
	$cardtype = table_common_card_type::t()->range();
	$count = min(10000, $count);
	foreach(table_common_card::t()->fetch_all_by_where($sqladd, $_GET['start'], $count) as $result) {
		$userlist[$result['uid']] = $result['uid'];
		$userlist[$result['makeruid']] = $result['makeruid'];
		$result['extcreditsval'] = $result['extcreditsval'].$_G['setting']['extcredits'][$result['extcreditskey']]['title'];
		unset($result['extcreditskey']);
		unset($result['maketype']);
		$cardlist[] = $result;
	}
	if($userlist) {
		$members = table_common_member::t()->fetch_all($userlist);
		unset($userlist);
	}

	foreach($cardlist as $key => $val) {
		foreach($val as $skey => $sval) {
			$sval = preg_replace('/\s+/', ' ', $sval);
			if($skey == 'id' && !$title['id']) {
				$title['id'] = cplang('card_number');
			}
			if($skey == 'typeid') {
				if(!$title['typeid']) {
					$title['typeid'] = cplang('card_type');
				}
				$sval = $sval != 0 ? $cardtype[$sval]['typename'] : cplang('card_type_default');
			}
			if(in_array($skey, ['uid', 'makeruid'])) {
				if($skey == 'makeruid' && !$title['makeruid']) {
					$title['makeruid'] = cplang('card_log_maker');
				}
				if($skey == 'uid' && !$title['uid']) {
					$title['uid'] = cplang('card_log_used_user');
				}

				$sval = $members[$sval]['username'];
			}
			if($skey == 'price') {
				if(!$title['price']) {
					$title['price'] = cplang('card_log_price');
				}
				$sval = $sval.cplang('card_make_price_unit');
			}
			if($skey == 'extcreditsval') {
				if(!$title['extcreditsval']) {
					$title['extcreditsval'] = cplang('card_extcreditsval');
				}
			}
			if($skey == 'status') {
				if(!$title['status']) {
					$title['status'] = cplang('card_status');
				}
				$sval = cplang('card_manage_status_'.$sval);
			}
			if(in_array($skey, ['dateline', 'cleardateline', 'useddateline'])) {
				if($skey == 'dateline' && !$title['dateline']) {
					$title['dateline'] = cplang('card_maketime');
				}
				if($skey == 'cleardateline' && !$title['cleardateline']) {
					$title['cleardateline'] = cplang('card_make_cleardateline');
				}
				if($skey == 'useddateline' && !$title['useddateline']) {
					$title['useddateline'] = cplang('card_used_dateline');
				}

				$sval = $sval ? date('Y-m-d', $sval) : '';
			}
			$detail .= strlen($sval) > 11 && is_numeric($sval) ? '['.$sval.'],' : $sval.',';
		}
		$detail = $detail."\n";
	}

}
$title = is_array($title) ? $title : [$title];
$detail = implode(',', $title)."\n".$detail;
$filename = 'card_'.date('Ymd', TIMESTAMP).'.csv';

ob_end_clean();
header('Content-Encoding: none');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');
header('Expires: 0');
if($_G['charset'] != 'gbk') {
	$detail = diconv($detail, $_G['charset'], 'GBK');
}
echo $detail;
exit();
	