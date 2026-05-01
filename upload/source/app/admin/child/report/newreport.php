<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showsubmenu('nav_report', [
	['report_newreport', 'report', 1],
	['report_resolved', 'report&operation=resolved', 0],
	['report_receiveuser', 'report&operation=receiveuser', 0]
]);
/*search={"nav_report":"action=report"}*/
showtips('report_tips');
/*search*/
showformheader('report&operation=newreport');
showtableheader();
$curcredits = $_G['setting']['creditstransextra'][8] ? $_G['setting']['creditstransextra'][8] : $_G['setting']['creditstrans'];
$report_reward = dunserialize($_G['setting']['report_reward']);
$offset = abs(ceil(($report_reward['max'] - $report_reward['min']) / 10));
if($report_reward['max'] > $report_reward['min']) {
	for($vote = $report_reward['max']; $vote >= $report_reward['min']; $vote -= $offset) {
		if($vote != 0) {
			$rewardlist .= $vote ? '<option value="'.$vote.'">'.($vote > 0 ? '+'.$vote : $vote).'</option>' : '';
		} else {
			$rewardlist .= '<option value="0" selected>'.cplang('report_newreport_no_operate').'</option>';
		}
	}
}
showsubtitle(['', 'report_detail', 'report_user', ($report_reward['max'] != $report_reward['min'] ? 'operation' : '')]);
$reportcount = table_common_report::t()->fetch_count();
$query = table_common_report::t()->fetch_all_report($start, $lpp);
foreach($query as $row) {
	$tmp = itemview_parse($row['url']);
	$itemview = !$tmp ? '' : ('<br><b>'.cplang('report_newreport_view').'</b><br>'.$tmp);
	showtablerow('', ['class="td25"', 'class="td28"', '', ''], [
		'<input type="checkbox" class="checkbox" name="reportids[]" value="'.$row['id'].'" />',
		'<b>'.cplang('report_newreport_url').'</b><a href="'.$row['url'].'" target="_blank">'.$row['url'].'</a><br \><b>'.cplang('report_newreport_time').'</b>'.dgmdate($row['dateline']).$itemview.'<br><b>'.cplang('report_newreport_message').'</b><br>'.$row['message'],
		'<a href="home.php?mod=space&uid='.$row['uid'].'">'.$row['username'].'</a><input type="hidden" name="reportuids['.$row['id'].']" value="'.$row['uid'].'">',
		($report_reward['max'] != $report_reward['min'] ? $_G['setting']['extcredits'][$curcredits]['title'].':&nbsp;<select name="creditsvalue['.$row['id'].']">'.$rewardlist.'</select><br /><br />'.cplang('report_note').':&nbsp;<input type="text" name="msg['.$row['id'].']" value="">' : '')
	]);
}
$multipage = multi($reportcount, $lpp, $page, ADMINSCRIPT."?action=report&lpp=$lpp", 0, 3);

showsubmit('', '', '', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'reportids\')" /><label for="chkall">'.cplang('select_all').'</label>&nbsp;&nbsp;<input type="submit" class="btn" name="delsubmit" value="'.$lang['delete'].'" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="btn" name="resolvesubmit" value="'.cplang('report_newreport_resolve').'" />', $multipage);
showtablefooter();
showformfooter();
	