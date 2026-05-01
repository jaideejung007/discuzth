<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

shownav('topic', 'nav_recyclebin');

if(!submitcheck('delsubmit') && !submitcheck('undelsubmit')) {

	showsubmenu('nav_recyclebin', [
		['recyclebin_list', 'recyclebin', 1],
		['search', 'recyclebin&operation=search', 0],
		['clean', 'recyclebin&operation=clean', 0]
	]);
	$lpp = empty($_GET['lpp']) ? 20 : $_GET['lpp'];
	$start = ($page - 1) * $lpp;
	$start_limit = ($page - 1) * $lpp;
	$checklpp = [];
	$checklpp[$lpp] = 'selected="selected"';
	showformheader('recyclebin');
	showtableheader($lang['recyclebin_list'].
		'&nbsp<select onchange="if(this.options[this.selectedIndex].value != \'\') {window.location=\''.ADMINSCRIPT.'?action=recyclebin&lpp=\'+this.options[this.selectedIndex].value }">
				<option value="20" '.$checklpp[20].'> '.$lang['perpage_20'].' </option><option value="50" '.$checklpp[50].'>'.$lang['perpage_50'].'</option><option value="100" '.$checklpp[100].'>'.$lang['perpage_100'].'</option></select>');
	showsubtitle(['', 'thread', 'recyclebin_list_thread', 'recyclebin_list_author', 'recyclebin_list_status', 'recyclebin_list_lastpost', 'recyclebin_list_operation', 'reason']);
	$fids = $threadlist = [];
	$threads = table_forum_thread::t()->fetch_all_by_tid_fid_displayorder(0, 0, -1, 'dateline', $start_limit, $lpp, '=');
	foreach($threads as $tid => $value) {
		$fids[$value['fid']] = $value['fid'];
	}
	if($fids) {
		$forums = table_forum_forum::t()->fetch_all_name_by_fid($fids);
		foreach($threads as $tid => $thread) {
			$thread['forumname'] = $forums[$thread['fid']]['name'];
			$thread['modthreadkey'] = modauthkey($thread['tid']);
			$threadlist[$thread['tid']] = $thread;
		}
	}
	if($threadlist) {
		$tids = array_keys($threadlist);
		foreach(table_forum_threadmod::t()->fetch_all_by_tid($tids) as $row) {
			if(empty($threadlist[$row['tid']]['moduid'])) {
				$threadlist[$row['tid']]['moduid'] = $row['uid'];
				$threadlist[$row['tid']]['modusername'] = $row['username'];
				$threadlist[$row['tid']]['moddateline'] = $row['dateline'];
				$threadlist[$row['tid']]['modaction'] = $row['action'];
				$threadlist[$row['tid']]['reason'] = $row['reason'];
			}
		}
		foreach($threadlist as $tid => $thread) {
			showtablerow('', ['class="td25"', '', '', 'class="td28"', 'class="td28"'], [
				"<input type=\"checkbox\" class=\"checkbox\" name=\"threadlist[]\" value=\"{$thread['tid']}\">",
				'<a href="forum.php?mod=viewthread&tid='.$thread['tid'].'&modthreadkey='.$thread['modthreadkey'].'" target="_blank">'.$thread['subject'].'</a>',
				'<a href="forum.php?mod=forumdisplay&fid='.$thread['fid'].'" target="_blank">'.$thread['forumname'].'</a>',
				'<a href="home.php?mod=space&uid='.$thread['authorid'].'" target="_blank">'.$thread['author'].'</a><br /><em style="font-size:9px;color:#999999;">'.dgmdate($thread['dateline'], 'd').'</em>',
				$thread['replies'].' / '.$thread['views'],
				$thread['lastposter'].'<br /><em style="font-size:9px;color:#999999;">'.dgmdate($thread['lastpost'], 'd').'</em>',
				$thread['modusername'] ? $thread['modusername'].'<br /><em style="font-size:9px;color:#999999;">'.dgmdate($thread['moddateline'], 'd').'</em>' : '',
				$thread['reason']
			]);
		}
	}


	$threadcount = table_forum_thread::t()->count_by_displayorder(-1);
	$multipage = multi($threadcount, $lpp, $page, ADMINSCRIPT."?action=recyclebin&lpp=$lpp", 0, 6);

	showsubmit('', '', '', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'threadlist\')" /><label for="chkall">'.cplang('select_all').'</label>&nbsp;&nbsp;<input type="submit" class="btn" name="delsubmit" value="'.cplang('recyclebin_delete').'" />&nbsp;<input type="submit" class="btn" name="undelsubmit" value="'.cplang('recyclebin_undelete').'" />', $multipage);
	showtablefooter();
	showformfooter();
} else {
	$threadlist = $_GET['threadlist'];
	if(empty($threadlist)) {
		cpmsg('recyclebin_none_selected', 'action=recyclebin', 'error');
	}

	$threadsundel = $threadsdel = 0;
	if(submitcheck('undelsubmit')) {
		$threadsundel = undeletethreads($threadlist);
	} elseif(submitcheck('delsubmit')) {
		require_once libfile('function/delete');
		$threadsdel = deletethread($threadlist);
	}

	cpmsg('recyclebin_succeed', 'action=recyclebin', 'succeed', ['threadsdel' => $threadsdel, 'threadsundel' => $threadsundel]);

}
	