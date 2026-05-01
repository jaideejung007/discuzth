<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$rid = intval($_GET['rid']);
$fid = intval($_GET['fid']);
$ruleinfo = table_common_credit_rule::t()->fetch($rid);
list(, $isSub) = explode('/', $ruleinfo['action']);
if($isSub) {
	cpmsg('undefined_action', '', 'error');
}

shownav('global', 'credits_edit');
showchildmenu([['setting_credits', 'setting&operation=credits&anchor=base'], ['setting_credits_policy', 'credits&operation=list&anchor=policytable']],
	"{$lang['setting_credits_add_sub']} - {$ruleinfo['rulename']}");

if(!submitcheck('rulesubmit')) {
	showtips('setting_credits_sub_add_tips');

	showformheader("credits&operation=add&rid=$rid&".($fid ? "fid=$fid" : ''));
	$cycletype = [
		[0, $lang['setting_credits_policy_cycletype_0'], ['cycletimetd' => 'none', 'rewardnumtd' => 'none']],
		[1, $lang['setting_credits_policy_cycletype_1'], ['cycletimetd' => 'none', 'rewardnumtd' => '']],
		[5, $lang['setting_credits_policy_cycletype_5'], ['cycletimetd' => 'none', 'rewardnumtd' => '']],
		[6, $lang['setting_credits_policy_cycletype_6'], ['cycletimetd' => 'none', 'rewardnumtd' => '']],
		[2, $lang['setting_credits_policy_cycletype_2'], ['cycletimetd' => '', 'rewardnumtd' => '']],
		[3, $lang['setting_credits_policy_cycletype_3'], ['cycletimetd' => '', 'rewardnumtd' => '']],
		[7, $lang['setting_credits_policy_cycletype_7'], ['cycletimetd' => '', 'rewardnumtd' => '']],
		[4, $lang['setting_credits_policy_cycletype_4'], ['cycletimetd' => 'none', 'rewardnumtd' => '']]];
	showtableheader();
	showsetting('setting_credits_policy_cycletype', ['rule[cycletype]', $cycletype], 0, 'mradio');
	showtagheader('tbody', 'cycletimetd', false, 'sub');
	showsetting('credits_edit_cycletime', 'rule[cycletime]', 0, 'text');
	showtagfooter('tbody');
	showtagheader('tbody', 'rewardnumtd', false, 'sub');
	showsetting('credits_edit_rewardnum', 'rule[rewardnum]', 0, 'text');
	showtagfooter('tbody');
	showtablefooter();
	showsubmit('rulesubmit');
	showtablefooter();
	showformfooter();
} else {
	$rule = $_GET['rule'];
	if(!$rule['cycletype']) {
		$rule['cycletime'] = 0;
		$rule['rewardnum'] = 1;
	}

	$rules = table_common_credit_rule::t()->fetch_all_by_action([$ruleinfo['action']]);
	$n = count($rules);
	foreach($rules as $rkey => $rvalue) {
		if($rvalue['cycletype'] == $rule['cycletype']) {
			cpmsg('credits_duplicate', '', 'error');
		}
		list(, $sub) = explode('/', $rvalue['action']);
		if($sub > $n) {
			$n = $sub + 1;
		}
	}


	table_common_credit_rule::t()->insert([
		'rulename' => $ruleinfo['rulename'],
		'action' => $ruleinfo['action'].'/'.$n,
		'cycletype' => $rule['cycletype'],
		'cycletime' => $rule['cycletime'],
		'rewardnum' => $rule['rewardnum'],
		'norepeat' => $ruleinfo['norepeat'],
		'fids' => $fid,
	]);

	updatecache(['setting', 'creditrule']);
	cpmsg('credits_update_succeed', 'action=credits&operation=list&anchor=policytable', 'succeed');
}
	