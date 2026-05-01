<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_usergroups() {
	global $_G;

	$data_uf = table_common_usergroup_field::t()->fetch_all_fields(null, ['groupid', 'readaccess', 'allowgetattach', 'allowgetimage', 'allowmediacode', 'maxsigsize', 'allowbegincode']);

	$data = [];
	foreach(table_common_usergroup::t()->range_orderby_creditshigher() as $key => $value) {
		$group = array_merge(['groupid' => $value['groupid'], 'upgroupid' => $value['upgroupid'], 'type' => $value['type'], 'grouptitle' => $value['grouptitle'], 'creditshigher' => $value['creditshigher'], 'creditslower' => $value['creditslower'], 'stars' => $value['stars'], 'color' => $value['color'], 'icon' => $value['icon'], 'system' => $value['system']], $data_uf[$key]);
		if($group['type'] == 'special') {
			if($group['system'] != 'private') {
				list($dailyprice) = explode("\t", $group['system']);
				$group['pubtype'] = $dailyprice > 0 ? 'buy' : 'free';
			}
		}
		unset($group['system']);
		$groupid = $group['groupid'];
		$group['grouptitle'] = $group['color'] ? '<font color="'.$group['color'].'">'.$group['grouptitle'].'</font>' : $group['grouptitle'];
		if($_G['setting']['userstatusby'] == 1) {
			$group['userstatusby'] = 1;
		} elseif($_G['setting']['userstatusby'] == 2) {
			if($group['type'] != 'member') {
				$group['userstatusby'] = 1;
			} else {
				$group['userstatusby'] = 2;
			}
		}
		if($group['type'] != 'member' && $value['upgroupid'] == 0) {
			unset($group['creditshigher'], $group['creditslower']);
		}
		unset($group['groupid']);
		$data[$groupid] = $group;
	}
	savecache('usergroups', $data);

	build_cache_usergroups_single();

	foreach(table_common_admingroup::t()->range() as $data) {
		savecache('admingroup_'.$data['admingid'], $data);
	}
}

function build_cache_usergroups_single() {
	global $_G;

	$pluginvalue = pluginsettingvalue('groups');
	$stylevalue = stylesettingvalue('groups');
	$allowthreadplugin = table_common_setting::t()->fetch_setting('allowthreadplugin', true);

	$data_uf = table_common_usergroup_field::t()->range();
	$data_ag = table_common_admingroup::t()->range();
	foreach(table_common_usergroup::t()->range() as $gid => $data) {
		$data = array_merge($data, (array)$data_uf[$gid], (array)$data_ag[$gid]);
		$ratearray = [];
		if($data['raterange']) {
			foreach(explode("\n", $data['raterange']) as $rating) {
				$rating = explode("\t", $rating);
				$ratearray[$rating[0]] = ['isself' => $rating[1], 'min' => $rating[2], 'max' => $rating[3], 'mrpd' => $rating[4]];
			}
		}
		$data['raterange'] = $ratearray;
		$data['grouptitle'] = $data['color'] ? '<font color="'.$data['color'].'">'.$data['grouptitle'].'</font>' : $data['grouptitle'];
		$data['grouptype'] = $data['type'];
		$data['grouppublic'] = $data['system'] != 'private';
		$data['groupcreditshigher'] = $data['creditshigher'];
		$data['groupcreditslower'] = $data['creditslower'];
		$data['allowthreadplugin'] = !empty($allowthreadplugin[$data['groupid']]) ? $allowthreadplugin[$data['groupid']] : [];
		$data['plugin'] = $pluginvalue[$data['groupid']];
		$data['style'] = $stylevalue[$data['groupid']];
		if(!empty($data['creditsformula'])) {
			if(!checkformulacredits($data['creditsformula'])) {
				$data['creditsformula'] = '';
			} else {
				$data['creditsformula'] = preg_replace('/(friends|doings|blogs|albums|polls|sharings|digestposts|posts|threads|oltime|extcredits[1-8])/', "\$member['\\1']", $data['creditsformula']);
				$data['creditsformulaexp'] = $data['creditsformula'];
				foreach(['digestposts', 'posts', 'threads', 'oltime', 'friends', 'doings', 'blogs', 'albums', 'polls', 'sharings', 'extcredits1', 'extcredits2', 'extcredits3', 'extcredits4', 'extcredits5', 'extcredits6', 'extcredits7', 'extcredits8'] as $var) {
					if($_G['setting']['extcredits'][$creditsid = preg_replace('/^extcredits(\d{1})$/', "\\1", $var)]) {
						$replacement = $_G['setting']['extcredits'][$creditsid]['title'];
					} else {
						$replacement = lang('spacecp', 'credits_formula_'.$var);
					}
					$data['creditsformulaexp'] = str_replace('$member[\''.$var.'\']', '<u>'.$replacement.'</u>', $data['creditsformulaexp']);
				}
				$data['creditsformulaexp'] = addslashes('<u>'.$_G['setting']['upgroup_name'][$data['groupid']].lang('spacecp', 'credits_formula_credits').'</u>='.$data['creditsformulaexp']);
			}
		}
		unset($data['type'], $data['system'], $data['creditshigher'], $data['creditslower'], $data['groupavatar'], $data['admingid']);
		if(!empty($data['fields'])) {
			$data['fields'] = json_decode($data['fields'], true);
		}
		savecache('usergroup_'.$data['groupid'], $data);
	}
}