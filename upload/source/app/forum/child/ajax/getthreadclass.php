<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$fid = intval($_GET['fid']);
$threadclass = '';
if($fid) {
	$option = [];
	$forumfield = table_forum_forumfield::t()->fetch($fid);
	if(!empty($forumfield['threadtypes'])) {
		foreach(table_forum_threadclass::t()->fetch_all_by_fid($fid) as $tc) {
			$option[] = '<option value="'.$tc['typeid'].'">'.$tc['name'].'</option>';
		}
		if(!empty($option)) {
			$threadclass .= '<option value="">'.lang('forum/template', 'modcp_select_threadclass').'</option>';
			$threadclass .= implode('', $option);
		}
	}
}

if(!empty($threadclass)) {
	$threadclass = '<select name="typeid" id="typeid" width="168" class="ps">'.$threadclass.'</select>';
}
include template('common/header_ajax');
echo $threadclass;
include template('common/footer_ajax');
exit;
	