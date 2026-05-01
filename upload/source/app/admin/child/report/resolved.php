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
	['report_newreport', 'report', 0],
	['report_resolved', 'report&operation=resolved', 1],
	['report_receiveuser', 'report&operation=receiveuser', 0]
]);
showformheader('report&operation=resolved');
showtableheader();
showsubtitle(['', 'report_detail', 'report_optuser', 'report_opttime']);
$reportcount = table_common_report::t()->fetch_count(1);
$query = table_common_report::t()->fetch_all_report($start, $lpp, 1);
foreach($query as $row) {
	if($row['opresult'] == 'ignore') {
		$opresult = cplang('report_newreport_no_operate');
	} else {
		$row['opresult'] = explode("\t", $row['opresult']);
		if($row['opresult'][1] > 0) {
			$row['opresult'][1] = '+'.$row['opresult'][1];
		}
		$opresult = $_G['setting']['extcredits'][$row['opresult'][0]]['title'].'&nbsp;'.$row['opresult'][1];
	}
	showtablerow('', ['class="td25"', 'class="td28"', '', '', 'class="td26"'], [
		'<input type="checkbox" class="checkbox" name="reportids[]" value="'.$row['id'].'" />',
		'<b>'.cplang('report_newreport_url').'</b><a href="'.$row['url'].'" target="_blank">'.$row['url'].'</a><br><b>'.cplang('report_newreport_time').'</b>'.dgmdate($row['dateline']).'<br><b>'.cplang('report_newreport_message').'</b>: '.$row['message'].'<br \><b>'.cplang('report_resolved_result').'</b>'.$opresult,
		$row['opname'],
		date('y-m-d H:i', $row['optime'])
	]);
}
$multipage = multi($reportcount, $lpp, $page, ADMINSCRIPT."?action=report&operation=resolved&lpp=$lpp", 0, 3);
showsubmit('', '', '', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'reportids\')" /><label for="chkall">'.cplang('del').'</label>&nbsp;&nbsp;<input type="submit" class="btn" name="delsubmit" value="'.$lang['delete'].'" />', $multipage);
showtablefooter();
showformfooter();
	