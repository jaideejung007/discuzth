<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$groupid = !empty($_GET['groupid']) && $_GET['groupid'] > 0 ? dintval($_GET['groupid']) : 0;

$rules = $sub_rules = $grouprules = [];
foreach(table_common_credit_rule::t()->fetch_all_rule() as $value) {
	$_e = explode('#', $value['action']);
	if(count($_e) > 1) {
		$value['action'] = $_e[1];
		$grouprules[$_e[0]][$_e[1]] = $value;
		continue;
	}
	list($action, $sub) = explode('/', $value['action']);
	if($sub) {
		$sub_rules[$action][] = $value;
	} else {
		$rules[$value['rid']] = $value;
	}
}
if(!submitcheck('rulesubmit')) {

	if($groupid) {
		foreach($rules as $rid => $rule) {
			if(!isset($grouprules[$groupid][$rule['action']])) {
				continue;
			}
			for($i = 1; $i <= 8; $i++) {
				$rules[$rid]['extcredits'.$i] = $grouprules[$groupid][$rule['action']]['extcredits'.$i];
			}
		}
	}

	$anchor = in_array($_GET['anchor'], ['base', 'policytable', 'edit']) ? $_GET['anchor'] : 'base';
	$current = [$anchor => 1];
	showsubmenu('setting_credits', [
		['setting_credits_base', 'setting&operation=credits&anchor=base', $current['base']],
		['setting_credits_policy', 'credits&operation=list&anchor=policytable', $current['policytable']],
	]);

	showformheader('credits&operation=list'.($groupid ? '&groupid='.$groupid : ''));
	showboxheader(cplang('setting_credits_policy').' '.getgroupselect($groupid), 'nobottom', 'id="policytable"'.($anchor != 'policytable' ? ' style="display: none"' : ''));
	showtableheader();
	echo '<tr class="header"><th class="td28 nowrap">'.$lang['setting_credits_policy_name'].'</th><th class="td28 nowrap">'.$lang['setting_credits_policy_cycletype'].'</th><th class="td28 nowrap">'.$lang['setting_credits_policy_rewardnum'].'</th>';
	for($i = 1; $i <= 8; $i++) {
		if($_G['setting']['extcredits'][$i]) {
			echo "<th class=\"td25\" id=\"policy$i\" ".($_G['setting']['extcredits'][$i] ? '' : 'disabled')." valign=\"top\">".$_G['setting']['extcredits'][$i]['title'].'</th>';
		}
	}
	echo '<th class="td25">&nbsp;</th></tr>';

	foreach($rules as $rid => $rule) {
		showrulerow($rule);
		if(!$groupid && isset($sub_rules[$rule['action']])) {
			foreach($sub_rules[$rule['action']] as $sub_rule) {
				showrulerow($sub_rule, 1);
			}
		}
	}
	showtablerow('', 'class="lineheight" colspan="9"', $lang['setting_credits_policy_comment']);
	showtablefooter();
	showboxfooter();
	showtableheader('', 'nobottom', '');
	showsetting('setting_credits_policy_mobile', 'settingnew[creditspolicymobile]', $_G['setting']['creditspolicymobile'], 'text');
	showsubmit('rulesubmit');
	showtablefooter();
	showformfooter();
} else {
	if(!$groupid) {
		foreach($_GET['credit'] as $rid => $credits) {
			$rule = [];
			for($i = 1; $i <= 8; $i++) {
				if($_G['setting']['extcredits'][$i]) {
					$rule['extcredits'.$i] = $credits[$i];
				}
			}
			table_common_credit_rule::t()->update($rid, $rule);
		}
	} else {
		foreach($_GET['credit'] as $rid => $credits) {

			$rule = [];
			for($i = 1; $i <= 8; $i++) {
				if($_G['setting']['extcredits'][$i]) {
					$rule['extcredits'.$i] = $credits[$i];
				}
			}

			$diff = false;
			for($i = 1; $i <= 8; $i++) {
				if($_G['setting']['extcredits'][$i]) {
					if($rules[$rid]['extcredits'.$i] != $credits[$i]) {
						$diff = true;
						break;
					}
				}
			}
			if($diff) {
				if(isset($grouprules[$groupid][$rules[$rid]['action']])) {
					//debug([$grouprules[$groupid][$rules[$rid]['action']]['rid'], $rule]);
					table_common_credit_rule::t()->update($grouprules[$groupid][$rules[$rid]['action']]['rid'], $rule);
				} else {
					$rule['action'] = $groupid.'#'.$rules[$rid]['action'];
					//debug($rule);
					table_common_credit_rule::t()->insert($rule);
				}
			} else {
				if(isset($grouprules[$groupid][$rules[$rid]['action']])) {
					//debug('del='.$grouprules[$groupid][$rules[$rid]['action']]['rid']);
					table_common_credit_rule::t()->delete($grouprules[$groupid][$rules[$rid]['action']]['rid']);
				}
			}



		}
	}
	$settings = [
		'creditspolicymobile' => $_GET['settingnew']['creditspolicymobile'],
	];
	table_common_setting::t()->update_batch($settings);
	updatecache(['setting', 'creditrule']);
	cpmsg('credits_update_succeed', 'action=credits&operation=list&anchor=policytable'.($groupid ? '&groupid='.$groupid : ''), 'succeed');
}


function showrulerow($rule, $sub = 0) {
	global $_G, $lang, $groupid;
	$tdarr = [$sub ? '<div class="board">&nbsp;</div>' : $rule['rulename'], $rule['rid'] ? $lang['setting_credits_policy_cycletype_'.$rule['cycletype']] : 'N/A', $rule['rid'] && $rule['cycletype'] ? $rule['rewardnum'] : 'N/A'];
	for($i = 1; $i <= 8; $i++) {
		if($_G['setting']['extcredits'][$i]) {
			array_push($tdarr, $sub ? '' : '<input name="credit['.$rule['rid'].']['.$i.']" class="txt" value="'.$rule['extcredits'.$i].'" />');
		}
	}
	if(!$groupid) {
		$opstr = '<a href="'.ADMINSCRIPT.'?action=credits&operation=edit&rid='.$rule['rid'].'" title="" class="act">'.$lang['edit'].'</a>';
		if($sub) {
			$opstr .= '<a href="'.ADMINSCRIPT.'?action=credits&operation=del&rid='.$rule['rid'].'" title="" class="act">'.$lang['delete'].'</a>';
		} else {
			$opstr .= '<a href="'.ADMINSCRIPT.'?action=credits&operation=add&rid='.$rule['rid'].'" title="" class="act">'.$lang['setting_credits_add_sub'].'</a>';
		}
	}
	array_push($tdarr, $opstr);
	showtablerow('', array_fill(0, count($_G['setting']['extcredits']) + 4, 'class="td25"'), $tdarr);
}

function getgroupselect($value) {
	global $lang;

	$return = '<select class="smallfont" onchange="if(this.value) {window.location.href=\''.ADMINSCRIPT.'?action=credits&operation=list&anchor=policytable&groupid=\'+this.value}">'.
		'<option value="-1"'.(!$value ? ' selected' : '').'>'.cplang('setting_credits_policy_global').'</option>';

	$query = table_common_usergroup::t()->range_orderby_credit();
	$groupselect = [];
	foreach($query as $group) {
		$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
		$groupselect[$group['type']] .= '<option value="'.$group['groupid'].'"'.($value == $group['groupid'] ? ' selected' : '').'>'.$group['grouptitle'].'</option>';
	}
	$return .= '<optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
		($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
		($groupselect['specialadmin'] ? '<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
		'<optgroup label="'.$lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup></select>';
	return $return;
}