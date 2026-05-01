<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

shownav('founder', 'nav_postsplit');
if(!submitcheck('postsplit_manage')) {

	showsubmenu('nav_postsplit_manage');
	/*search={"nav_postsplit":"action=postsplit&operation=manage","nav_postsplit_manage":"action=postsplit&operation=manage"}*/
	showtips('postsplit_manage_tips');
	/*search*/
	showformheader('postsplit&operation=manage');
	showtableheader();

	showsubtitle(['postsplit_manage_tablename', 'postsplit_manage_datalength', 'postsplit_manage_table_memo', '']);


	$tablename = table_forum_post::t()->getposttable(0, true);
	$tableid = 0;
	$tablestatus = helper_dbtool::gettablestatus($tablename);
	$postcount = $tablestatus['Rows'];
	$data_length = $tablestatus['Data_length'];
	$index_length = $tablestatus['Index_length'];


	$opstr = '<a href="'.ADMINSCRIPT.'?action=postsplit&operation=split&tableid=0">'.cplang('postsplit_name').'</a>';
	showtablerow('', ['', '', '', 'class="td25"'], [$tablename, $data_length, "<input type=\"text\" class=\"txt\" name=\"memo[0]\" value=\"{$posttable_info[0]['memo']}\" />", $opstr]);

	foreach(table_forum_post::t()->show_table() as $table) {
		$tablename = current($table);
		$tableid = gettableid($tablename);
		if(!preg_match('/^\d+$/', $tableid)) {
			continue;
		}
		$tablestatus = helper_dbtool::gettablestatus($tablename);

		$opstr = '<a href="'.ADMINSCRIPT.'?action=postsplit&operation=split&tableid='.$tableid.'">'.cplang('postsplit_name').'</a>';
		showtablerow('', ['', '', '', 'class="td25"'], [$tablename, $tablestatus['Data_length'], "<input type=\"text\" class=\"txt\" name=\"memo[$tableid]\" value=\"{$posttable_info[$tableid]['memo']}\" />", $opstr]);
	}
	showsubmit('postsplit_manage', 'postsplit_manage_update_memo_submit');
	showtablefooter();
	showformfooter();
} else {
	$posttable_info = [];
	foreach($_GET['memo'] as $key => $value) {
		$key = intval($key);
		$posttable_info[$key]['memo'] = dhtmlspecialchars($value);
	}

	table_common_setting::t()->update_setting('posttable_info', $posttable_info);
	savecache('posttable_info', $posttable_info);
	update_posttableids();
	updatecache('setting');

	cpmsg('postsplit_table_memo_update_succeed', 'action=postsplit&operation=manage', 'succeed');
}
	