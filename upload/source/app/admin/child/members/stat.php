<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($_GET['do'] == 'stepstat' && $_GET['t'] > 0 && $_GET['i'] > 0) {
	$t = intval($_GET['t']);
	$i = intval($_GET['i']);
	$o = $i - 1;
	$value = table_common_member_stat_field::t()->fetch_all_by_fieldid($_GET['fieldid'], $o, 1);
	if($value) {
		$optionid = intval($value[0]['optionid']);
		$fieldvalue = $value[0]['fieldvalue'];
	} else {
		$optionid = 0;
		$fieldvalue = '';
	}
	$cnt = ($_GET['fieldid'] === 'groupid') ? table_common_member::t()->count_by_groupid($fieldvalue) : table_common_member_profile::t()->count_by_field($_GET['fieldid'], $fieldvalue);
	table_common_member_stat_field::t()->update($optionid, ['users' => $cnt, 'updatetime' => TIMESTAMP]);
	if($i < $t) {
		cpmsg('members_stat_do_stepstat', 'action=members&operation=stat&fieldid='.$_GET['fieldid'].'&do=stepstat&t='.$t.'&i='.($i + 1), '', ['t' => $t, 'i' => $i]);
	} else {
		cpmsg('members_stat_update_data_succeed', 'action=members&operation=stat&fieldid='.$_GET['fieldid'], 'succeed');
	}
}

$options = ['groupid' => cplang('usergroup')];
$fieldids = ['gender', 'birthyear', 'birthmonth', 'constellation', 'zodiac', 'birthcountry', 'residecountry'];
loadcache('profilesetting');
foreach($_G['cache']['profilesetting'] as $fieldid => $value) {
	if($value['formtype'] == 'select' || $value['formtype'] == 'radio' || in_array($fieldid, $fieldids)) {
		$options[$fieldid] = $value['title'];
	}
}

if(!empty($_GET['fieldid']) && !isset($options[$_GET['fieldid']])) {
	cpmsg('members_stat_bad_fieldid', 'action=members&operation=stat', 'error');
}

if(!empty($_GET['fieldid']) && $_GET['fieldid'] == 'groupid') {
	$usergroups = [];
	foreach(table_common_usergroup::t()->range() as $value) {
		$usergroups[$value['groupid']] = $value['grouptitle'];
	}
}

if(!submitcheck('statsubmit')) {

	shownav('user', 'nav_members_stat');
	showsubmenu('nav_members_stat');
	showtips('members_stat_tips');

	showformheader('members&operation=stat&fieldid='.$_GET['fieldid']);
	showtableheader('members_stat_options');
	$option_html = '<ul>';
	foreach($options as $key => $value) {
		$extra_style = $_GET['fieldid'] == $key ? ' font-weight: 900;' : '';
		$option_html .= ''
			."<li style=\"float: left; width: 160px;$extra_style\">"
			."<a href=\"".ADMINSCRIPT."?action=members&operation=stat&fieldid=$key\">$value</a>"
			.'</li>';
	}
	$option_html .= '</ul><br style="clear: both;" />';
	showtablerow('', ['colspan="5"'], [$option_html]);

	if($_GET['fieldid']) {

		$list = [];
		$total = 0;
		foreach(($list = table_common_member_stat_field::t()->fetch_all_by_fieldid($_GET['fieldid'])) as $value) {
			$total += $value['users'];
		}
		for($i = 0, $L = count($list); $i < $L; $i++) {
			if($total) {
				$list[$i]['percent'] = intval(10000 * $list[$i]['users'] / $total) / 100;
			} else {
				$list[$i]['percent'] = 0;
			}
			$list[$i]['width'] = $list[$i]['percent'] ? intval($list[$i]['percent'] * 2) : 1;
		}
		showtablerow('', ['colspan="4"'], [cplang('members_stat_current_field').$options[$_GET['fieldid']].'; '.cplang('members_stat_members').$total]);

		showtablerow('', ['width="200"', '', 'width="160"', 'width="160"'], [
			cplang('members_stat_option'),
			cplang('members_stat_view'),
			cplang('members_stat_option_members'),
			cplang('members_stat_updatetime')
		]);
		foreach($list as $value) {
			if($_GET['fieldid'] == 'groupid') {
				$value['fieldvalue'] = $usergroups[$value['fieldvalue']];
			} elseif($_GET['fieldid'] == 'gender') {
				$value['fieldvalue'] = lang('space', 'gender_'.$value['fieldvalue']);
			} elseif(empty($value['fieldvalue'])) {
				$value['fieldvalue'] = cplang('members_stat_null_fieldvalue');
			}
			showtablerow('', ['width="200"', '', 'width="160"', 'width="160"'], [
				$value['fieldvalue'],
				'<div style="background-color: yellow; width: 200px; height: 20px;"><div style="background-color: red; height: 20px; width: '.$value['width'].'px;"></div></div>',
				$value['users'].' ('.$value['percent'].'%)',
				!empty($value['updatetime']) ? dgmdate($value['updatetime'], 'u') : 'N/A'
			]);
		}

		$optype_html = '<input type="radio" class="radio" name="optype" id="optype_option" value="option" /><label for="optype_option">'.cplang('members_stat_update_option').'</label>&nbsp;&nbsp;'
			.'<input type="radio" class="radio" name="optype" id="optype_data" value="data" /><label for="optype_data">'.cplang('members_stat_update_data').'</label>';
		showsubmit('statsubmit', 'submit', $optype_html);
	}
	showtablefooter();
	showformfooter();

} else {

	if($_POST['optype'] == 'option') {

		$options = $inserts = $hits = $deletes = [];
		foreach(table_common_member_stat_field::t()->fetch_all_by_fieldid($_GET['fieldid']) as $value) {
			$options[$value['optionid']] = $value['fieldvalue'];
			$hits[$value['optionid']] = false;
		}

		$alldata = $_GET['fieldid'] === 'groupid' ? table_common_member::t()->fetch_all_groupid() : table_common_member_profile::t()->fetch_all_field_value($_GET['fieldid']);
		foreach($alldata as $value) {
			$fieldvalue = $value[$_GET['fieldid']];
			$optionid = array_search($fieldvalue, $options);
			if($optionid) {
				$hits[$optionid] = true;
			} else {
				$inserts[] = ['fieldid' => $_GET['fieldid'], 'fieldvalue' => $fieldvalue];
			}
		}
		foreach($hits as $key => $value) {
			if(!$value) {
				$deletes[] = $key;
			}
		}
		if($deletes) {
			table_common_member_stat_field::t()->delete($deletes);

		}
		if($inserts) {
			table_common_member_stat_field::t()->insert_batch($inserts);
		}

		cpmsg('members_stat_update_option_succeed', 'action=members&operation=stat&fieldid='.$_GET['fieldid'], 'succeed');

	} elseif($_POST['optype'] == 'data') {

		if(($t = table_common_member_stat_field::t()->count_by_fieldid($_GET['fieldid'])) > 0) {
			cpmsg('members_stat_do_stepstat_prepared', 'action=members&operation=stat&fieldid='.$_GET['fieldid'].'&do=stepstat&t='.$t.'&i=1', '', ['t' => $t]);
		} else {
			cpmsg('members_stat_update_data_succeed', 'action=members&operation=stat&fieldid='.$_GET['fieldid'], 'succeed');
		}

	} else {
		cpmsg('members_stat_null_operation', 'action=members&operation=stat', 'error');
	}
}
	