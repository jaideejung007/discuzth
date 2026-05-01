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
$isSub = $usecustom = 0;
if($rid) {
	$globalrule = $ruleinfo = table_common_credit_rule::t()->fetch($rid);
	if($fid) {
		$query = table_forum_forum::t()->fetch_info_by_fid($fid);
		$forumname = $query['name'];
		$policy = $query['creditspolicy'] ? dunserialize($query['creditspolicy']) : [];
		if(isset($policy[$ruleinfo['action']])) {
			$usecustom = in_array($fid, explode(',', $globalrule['fids'])) ? 1 : 0;
			$ruleinfo = $policy[$ruleinfo['action']];
		}
	}
	list(, $isSub) = explode('/', $globalrule['action']);
}
if(!submitcheck('rulesubmit')) {
	if(!$rid) {
		$ruleinfo['rulename'] = $lang['credits_edit_lowerlimit'];
	}
	if($isSub) {
		$ruleinfo['rulename'] .= ' ('.$lang['setting_credits_sub'].'#'.$isSub.')';
	}
	if(!$fid) {
		shownav('global', 'credits_edit');
		showchildmenu([['setting_credits', 'setting&operation=credits&anchor=base'], ['setting_credits_policy', 'credits&operation=list&anchor=policytable']],
			$ruleinfo['rulename']);
	} else {
		if(!in_array($fid, explode(',', $globalrule['fids']))) {
			for($i = 1; $i <= 8; $i++) {
				$ruleinfo['extcredits'.$i] = '';
			}
		}
		shownav('forum', 'forums_edit');
		showchildmenu([['nav_forums', 'forums'], [$forumname.'(fid:'.$fid.')'], ['forums_edit_credits', 'forums&operation=edit&fid='.$fid.'&anchor=credits']],
			$ruleinfo['rulename']);
		showtips('forums_edit_tips');
	}
	showformheader("credits&operation=edit&rid=$rid&".($fid ? "fid=$fid" : ''));
	$extra = '';
	if($fid) {
		$actives = $checkarr = [];
		$actives[$usecustom] = ' class="checked"';
		$checkarr[$usecustom] = ' checked';
		showtableheader('', 'nobottom');
		$str = <<<EOF
	<ul onmouseover="altStyle(this);">
		<li$actives[1]><input type="radio" onclick="$('edit').style.display = '';" $checkarr[1] value="1" name="rule[usecustom]" class="radio">&nbsp;{$lang['yes']}</li>
		<li$actives[0]><input type="radio" onclick="$('edit').style.display = 'none';" $checkarr[0] value="0" name="rule[usecustom]" class="radio">&nbsp;{$lang['no']}</li>
	</ul>
EOF;
		showsetting('setting_credits_use_custom_credit', 'usecustom', $usecustom, $str);
		showtablefooter();
		$extra = !$usecustom ? ' style="display:none;" ' : '';
	}
	if($isSub) {
		showtips('setting_credits_sub_add_tips');
	} else {
		showtips('setting_credits_policy_comment');
	}
	showtableheader('credits_edit', 'nobottom', 'id="edit"'.$extra);
	if($rid) {
		showsetting('setting_credits_policy_rulename', 'rule[rulename]', $ruleinfo['rulename'], 'text');
		$cycletype = [
			[0, $lang['setting_credits_policy_cycletype_0'], ['cycletimetd' => 'none', 'rewardnumtd' => 'none']],
			[1, $lang['setting_credits_policy_cycletype_1'], ['cycletimetd' => 'none', 'rewardnumtd' => '']],
			[5, $lang['setting_credits_policy_cycletype_5'], ['cycletimetd' => 'none', 'rewardnumtd' => '']],
			[6, $lang['setting_credits_policy_cycletype_6'], ['cycletimetd' => 'none', 'rewardnumtd' => '']],
			[2, $lang['setting_credits_policy_cycletype_2'], ['cycletimetd' => '', 'rewardnumtd' => '']],
			[3, $lang['setting_credits_policy_cycletype_3'], ['cycletimetd' => '', 'rewardnumtd' => '']],
			[7, $lang['setting_credits_policy_cycletype_7'], ['cycletimetd' => '', 'rewardnumtd' => '']],
			[4, $lang['setting_credits_policy_cycletype_4'], ['cycletimetd' => 'none', 'rewardnumtd' => '']]];
		showsetting('setting_credits_policy_cycletype', ['rule[cycletype]', $cycletype], $ruleinfo['cycletype'], 'mradio');
		showtagheader('tbody', 'cycletimetd', in_array($ruleinfo['cycletype'], [2, 3, 7]), 'sub');
		showsetting('credits_edit_cycletime', 'rule[cycletime]', $ruleinfo['cycletime'], 'text');
		showtagfooter('tbody');
		showtagheader('tbody', 'rewardnumtd', in_array($ruleinfo['cycletype'], [1, 2, 3, 4, 5, 6, 7]), 'sub');
		showsetting('credits_edit_rewardnum', 'rule[rewardnum]', $ruleinfo['rewardnum'], 'text');
		showtagfooter('tbody');
	}
	if(!$isSub) {
		for($i = 1; $i <= 8; $i++) {
			if($_G['setting']['extcredits'][$i]) {
				if($rid) {
					showsetting("extcredits{$i}(".$_G['setting']['extcredits'][$i]['title'].')', "rule[extcredits{$i}]", $ruleinfo['extcredits'.$i], 'text', '', 0, $fid ? '('.$lang['credits_edit_globalrule'].':'.$globalrule['extcredits'.$i].')' : '');
				} else {
					showsetting("extcredits{$i}(".$_G['setting']['extcredits'][$i]['title'].')', "rule[extcredits{$i}]", $_G['setting']['creditspolicy']['lowerlimit'][$i], 'text');
				}
			}
		}
	}
	showtablefooter();
	showtableheader('', 'nobottom');
	showsubmit('rulesubmit');
	showtablefooter();
	showformfooter();
} else {
	$rid = $_GET['rid'];
	$rule = $_GET['rule'];
	if($rid) {
		if(!$rule['cycletype']) {
			$rule['cycletime'] = 0;
			$rule['rewardnum'] = 1;
		}
		// 验证积分策略是否重复 开始
		if($rule['usecustom']) {
			list($mainAction, $sub) = explode('/', $ruleinfo['action']);
			if($ruleinfo['cycletype'] != $rule['cycletype']) {
				foreach($policy as $pkey => $pvalue) {
					list($mainPolicyAction, $policySub) = explode('/', $pkey);
					if($mainPolicyAction == $mainAction && $pvalue['cycletype'] == $rule['cycletype']) {
						cpmsg('credits_duplicate_custom', '', 'error');
					}
				}
			}
			if($globalrule['cycletype'] != $rule['cycletype']) {
				$rules = table_common_credit_rule::t()->fetch_all_by_action([$mainAction]);
				foreach($rules as $rkey => $rvalue) {
					if($rvalue['cycletype'] == $rule['cycletype']) {
						cpmsg('credits_duplicate_default', '', 'error');
					}
				}
			}
		}
		// 验证积分策略是否重复 结束

		$havecredit = $rule['usecustom'];
		for($i = 1; $i <= 8; $i++) {
			if(!$_G['setting']['extcredits'][$i]) {
				$rule['extcredits'.$i] = 0;
			}
		}
		foreach($rule as $key => $val) {
			if($key == 'rulename'){
				$rule[$key] = addslashes($val);
			}else {
				$rule[$key] = intval($val);
			}
		}
		if($fid) {
			$fids = $globalrule['fids'] ? explode(',', $globalrule['fids']) : [];
			if($havecredit) {
				$rule['rid'] = $rid;
				$rule['fid'] = $fid;
				$rule['rulename'] = $ruleinfo['rulename'];
				$rule['action'] = $ruleinfo['action'];
				$policy[$ruleinfo['action']] = $rule;
				if(!in_array($fid, $fids)) {
					$fids[] = $fid;
				}
			} else {
				if($rule['cycletype'] != 0 && ($rule['cycletype'] == 4 && !$rule['rewardnum'])) {
					require_once DISCUZ_ROOT.'./source/class/class_credit.php';
					credit::deletelogbyfid($rid, $fid);
				}
				unset($policy[$ruleinfo['action']]);
				if(in_array($fid, $fids)) {
					unset($fids[array_search($fid, $fids)]);
				}
			}
			table_forum_forumfield::t()->update($fid, ['creditspolicy' => serialize($policy)]);
			table_common_credit_rule::t()->update($rid, ['fids' => implode(',', $fids)]);
			updatecache(['forums', 'creditrule']);
			cpmsg('credits_update_succeed', 'action=forums&operation=edit&anchor=credits&fid='.$fid, 'succeed');
		} else {
			table_common_credit_rule::t()->update($rid, $rule);
		}
		updatecache('creditrule');
	} else {
		$lowerlimit['creditspolicy']['lowerlimit'] = [];
		for($i = 1; $i <= 8; $i++) {
			if($_G['setting']['extcredits'][$i]) {
				$lowerlimit['creditspolicy']['lowerlimit'][$i] = (float)$rule['extcredits'.$i];
			}
		}
		table_common_setting::t()->update_setting('creditspolicy', $lowerlimit['creditspolicy']);
		updatecache(['setting', 'creditrule']);
	}
	cpmsg('credits_update_succeed', 'action=credits&operation=list&anchor=policytable', 'succeed');
}
	