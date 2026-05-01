<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('tasksubmit')) {

	shownav('extended', 'nav_tasks');
	showsubmenu('nav_tasks', [
		['admin', 'tasks', 1],
		$submenus ? [['menu' => 'add', 'submenu' => $submenus]] : [],
		['nav_task_type', 'tasks&operation=type', 0]
	]);
	showformheader('tasks');
	showtableheader('tasks_list', 'fixpadding');
	showsubtitle(['display_order', 'available', 'name', 'tasks_reward', 'time', 'tasks_status', '']);

	$starttasks = [];
	foreach(table_common_task::t()->fetch_all_data() as $task) {

		if($task['reward'] == 'credit') {
			$reward = cplang('credits').' '.$_G['setting']['extcredits'][$task['prize']]['title'].' '.$task['bonus'].' '.$_G['setting']['extcredits'][$task['prize']]['unit'];
		} elseif($task['reward'] == 'magic') {
			$magicname = table_common_magic::t()->fetch($task['prize']);
			$reward = cplang('tasks_reward_magic').' '.$magicname['name'].' '.$task['bonus'].' '.cplang('magic_unit');
		} elseif($task['reward'] == 'medal') {
			$medalname = table_forum_medal::t()->fetch($task['prize']);
			$reward = cplang('medals').' '.$medalname['name'].($task['bonus'] ? ' '.cplang('validity').$task['bonus'].' '.cplang('days') : '');
		} elseif($task['reward'] == 'invite') {
			$reward = cplang('tasks_reward_invite').' '.$task['prize'].($task['bonus'] ? ' '.cplang('validity').$task['bonus'].' '.cplang('days') : '');
		} elseif($task['reward'] == 'group') {
			$group = table_common_usergroup::t()->fetch($task['prize']);
			$grouptitle = $group['grouptitle'];
			$reward = cplang('usergroup').' '.$grouptitle.($task['bonus'] ? ' '.cplang('validity').' '.$task['bonus'].' '.cplang('days') : '');
		} else {
			$reward = cplang('none');
		}
		if($task['available'] == '1' && (!$task['starttime'] || $task['starttime'] <= TIMESTAMP) && (!$task['endtime'] || $task['endtime'] > TIMESTAMP)) {
			$starttasks[] = $task['taskid'];
		}

		$checked = $task['available'] ? ' checked="checked"' : '';

		if($task['starttime'] && $task['endtime']) {
			$task['time'] = dgmdate($task['starttime'], 'y-m-d H:i').' ~ '.dgmdate($task['endtime'], 'y-m-d H:i');
		} elseif($task['starttime'] && !$task['endtime']) {
			$task['time'] = dgmdate($task['starttime'], 'y-m-d H:i').' '.cplang('tasks_online');
		} elseif(!$task['starttime'] && $task['endtime']) {
			$task['time'] = dgmdate($task['endtime'], 'y-m-d H:i').' '.cplang('tasks_offline');
		} else {
			$task['time'] = cplang('nolimit');
		}

		if($task['available'] == 2 && ($task['starttime'] > TIMESTAMP || ($task['endtime'] && $task['endtime'] <= TIMESTAMP))) {
			$task['available'] = 1;
			table_common_task::t()->update($task['taskid'], ['available' => 1]);
		}
		if($task['available'] == 1 && (!$task['starttime'] || $task['starttime'] <= TIMESTAMP) && (!$task['endtime'] || $task['endtime'] > TIMESTAMP)) {
			$task['available'] = 2;
			table_common_task::t()->update($task['taskid'], ['available' => 2]);
		}

		showtablerow('', ['class="td25"', 'class="td25"'], [
			'<input type="text" class="txt" name="displayordernew['.$task['taskid'].']" value="'.$task['displayorder'].'" size="3" />',
			"<input class=\"checkbox\" type=\"checkbox\" name=\"availablenew[{$task['taskid']}]\" value=\"1\"$checked><input type=\"hidden\" name=\"availableold[{$task['taskid']}]\" value=\"{$task['available']}\">",
			"<input type=\"text\" class=\"txt\" name=\"namenew[{$task['taskid']}]\" size=\"20\" value=\"{$task['name']}\"><input type=\"hidden\" name=\"nameold[{$task['taskid']}]\" value=\"{$task['name']}\">",
			$reward,
			$task['time'].'<input type="hidden" name="scriptnamenew['.$task['taskid'].']" value="'.$task['scriptname'].'">',
			($task['available'] == 1 ? ($task['endtime'] && $task['endtime'] <= TIMESTAMP ? cplang('tasks_status_3') : cplang('tasks_status_1')) : ($task['available'] == 2 ? cplang('tasks_status_2') : cplang('tasks_status_0'))),
			"<a href=\"".ADMINSCRIPT."?action=tasks&operation=edit&id={$task['taskid']}\" class=\"act\">{$lang['edit']}</a>&nbsp;&nbsp;<a href=\"".ADMINSCRIPT."?action=tasks&operation=delete&id={$task['taskid']}\" class=\"act\">{$lang['delete']}</a>"
		]);

	}

	if($starttasks) {
		table_common_task::t()->update($starttasks, ['available' => 2]);
		require_once libfile('class/task');
		$tasklib = &task::instance();
		$tasklib->update_available(1);
	}

	showsubmit('tasksubmit', 'submit');
	showtablefooter();
	showformfooter();

} else {

	$checksettingsok = TRUE;
	if(is_array($_GET['namenew'])) {
		foreach($_GET['namenew'] as $id => $name) {
			$_GET['availablenew'][$id] = $_GET['availablenew'][$id] && (!$starttimenew[$id] || $starttimenew[$id] <= TIMESTAMP) && (!$endtimenew[$id] || $endtimenew[$id] > TIMESTAMP) ? 2 : $_GET['availablenew'][$id];
			$update = ['name' => dhtmlspecialchars($_GET['namenew'][$id]), 'available' => $_GET['availablenew'][$id]];
			if(isset($_GET['displayordernew'][$id])) {
				$update['displayorder'] = $_GET['displayordernew'][$id];
			}
			table_common_task::t()->update($id, $update);
		}
	}

	updatecache('setting');
	require_once libfile('class/task');
	$tasklib = &task::instance();
	$tasklib->update_available(1);

	if($checksettingsok) {
		cpmsg('tasks_succeed', 'action=tasks', 'succeed');
	} else {
		cpmsg('tasks_setting_invalid', '', 'error');
	}

}
	