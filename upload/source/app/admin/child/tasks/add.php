<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($_GET['script'])) {
	cpmsg('undefined_action');
}

$task_name = $task_description = $task_icon = $task_period = $task_periodtype = $task_conditions = '';
if(in_array($_GET['script'], $custom_scripts)) {
	$escript = explode(':', $_GET['script']);
	if(count($escript) > 1 && preg_match('/^[\w\_:]+$/', $_GET['script'])) {
		include_once DISCUZ_PLUGIN($escript[0]).'/task/task_'.$escript[1].'.php';
		$taskclass = 'task_'.$escript[1];
	} else {
		require_once libfile('task/'.$_GET['script'], 'class');
		$taskclass = 'task_'.$_GET['script'];
	}
	$task = new $taskclass;
	$task_name = lang('task/'.$_GET['script'], $task->name);
	$task_description = lang('task/'.$_GET['script'], $task->description);
	$task_icon = $task->icon;
	$task_period = $task->period;
	$task_periodtype = $task->periodtype;
	$task_conditions = $task->conditions;
} else {
	cpmsg('parameters_error', '', 'error');
}

if(!submitcheck('addsubmit')) {

	echo '<script type="text/javascript" src="'.STATICURL.'js/calendar.js"></script>';
	shownav('extended', 'nav_tasks');
	showsubmenu('nav_tasks', [
		['admin', 'tasks', 0],
		[['menu' => 'add', 'submenu' => $submenus], 1],
		['nav_task_type', 'tasks&operation=type', 0]
	]);

	showformheader('tasks&operation=add&script='.$_GET['script']);
	showtableheader('tasks_add_basic', 'fixpadding');
	showsetting('tasks_add_name', 'name', $task_name, 'text');
	showsetting('tasks_add_desc', 'description', $task_description, 'textarea');
	if(count($escript) > 1 && file_exists(DISCUZ_PLUGIN($escript[0]).'/task/task_'.$escript[1].'.gif')) {
		$defaulticon = 'source/plugin/'.$escript[0].'/task/task_'.$escript[1].'.gif';
	} else {
		$defaulticon = STATICURL.'/image/task/task.gif';
	}
	showsetting('tasks_add_icon', 'iconnew', $task_icon, 'text', '', 0, cplang('tasks_add_icon_comment', ['defaulticon' => $defaulticon]));
	showsetting('tasks_add_starttime', 'starttime', '', 'calendar', '', 0, '', 1);
	showsetting('tasks_add_endtime', 'endtime', '', 'calendar', '', 0, '', 1);
	showsetting('tasks_add_periodtype', ['periodtype', [
		[0, cplang('tasks_add_periodtype_hour')],
		[1, cplang('tasks_add_periodtype_day')],
		[2, cplang('tasks_add_periodtype_week')],
		[3, cplang('tasks_add_periodtype_month')],
	]], $task_periodtype, 'mradio');
	showsetting('tasks_add_period', 'period', $task_period, 'text');
	showsetting('tasks_add_reward', ['reward', [
		['', cplang('none'), ['reward_credit' => 'none', 'reward_magic' => 'none', 'reward_medal' => 'none', 'reward_group' => 'none', 'reward_invite' => 'none']],
		['credit', cplang('credits'), ['reward_credit' => '', 'reward_magic' => 'none', 'reward_medal' => 'none', 'reward_group' => 'none', 'reward_invite' => 'none']],
		$_G['setting']['magicstatus'] ? ['magic', cplang('tasks_reward_magic'), ['reward_credit' => 'none', 'reward_magic' => '', 'reward_medal' => 'none', 'reward_group' => 'none', 'reward_invite' => 'none']] : '',
		$_G['setting']['medalstatus'] ? ['medal', cplang('medals'), ['reward_credit' => 'none', 'reward_magic' => 'none', 'reward_medal' => '', 'reward_group' => 'none', 'reward_invite' => 'none']] : '',
		$_G['setting']['regstatus'] > 1 ? ['invite', cplang('tasks_reward_invite'), ['reward_credit' => 'none', 'reward_magic' => 'none', 'reward_medal' => 'none', 'reward_group' => 'none', 'reward_invite' => '']] : '',
		['group', cplang('tasks_add_group'), ['reward_credit' => 'none', 'reward_magic' => 'none', 'reward_medal' => 'none', 'reward_group' => '', 'reward_invite' => 'none']]
	]], '', 'mradio');

	$extcreditarray = [[0, cplang('select')]];
	foreach($_G['setting']['extcredits'] as $creditid => $extcredit) {
		$extcreditarray[] = [$creditid, $extcredit['title']];
	}

	showtagheader('tbody', 'reward_credit');
	showsetting('tasks_add_extcredit', ['prize_credit', $extcreditarray], 0, 'select');
	showsetting('tasks_add_credits', 'bonus_credit', '0', 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'reward_magic');
	showsetting('tasks_add_magicname', ['prize_magic', table_common_magic::t()->fetch_all_name_by_available()], 0, 'select');
	showsetting('tasks_add_magicnum', 'bonus_magic', '0', 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'reward_medal');
	showsetting('tasks_add_medalname', ['prize_medal', table_forum_medal::t()->fetch_all_name_by_available()], 0, 'select');
	showsetting('tasks_add_medalexp', 'bonus_medal', '', 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'reward_invite');
	showsetting('tasks_add_invitenum', 'prize_invite', '1', 'text');
	showsetting('tasks_add_inviteexp', 'bonus_invite', '10', 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'reward_group');
	showsetting('tasks_add_group', ['prize_group', table_common_usergroup::t()->fetch_all_by_type('special', 0)], 0, 'select');

	showsetting('tasks_add_groupexp', 'bonus_group', '', 'text');
	showtagfooter('tbody');

	showtitle('tasks_add_appyperm');
	showsetting('tasks_add_groupperm', ['grouplimit', [
		['all', cplang('tasks_add_group_all'), ['specialgroup' => 'none']],
		['member', cplang('tasks_add_group_member'), ['specialgroup' => 'none']],
		['admin', cplang('tasks_add_group_admin'), ['specialgroup' => 'none']],
		['special', cplang('tasks_add_group_special'), ['specialgroup' => '']]
	]], 'all', 'mradio');
	showtagheader('tbody', 'specialgroup');
	showsetting('tasks_add_usergroup', ['applyperm[]', table_common_usergroup::t()->fetch_all_by_type()], 0, 'mselect');

	showtagfooter('tbody');
	showsetting('tasks_add_maxnum', 'tasklimits', '', 'text');

	if(is_array($task_conditions)) {
		foreach($task_conditions as $taskvarkey => $taskvar) {
			if($taskvar['sort'] == 'apply' && $taskvar['title']) {
				if(!empty($taskvar['value']) && is_array($taskvar['value'])) {
					foreach($taskvar['value'] as $k => $v) {
						$taskvar['value'][$k][1] = lang('task/'.$_GET['script'], $taskvar['value'][$k][1]);
					}
				}
				$varname = in_array($taskvar['type'], ['mradio', 'mcheckbox', 'select', 'mselect']) ?
					($taskvar['type'] == 'mselect' ? [$taskvarkey.'[]', $taskvar['value']] : [$taskvarkey, $taskvar['value']])
					: $taskvarkey;
				$comment = lang('task/'.$_GET['script'], $taskvar['title'].'_comment');
				$comment = $comment != $taskvar['title'].'_comment' ? $comment : '';
				showsetting(lang('task/'.$_GET['script'], $taskvar['title']).':', $varname, $taskvar['value'], $taskvar['type'], '', 0, $comment);
			}
		}
	}

	showtitle('tasks_add_conditions');

	if(in_array($_GET['script'], $custom_scripts)) {

		$haveconditions = false;
		if(is_array($task_conditions)) {
			foreach($task_conditions as $taskvarkey => $taskvar) {
				if($taskvar['sort'] == 'complete' && $taskvar['title']) {
					if(!empty($taskvar['value']) && is_array($taskvar['value'])) {
						foreach($taskvar['value'] as $k => $v) {
							$taskvar['value'][$k][1] = lang('task/'.$_GET['script'], $taskvar['value'][$k][1]);
						}
					}
					$haveconditions = true;
					$varname = in_array($taskvar['type'], ['mradio', 'mcheckbox', 'select', 'mselect']) ?
						($taskvar['type'] == 'mselect' ? [$taskvarkey.'[]', $taskvar['value']] : [$taskvarkey, $taskvar['value']])
						: $taskvarkey;
					$comment = lang('task/'.$_GET['script'], $taskvar['title'].'_comment');
					$comment = $comment != $taskvar['title'].'_comment' ? $comment : '';
					showsetting(lang('task/'.$_GET['script'], $taskvar['title']).':', $varname, $taskvar['default'], $taskvar['type'], '', 0, $comment);
				}
			}
		}
		if(!$haveconditions) {
			showtablerow('', 'class="td27" colspan="2"', cplang('nolimit'));
		}
	}

	showsubmit('addsubmit', 'submit');
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
	} elseif(($_GET['endtime'] && $_GET['endtime'] <= TIMESTAMP) || ($_GET['starttime'] && $_GET['endtime'] && $_GET['endtime'] <= $_GET['starttime'])) {
		cpmsg('tasks_time_invalid', '', 'error');
	} elseif($reward && (!$prize || ($reward == 'credit' && !$bonus))) {
		cpmsg('tasks_reward_invalid', '', 'error');
	}
	$data = [
		'relatedtaskid' => $_GET['relatedtaskid'],
		'exclusivetaskid' => $_GET['exclusivetaskid'],
		'available' => 0,
		'name' => $_GET['name'],
		'description' => $_GET['description'],
		'icon' => $_GET['iconnew'],
		'tasklimits' => $_GET['tasklimits'],
		'applyperm' => $applyperm,
		'scriptname' => $_GET['script'],
		'starttime' => $_GET['starttime'],
		'endtime' => $_GET['endtime'],
		'period' => $_GET['period'],
		'periodtype' => $_GET['periodtype'],
		'reward' => $reward,
		'prize' => $prize,
		'bonus' => $bonus,
	];
	$taskid = table_common_task::t()->insert($data, true);

	if(is_array($task_conditions)) {
		foreach($task_conditions as $taskvarkey => $taskvars) {
			if($taskvars['title']) {
				$comment = lang('task/'.$_GET['script'], $taskvars['title'].'_comment');
				$comment = $comment != $taskvars['title'].'_comment' ? $comment : '';
				$data = [
					'taskid' => $taskid,
					'sort' => $taskvars['sort'],
					'name' => lang('task/'.$_GET['script'], $taskvars['title']),
					'description' => $comment,
					'variable' => $taskvarkey,
					'value' => is_array($_GET[''.$taskvarkey]) ? serialize($_GET[''.$taskvarkey]) : $_GET[''.$taskvarkey],
					'type' => $taskvars['type'],
				];
				table_common_taskvar::t()->insert($data);
			}
		}
	}
	require_once libfile('class/task');
	$tasklib = &task::instance();
	$tasklib->update_available(1);

	cpmsg('tasks_succeed', 'action=tasks', 'succeed');

}
	