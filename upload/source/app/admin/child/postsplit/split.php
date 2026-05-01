<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!$_G['setting']['bbclosed']) {
	cpmsg('postsplit_forum_must_be_closed', 'action=postsplit&operation=manage', 'error');
}

$tableid = intval($_GET['tableid']);
$tablename = getposttable($tableid);
if($tableid && $tablename != 'forum_post' || !$tableid) {
	$status = helper_dbtool::gettablestatus(getposttable($tableid, true), false);
	$allowsplit = false;

	if($status && ((!$tableid && $status['Data_length'] > 400 * 1048576) || ($tableid && $status['Data_length']))) {

		if(!submitcheck('splitsubmit')) {
			showsubmenu('nav_postsplit_manage');
			/*search={"nav_postsplit":"action=postsplit&operation=manage","nav_postsplit_manage":"action=postsplit&operation=manage"}*/
			showtips('postsplit_manage_tips');
			/*search*/
			showformheader('postsplit&operation=split&tableid='.$tableid);
			showtableheader();
			showsetting('postsplit_from', '', '', getposttable($tableid, true).(!empty($posttable_info[$tableid]['memo']) ? '('.$posttable_info[$tableid]['memo'].')' : ''));
			$tablelist = '<option value="-1">'.cplang('postsplit_create').'</option>';
			foreach($posttable_info as $tid => $info) {
				if($tableid != $tid) {
					$tablestatus = helper_dbtool::gettablestatus(getposttable($tid, true));
					$tablelist .= '<option value="'.$tid.'">'.($info['memo'] ? $info['memo'] : 'forum_post'.($tid ? '_'.$tid : '')).'('.$tablestatus['Data_length'].')'.'</option>';
				}
			}
			showsetting('postsplit_to', '', '', '<select onchange="if(this.value >= 0) {$(\'tableinfo\').style.display = \'none\';} else {$(\'tableinfo\').style.display = \'\';}" name="targettable">'.$tablelist.'</select>');
			showtagheader('tbody', 'tableinfo', true, 'sub');
			showsetting('postsplit_manage_table_memo', 'memo', '', 'text');
			showtagfooter('tbody');

			$datasize = round($status['Data_length'] / 1048576);
			$maxsize = round(($datasize - ($tableid ? 0 : 300)) / 100);
			$maxi = $maxsize > 10 ? 10 : ($maxsize < 1 ? 1 : $maxsize);
			for($i = 1; $i <= $maxi; $i++) {
				$movesize = $i == 10 ? 1024 : $i * 100;
				$maxsizestr .= '<option value="'.$movesize.'">'.($i == 10 ? sizecount($movesize * 1048576) : $movesize.'MB').'</option>';
			}
			showsetting('postsplit_move_size', '', '', '<select name="movesize">'.$maxsizestr.'</select>');

			showsubmit('splitsubmit', 'postsplit_manage_submit');
			showtablefooter();
			showformfooter();
		} else {

			$targettable = intval($_GET['targettable']);
			$createtable = false;
			if($targettable == -1) {
				$maxtableid = getmaxposttableid();
				DB::query('SET SQL_QUOTE_SHOW_CREATE=0', 'SILENT');
				$tableinfo = table_forum_post::t()->show_table_by_tableid(0);
				$createsql = $tableinfo['Create Table'];
				$targettable = $maxtableid + 1;
				$newtable = 'forum_post_'.$targettable;
				$createsql = str_replace(getposttable(), $newtable, $createsql);
				DB::query($createsql);

				$posttable_info[$targettable]['memo'] = $_GET['memo'];
				table_common_setting::t()->update_setting('posttable_info', $posttable_info);
				savecache('posttable_info', $posttable_info);
				update_posttableids();
				$createtable = true;
			}
			$sourcetablearr = gettablefields(getposttable($tableid));
			$targettablearr = gettablefields(getposttable($targettable));
			$fields = array_diff(array_keys($sourcetablearr), array_keys($targettablearr));
			if(!empty($fields)) {
				cpmsg('postsplit_do_error', '', '', ['tableid' => getposttable($targettable, true), 'fields' => implode(',', $fields)]);
			}

			$movesize = intval($_GET['movesize']);
			$movesize = $movesize >= 100 && $movesize <= 1024 ? $movesize : 100;
			$targetstatus = helper_dbtool::gettablestatus(getposttable($targettable, true), false);
			$hash = urlencode(authcode("$tableid\t$movesize\t$targettable\t{$targetstatus['Data_length']}", 'ENCODE'));
			if($createtable) {
				cpmsg('postsplit_table_create_succeed', 'action=postsplit&operation=movepost&fromtable='.$tableid.'&movesize='.$movesize.'&targettable='.$targettable.'&hash='.$hash, 'loadingform');
			} else {
				cpmsg('postsplit_finish', 'action=postsplit&operation=movepost&fromtable='.$tableid.'&movesize='.$movesize.'&targettable='.$targettable.'&hash='.$hash, 'loadingform');
			}

		}
	} else {
		cpmsg('postsplit_unallow', 'action=postsplit');
	}
}
	