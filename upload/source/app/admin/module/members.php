<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

@set_time_limit(600);
if($operation != 'export') {
	cpheader();
}

require_once libfile('function/delete');
require_once childfile('members/function');

$_G['setting']['memberperpage'] = 50;
$page = max(1, $_G['page']);
$start_limit = ($page - 1) * $_G['setting']['memberperpage'];
$search_condition = array_merge($_GET, $_POST);

if(!is_array($search_condition['groupid']) && $search_condition['groupid']) {
	$search_condition['groupid'][0] = $search_condition['groupid'];
}
foreach($search_condition as $k => $v) {
	if(in_array($k, ['action', 'operation', 'formhash', 'confirmed', 'submit', 'page', 'deletestart', 'allnum', 'includeuc', 'includepost', 'current', 'pertask', 'lastprocess', 'deleteitem']) || $v === '') {
		unset($search_condition[$k]);
	}
	if($k === 'regip') {
		$search_condition[$k] = ip::to_ip($search_condition[$k]);
	}
}
if(!empty($search_condition['username_his'])) {
	include_once libfile('class/membersearch');
	$ms = new membersearch();
	$sql = $ms->makeset('username', $search_condition['username_his']);
	$uids = DB::fetch_all('SELECT uid FROM %t %i WHERE %i', ['common_member_username_history', $sql['table'], $sql['where']], 'uid');
	if($uids) {
		$search_condition['uid'] .= ($search_condition['uid'] ? ',' : '').implode(',', array_keys($uids));
	} else {
		cpmsg('members_no_find_user', '', 'error');
	}
}
$search_condition = searchcondition($search_condition);
$tmpsearch_condition = $search_condition;
unset($tmpsearch_condition['tablename']);
$member = [];
$tableext = '';
if(in_array($operation, ['ban', 'edit', 'group', 'credit', 'medal', 'access', 'chgusername'], true)) {
	if(empty($_GET['uid']) && empty($_GET['username'])) {
		cpmsg('members_nonexistence', 'action=members&operation='.$operation.(!empty($_GET['highlight']) ? "&highlight={$_GET['highlight']}" : ''), 'form', [], '<input type="text" name="username" value="" class="txt" />');
	}
	$member = !empty($_GET['uid']) ? table_common_member::t()->fetch($_GET['uid'], false, 1) : table_common_member::t()->fetch_by_username($_GET['username'], 1);
	if(!$member) {
		cpmsg('members_edit_nonexistence', '', 'error');
	}
	$tableext = isset($member['_inarchive']) ? '_archive' : '';
}

$file = childfile('members/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}
require_once $file;



