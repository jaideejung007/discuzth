<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($id)) {
	cpmsg('undefined_action');
}

$task = table_common_task::t()->fetch($id);

if(!submitcheck('editsubmit')) {

	echo '<script type="text/javascript" src="'.STATICURL.'js/calendar.js"></script>';
	shownav('extended', 'nav_tasks');
	showchildmenu([['nav_tasks', 'tasks']], $task['name']);

	$escript = explode(':', $task['scriptname']);

	showformheader('tasks&operation=edit&id='.$id);
	showtableheader('', 'fixpadding');
	showsetting('tasks_add_name', 'name', $task['name'], 'text');
	showsetting('tasks_add_desc', 'description', $task['description'], 'textarea');
	if(count($escript) > 1 && preg_match('/^[\w\_:]+$/', $task['scriptname']) && file_exists(DISCUZ_PLUGIN($escript[0]).'/task/task_'.$escript[1].'.gif')) {
		$defaulticon = 'source/plugin/'.$escript[0].'/task/task_'.$escript[1].'.gif';
	} else {
		$defaulticon = 'static/image/task/task.gif';
	}
	showsetting('tasks_add_icon', 'iconnew', $task['icon'], 'text', '', 0, cplang('tasks_add_icon_comment', ['defaulticon' => $defaulticon]));
	showsetting('tasks_add_starttime', 'starttime', $task['starttime'] ? dgmdate($task['starttime'], 'Y-m-d H:i') : '', 'calendar', '', 0, '', 1);
	showsetting('tasks_add_endtime', 'endtime', $task['endtime'] ? dgmdate($task['endtime'], 'Y-m-d H:i') : '', 'calendar', '', 0, '', 1);
	showsetting('tasks_add_periodtype', ['periodtype', [
		[0, cplang('tasks_add_periodtype_hour')],
		[1, cplang('tasks_add_periodtype_day')],
		[2, cplang('tasks_add_periodtype_week')],
		[3, cplang('tasks_add_periodtype_month')],
	]], $task['periodtype'], 'mradio');
	showsetting('tasks_add_period', 'period', $task['period'], 'text');
	showsetting('tasks_add_reward', ['reward', [
		['', cplang('none'), ['reward_credit' => 'none', 'reward_magic' => 'none', 'reward_medal' => 'none', 'reward_group' => 'none']],
		['credit', cplang('credits'), ['reward_credit' => '', 'reward_magic' => 'none', 'reward_medal' => 'none', 'reward_group' => 'none']],
		$_G['setting']['magicstatus'] ? ['magic', cplang('tasks_reward_magic'), ['reward_credit' => 'none', 'reward_magic' => '', 'reward_medal' => 'none', 'reward_group' => 'none']] : '',
		$_G['setting']['medalstatus'] ? ['medal', cplang('medals'), ['reward_credit' => 'none', 'reward_magic' => 'none', 'reward_medal' => '', 'reward_group' => 'none']] : '',
		$_G['setting']['regstatus'] > 1 ? ['invite', cplang('tasks_reward_invite'), ['reward_credit' => 'none', 'reward_magic' => 'none', 'reward_medal' => 'none', 'reward_group' => 'none', 'reward_invite' => '']] : '',
		['group', cplang('tasks_add_group'), ['reward_credit' => 'none', 'reward_magic' => 'none', 'reward_medal' => 'none', 'reward_group' => '']]
	]], $task['reward'], 'mradio');

	$extcreditarray = [[0, cplang('select')]];
	foreach($_G['setting']['extcredits'] as $creditid => $extcredit) {
		$extcreditarray[] = [$creditid, $extcredit['title']];
	}

	showtagheader('tbody', 'reward_credit', $task['reward'] == 'credit');
	showsetting('tasks_add_extcredit', ['prize_credit', $extcreditarray], $task['prize'], 'select');
	showsetting('tasks_add_credits', 'bonus_credit', $task['bonus'], 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'reward_magic', $task['reward'] == 'magic');
	showsetting('tasks_add_magicname', ['prize_magic', table_common_magic::t()->fetch_all_name_by_available()], $task['prize'], 'select');
	showsetting('tasks_add_magicnum', 'bonus_magic', $task['bonus'], 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'reward_medal', $task['reward'] == 'medal');
	showsetting('tasks_add_medalname', ['prize_medal', table_forum_medal::t()->fetch_all_name_by_available()], $task['prize'], 'select');
	showsetting('tasks_add_medalexp', 'bonus_medal', $task['bonus'], 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'reward_invite', $task['reward'] == 'invite');
	showsetting('tasks_add_invitenum', 'prize_invite', $task['prize'], 'text');
	showsetting('tasks_add_inviteexp', 'bonus_invite', $task['bonus'], 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'reward_group', $task['reward'] == 'group');
	showsetting('tasks_add_group', ['prize_group', table_common_usergroup::t()->fetch_all_by_type('special', 0)], $task['prize'], 'select');
	showsetting('tasks_add_groupexp', 'bonus_group', $task['bonus'], 'text');
	showtagfooter('tbody');

	showtitle('tasks_add_appyperm');
	if(!$task['applyperm']) {
		$task['applyperm'] = 'all';
	}
	$task['grouplimit'] = in_array($task['applyperm'], ['all', 'member', 'admin']) ? $task['applyperm'] : 'special';
	showsetting('tasks_add_groupperm', ['grouplimit', [
		['all', cplang('tasks_add_group_all'), ['specialgroup' => 'none']],
		['member', cplang('tasks_add_group_member'), ['specialgroup' => 'none']],
		['admin', cplang('tasks_add_group_admin'), ['specialgroup' => 'none']],
		['special', cplang('tasks_add_group_special'), ['specialgroup' => '']]
	]], $task['grouplimit'], 'mradio');
	showtagheader('tbody', 'specialgroup', $task['grouplimit'] == 'special');
	showsetting('tasks_add_usergroup', ['applyperm[]', table_common_usergroup::t()->fetch_all_by_type()], explode("\t", $task['applyperm']), 'mselect');
	showtagfooter('tbody');
	$tasklist = [0 => ['taskid' => 0, 'name' => cplang('nolimit')]];
	foreach(table_common_task::t()->fetch_all_by_available(2) as $value) {
		if($value['taskid'] != $task['taskid']) {
			$tasklist[$value['taskid']] = ['taskid' => $value['taskid'], 'name' => $value['name']];
		}
	}
	showsetting('tasks_add_relatedtask', ['relatedtaskid', $tasklist], $task['relatedtaskid'], 'select');
	showsetting('tasks_add_exclusivetask', ['exclusivetaskid', $tasklist], $task['exclusivetaskid'], 'select');
	showsetting('tasks_add_maxnum', 'tasklimits', $task['tasklimits'], 'text');

	$taskvars = [];
	foreach(table_common_taskvar::t()->fetch_all_by_taskid($id) as $taskvar) {
		if($taskvar['sort'] == 'apply') {
			$taskvars['apply'][] = $taskvar;
		} elseif($taskvar['sort'] == 'complete') {
			$taskvars['complete'][$taskvar['variable']] = $taskvar;
		} elseif($taskvar['sort'] == 'setting' && $taskvar['name']) {
			$taskvars['setting'][$taskvar['variable']] = $taskvar;
		}
	}

	if($taskvars['apply']) {
		foreach($taskvars['apply'] as $taskvar) {
			showsetting($taskvar['name'], $taskvar['variable'], $taskvar['value'], $taskvar['type'], '', 0, $taskvar['description']);
		}
	}

	showtitle('tasks_add_conditions');

	if(count($escript) > 1 && preg_match('/^[\w\_:]+$/', $task['scriptname'])) {
		include_once DISCUZ_PLUGIN($escript[0]).'/task/task_'.$escript[1].'.php';
		$taskclass = 'task_'.$escript[1];
	} else {
		require_once libfile('task/'.$task['scriptname'], 'class');
		$taskclass = 'task_'.$task['scriptname'];
	}
	$taskcv = new $taskclass;

	if($taskvars['complete']) {
		foreach($taskvars['complete'] as $taskvar) {
			$taskcvar = $taskcv->conditions[$taskvar['variable']];
			if(is_array($taskcvar['value'])) {
				foreach($taskcvar['value'] as $k => $v) {
					$taskcvar['value'][$k][1] = lang('task/'.$task['scriptname'], $taskcvar['value'][$k][1]);
				}
			}
			$varname = in_array($taskvar['type'], ['mradio', 'mcheckbox', 'select', 'mselect']) ?
				($taskvar['type'] == 'mselect' ? [$taskvar['variable'].'[]', $taskcvar['value']] : [$taskvar['variable'], $taskcvar['value']])
				: $taskvar['variable'];
			if(in_array($taskvar['type'], ['mcheckbox', 'mselect'])) {
				$taskvar['value'] = dunserialize($taskvar['value']);
			}
			showsetting($taskvar['name'], $varname, $taskvar['value'], $taskvar['type'], '', 0, $taskvar['description']);
		}
	} else {
		showtablerow('', 'class="td27" colspan="2"', cplang('nolimit'));
	}

	showsubmit('editsubmit', 'submit');
	showtablefooter();
	showformfooter();

} else {

	$applyperm = $_GET['grouplimit'] == 'special' && is_array($_GET['applyperm']) ? implode("\t", $_GET['applyperm']) : $_GET['grouplimit'];
	$_GET['starttime'] = strtotime($_GET['starttime']);
	$_GET['endtime'] = strtotime($_GET['endtime']);
	$reward = $_GET['reward'];
	$prize = $_GET['prize_'.$reward];
	$bonus = $_GET['bonus_'.$reward];

	if(!$_GET['name'] || !$_GET['description']) {
		cpmsg('tasks_basic_invalid', '', 'error');
	} elseif(($_GET['starttime'] != $task['starttime'] || $_GET['endtime'] != $task['endtime']) && (($_GET['endtime'] && $_GET['endtime'] <= TIMESTAMP) || ($_GET['starttime'] && $_GET['endtime'] && $_GET['endtime'] <= $_GET['starttime']))) {
		cpmsg('tasks_time_invalid', '', 'error');
	} elseif($reward && (!$prize || ($reward == 'credit' && !$bonus))) {
		cpmsg('tasks_reward_invalid', '', 'error');
	}

	if($task['available'] == '2' && ($_GET['starttime'] > TIMESTAMP || ($_GET['endtime'] && $_GET['endtime'] <= TIMESTAMP))) {
		table_common_task::t()->update($id, ['available' => 1]);
	}
	if($task['available'] == '1' && (!$_GET['starttime'] || $_GET['starttime'] <= TIMESTAMP) && (!$_GET['endtime'] || $_GET['endtime'] > TIMESTAMP)) {
		table_common_task::t()->update($id, ['available' => 2]);
	}

	$itemarray = [];
	foreach(table_common_taskvar::t()->fetch_all_by_taskid($id, 'IS NOT NULL') as $taskvar) {
		$itemarray[] = $taskvar['variable'];
	}
	table_common_task::t()->update($id, [
		'relatedtaskid' => $_GET['relatedtaskid'],
		'exclusivetaskid' => $_GET['exclusivetaskid'],
		'name' => $_GET['name'],
		'description' => $_GET['description'],
		'icon' => $_GET['iconnew'],
		'tasklimits' => $_GET['tasklimits'],
		'applyperm' => $applyperm,
		'starttime' => $_GET['starttime'],
		'endtime' => $_GET['endtime'],
		'period' => $_GET['period'],
		'periodtype' => $_GET['periodtype'],
		'reward' => $reward,
		'prize' => $prize,
		'bonus' => $bonus,
	]);

	foreach($itemarray as $item) {
		$value = $_GET[''.$item];
		if(in_array($item, ['num', 'time', 'threadid'])) {
			$value = intval($value);
		}
		if($value !== null) {
			table_common_taskvar::t()->update_by_taskid($id, $item, ['value' => is_array($value) ? serialize($value) : $value]);
		}
	}
	require_once libfile('class/task');
	$tasklib = &task::instance();
	$tasklib->update_available(1);

	cpmsg('tasks_succeed', 'action=tasks', 'succeed');

}
	