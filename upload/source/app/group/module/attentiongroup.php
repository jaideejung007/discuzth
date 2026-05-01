<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$_GET['handlekey'] = 'attentiongroup';
require_once libfile('function/group');
$usergroups = update_usergroups($_G['uid']);
$attentiongroup = !empty($_G['member']['attentiongroup']) ? explode(',', $_G['member']['attentiongroup']) : [];
$counttype = count($attentiongroup);
if(submitcheck('attentionsubmit')) {
	if(is_array($_GET['attentiongroupid'])) {
		$_GET['attentiongroupid'] = array_slice($_GET['attentiongroupid'], 0, 5);
		table_common_member_field_forum::t()->update($_G['uid'], ['attentiongroup' => implode(',', $_GET['attentiongroupid'])]);
	} else {
		table_common_member_field_forum::t()->update($_G['uid'], ['attentiongroup' => '']);
	}
	showmessage('setup_finished', 'group.php?mod=my&view=groupthread');
}
include template('group/group_attentiongroup');

