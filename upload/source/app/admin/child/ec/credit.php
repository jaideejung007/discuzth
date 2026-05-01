<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$defaultrank = [
	1 => 4,
	2 => 11,
	3 => 41,
	4 => 91,
	5 => 151,
	6 => 251,
	7 => 501,
	8 => 1001,
	9 => 2001,
	10 => 5001,
	11 => 10001,
	12 => 20001,
	13 => 50001,
	14 => 100001,
	15 => 200001
];

if(!submitcheck('creditsubmit')) {

	$ec_credit = table_common_setting::t()->fetch_setting('ec_credit', true);
	$ec_credit = $ec_credit ? $ec_credit : [
		'maxcreditspermonth' => '6',
		'rank' => $defaultrank
	];

	/*search={"nav_ec":"action=ec&operation=base","nav_ec_credit":"action=ec&operation=credit"}*/
	showtips('ec_credit_tips');
	showformheader('ec&operation=credit');
	showtableheader('ec_credit', 'nobottom');
	showsetting('ec_credit_maxcreditspermonth', 'ec_creditnew[maxcreditspermonth]', $ec_credit['maxcreditspermonth'], 'text');
	showtablefooter('</tbody>');
	/*search*/

	showtableheader('ec_credit_rank', 'notop fixpadding');
	showsubtitle(['ec_credit_rank', 'ec_credit_between', 'ec_credit_sellericon', 'ec_credit_buyericon']);

	$staticurl = STATICURL;

	foreach($ec_credit['rank'] as $rank => $mincredits) {
		showtablerow('', '', [
			$rank,
			'<input type="text" class="txt" size="6" name="ec_creditnew[rank]['.$rank.']" value="'.$mincredits.'" /> ~ '.$ec_credit['rank'][$rank + 1],
			"<img src=\"{$staticurl}image/traderank/seller/$rank.gif\" border=\"0\">",
			"<img src=\"{$staticurl}image/traderank/buyer/$rank.gif\" border=\"0\">"
		]);
	}
	showsubmit('creditsubmit');
	showtablefooter();
	showformfooter();

} else {
	$ec_creditnew = $_GET['ec_creditnew'];
	$ec_creditnew['maxcreditspermonth'] = intval($ec_creditnew['maxcreditspermonth']);

	if(is_array($ec_creditnew['rank'])) {
		foreach($ec_creditnew['rank'] as $rank => $mincredits) {
			$mincredits = intval($mincredits);
			if($rank == 1 && $mincredits <= 0) {
				cpmsg('ecommerce_invalidcredit', '', 'error');
			} elseif($rank > 1 && $mincredits <= $ec_creditnew['rank'][$rank - 1]) {
				cpmsg('ecommerce_must_larger', '', 'error', ['rank' => $rank]);
			}
			$ec_creditnew['rank'][$rank] = $mincredits;
		}
	} else {
		$ec_creditnew['rank'] = $defaultrank;
	}

	table_common_setting::t()->update_setting('ec_credit', $ec_creditnew);
	updatecache('setting');

	cpmsg('ec_credit_succeed', 'action=ec&operation=credit', 'succeed');

}
	