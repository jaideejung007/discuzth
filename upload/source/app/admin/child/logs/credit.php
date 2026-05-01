<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

include_once libfile('function/credit');
$operationlist = ['TRC', 'RTC', 'RAC', 'MRC', 'TFR', 'RCV', 'CEC', 'ECU', 'SAC', 'BAC', 'PRC', 'RSC', 'STC', 'BTC', 'AFD', 'UGP', 'RPC', 'ACC', 'RCT', 'RCA', 'RCB', 'CDC', 'RKC', 'BME', 'RPR', 'RPZ', 'CHU', 'RUL', 'INV', 'ERR'];

$rdata = [
	'task' => ['TRC'],
	'thread' => ['RTC', 'RAC', 'STC', 'BTC', 'ACC', 'RCT', 'RCA', 'RCB'],
	'member' => ['TFR', 'RCV', 'CEC', 'ECU', 'AFD', 'CDC', 'RKC', 'RPR', 'RPZ', 'CHU'],
	'attach' => ['BAC', 'SAC'],
	'magic' => ['MRC', 'BGC', 'RGC', 'AGC', 'BMC'],
	'medal' => ['BME'],
	'post' => ['PRC', 'RSC'],
	'usergroup' => ['UGP'],
	'report' => ['RPC'],
];

$perpage = max(50, empty($_GET['perpage']) ? 50 : intval($_GET['perpage']));
$start_limit = ($page - 1) * $perpage;

$where = '1';
$pageadd = '';
$begintime = $endtime = $uid = 0;
if($srch_uid = trim($_GET['srch_uid'])) {
	if($uid = max(0, intval($srch_uid))) {
		$where .= " AND l.`uid`='$uid'";
		$pageadd .= '&srch_uid='.$uid;
	} else {
		$srch_uid = '';
	}
} elseif($srch_username = trim($_GET['srch_username'])) {
	$uid = ($uid = table_common_member::t()->fetch_uid_by_username($srch_username)) ? $uid : table_common_member_archive::t()->fetch_uid_by_username($srch_username);
	if($uid) {
		$where .= " AND l.`uid`='$uid'";
		$pageadd .= '&srch_username='.rawurlencode($srch_username);
	} else {
		$srch_username = '';
	}
}
if(($srch_rtype = trim($_GET['srch_rtype'])) && array_key_exists($srch_rtype, $rdata) && isset($_GET['srch_rid']) && ($srch_rid = max(0, intval($_GET['srch_rid'])))) {
	$where .= " AND l.`relatedid`='$srch_rid'";
	$pageadd .= '&srch_rtype='.$srch_rtype.'&srch_rid='.$srch_rid;
}
$optype = '';
if($srch_operation = trim($_GET['srch_operation'])) {
	if(in_array($srch_operation, $operationlist)) {
		$where .= " AND l.`operation`='$srch_operation'";
		$optype = $srch_operation;
		$pageadd .= '&srch_operation='.$srch_operation;
	}
}
if($srch_starttime = trim($_GET['srch_starttime'])) {
	if($starttime = strtotime($srch_starttime)) {
		$where .= " AND l.`dateline`>'$starttime'";
		$begintime = $starttime;
		$pageadd .= '&srch_starttime='.$srch_starttime;
	} else {
		$srch_starttime = '';
	}
}
if($srch_endtime = trim($_GET['srch_endtime'])) {
	if($endtime = strtotime($srch_endtime)) {
		$where .= " AND l.`dateline`<'$endtime'";
		$pageadd .= '&srch_endtime='.$srch_endtime;
	} else {
		$srch_endtime = '';
	}
}
$income = intval($_GET['income']);
$exttype = intval($_GET['exttype']);

$select_operation_html = '<select name="srch_operation">';
$select_operation_html .= '<option>'.cplang('logs_select_operation').'</option>';
foreach($operationlist as $row) {
	$select_operation_html .= '<option value="'.$row.'"'.($row == $srch_operation ? ' selected="selected"' : '').'>'.cplang('logs_credit_update_'.$row).'</option>';
}
$select_operation_html .= '</select>';

$select_rid_html = '<select name="srch_rtype"><option value="">'.$lang['logs_select_ridtype'].'</option>';
foreach($rdata as $k => $v) {
	$select_rid_html .= '<option value="'.$k.'"'.($srch_rtype == $k ? ' selected="selected"' : '').'>'.$lang['logs_'.$k.'_id'].'</option>';
}
$select_rid_html .= '</select>';

showformheader("logs&operation=$operation");

showtableheader('search', 'fixpadding');
showtablerow('', ['class="td23"', 'width="150"', 'class="td23"'],
	[
		cplang('username'), '<input type="text" name="srch_username" class="txt" value="'.$srch_username.'" />',
		cplang('logs_credit_relatedid'), $select_rid_html.'&nbsp;<input type="text" name="srch_rid" class="txt" value="'.$srch_rid.'" />',
	]
);
showtablerow('', ['class="td23"', 'width="150"', 'class="td23"'],
	[
		cplang('uid'), '<input type="text" name="srch_uid" class="txt" value="'.$srch_uid.'" />',
		cplang('time'), '<input type="text" name="srch_starttime" class="txt" value="'.$srch_starttime.'" onclick="showcalendar(event, this)" />- <input type="text" name="srch_endtime" class="txt" value="'.$srch_endtime.'" onclick="showcalendar(event, this)" />',
	]
);
showtablerow('', ['class="td23"', 'width="150"', 'class="td23"'],
	[
		cplang('logs_lpp'), '<input type="text" name="perpage" class="txt" value="'.$perpage.'" size="5" /></label>',
		cplang('type'), $select_operation_html,
	]
);
$select_income_html = '<select name="income"><option>'.cplang('logs_credit_select_no').'</option>';
$select_income_html .= '<option value="1"'.($income == 1 ? ' selected="selected"' : '').'>'.cplang('logs_credit_income_in').'</option>';
$select_income_html .= '<option value="-1"'.($income == -1 ? ' selected="selected"' : '').'>'.cplang('logs_credit_income_out').'</option>';
$select_income_html .= '</select>';

$select_operation_html = '<select name="exttype"><option>'.cplang('logs_credit_select_no').'</option>';
foreach($_G['setting']['extcredits'] as $id => $row) {
	$select_operation_html .= '<option value="'.$id.'"'.($exttype == $id ? ' selected="selected"' : '').'>'.$row['title'].'</option>';
}

showtablerow('', ['class="td23"', 'width="150"', 'class="td23"'],
	[
		cplang('logs_credit_income'), $select_income_html,
		cplang('credits'), $select_operation_html,
	]
);
showtablerow('', ['colspan="4"'], ['<input type="submit" name="srchlogbtn" class="btn" value="'.$lang['search'].'" />']);
showtablefooter();
echo '<script src="'.STATICURL.'js/calendar.js" type="text/javascript"></script>';
showtableheader('', 'fixpadding');
showtablerow('class="header"', ['class="td23"', 'class="td23"', 'class="td23"', 'class="td24"', 'class="td24"', 'class="td24"', 'class="td24"'], [
	cplang('username'),
	cplang('time'),
	cplang('type'),
	cplang('logs_credits_log_update'),
	cplang('logs_credits_log_ac'),
	cplang('detail'),
	cplang('logs_credit_relatedid'),
]);

$num = table_common_credit_log::t()->count_by_search($uid, $optype, $begintime, $endtime, $exttype, $income, $_G['setting']['extcredits'], $srch_rid);

$mpurl = ADMINSCRIPT."?action=logs&operation=$operation".$pageadd;
$multipage = multi($num, $perpage, $page, $mpurl, 0, 3);

$logs = table_common_credit_log::t()->fetch_all_by_search($uid, $optype, $begintime, $endtime, $exttype, $income, $_G['setting']['extcredits'], $start_limit, $perpage, $srch_rid);
$luid = [];
$aids = $pids = $tids = $taskids = $uids = $loglist = [];
loadcache(['magics']);
foreach($logs as $log) {
	$luid[$log['uid']] = $log['uid'];

	$credits = [];
	$havecredit = false;
	$maxid = $minid = 0;
	foreach($_G['setting']['extcredits'] as $id => $credit) {
		if($log['extcredits'.$id]) {
			$havecredit = true;
			if($log['operation'] == 'RPZ') {
				$credits[] = $credit['title'].lang('spacecp', 'credit_update_reward_clean');
			} else {
				$credits[] = $credit['title'].' <span class="'.($log['extcredits'.$id] > 0 ? 'xi1' : 'xg1').'">'.($log['extcredits'.$id] > 0 ? '+' : '').$log['extcredits'.$id].'</span>';
			}
			if($log['operation'] == 'CEC' && !empty($log['extcredits'.$id])) {
				if($log['extcredits'.$id] > 0) {
					$log['maxid'] = $id;
				} elseif($log['extcredits'.$id] < 0) {
					$log['minid'] = $id;
				}
			}

		}
	}
	if(!$havecredit) {
		continue;
	}
	$log['credit'] = implode('<br/>', $credits);
	if(in_array($log['operation'], ['RTC', 'RAC', 'STC', 'BTC', 'ACC', 'RCT', 'RCA', 'RCB'])) {
		$tids[$log['relatedid']] = $log['relatedid'];
	} elseif(in_array($log['operation'], ['SAC', 'BAC'])) {
		$aids[$log['relatedid']] = $log['relatedid'];
	} elseif(in_array($log['operation'], ['PRC', 'RSC'])) {
		$pids[$log['relatedid']] = $log['relatedid'];
	} elseif(in_array($log['operation'], ['TFR', 'RCV'])) {
		$uids[$log['relatedid']] = $log['relatedid'];
	} elseif($log['operation'] == 'TRC') {
		$taskids[$log['relatedid']] = $log['relatedid'];
	}
	$loglist[] = $log;
}
$otherinfo = getotherinfo($aids, $pids, $tids, $taskids, $uids);
$members = table_common_member::t()->fetch_all($luid);
foreach($logs as $log) {
	$log['username'] = $members[$log['uid']]['username'];
	$log['update'] = $log['ac'] = '';
	$haveaccredit = false;
	foreach($_G['setting']['extcredits'] as $id => $credit) {
		if($log['extcredits'.$id]) {
			if($log['operation'] == 'RPZ') {
				$log['update'] .= $credit['title'].$lang['logs_credit_update_reward_clean'].'&nbsp;';
			} else {
				$log['update'] .= $credit['title'].($log['extcredits'.$id] > 0 ? '+' : '').$log['extcredits'.$id].$credit['unit'].'&nbsp;';
			}
			if(isset($log['ac_extcredits'.$id]) && $log['ac_extcredits'.$id]) {
				$haveaccredit = true;
			}
			if(isset($log['ac_extcredits'.$id])) {
				$log['ac'] .= $credit['title'].' '.$log['ac_extcredits'.$id].$credit['unit'].'&nbsp;';
			}
		}
	}
	$related = $rtype = '';
	if(in_array($log['operation'], $rdata['task'])) {
		$rtype = 'task';
		$related = '<a href="home.php?mod=task&do=view&id='.$log['relatedid'].'" target="_blank">'.cplang('logs_task_id').':'.$log['relatedid'].'</a>';
	} elseif(in_array($log['operation'], $rdata['thread'])) {
		$rtype = 'thread';
		$related = '<a href="forum.php?mod=viewthread&tid='.$log['relatedid'].'" target="_blank">'.cplang('logs_thread_id').':'.$log['relatedid'].'</a>';
	} elseif(in_array($log['operation'], $rdata['magic'])) {
		$rtype = 'magic';
		$related = cplang('logs_magic_id').':'.$log['relatedid'];
	} elseif(in_array($log['operation'], $rdata['medal'])) {
		$rtype = 'medal';
		$related = cplang('logs_medal_id').':'.$log['relatedid'];
	} elseif(in_array($log['operation'], $rdata['member'])) {
		$rtype = 'member';
		$related = '<a href="home.php?mod=space&uid='.$log['relatedid'].'&do=profile" target="_blank">'.cplang('uid').':'.$log['relatedid'].'</a>';
	} elseif(in_array($log['operation'], $rdata['attach'])) {
		$rtype = 'attach';
		$aid = aidencode($log['relatedid']);
		$related = '<a href="forum.php?mod=attachment&aid='.$aid.'&findpost=yes" target="_blank">'.cplang('logs_attach_id').':'.$log['relatedid'].'</a>';
	} elseif(in_array($log['operation'], $rdata['post'])) {
		$rtype = 'post';
		$related = '<a href="forum.php?mod=redirect&goto=findpost&pid='.$log['relatedid'].'" target="_blank">'.cplang('logs_post_id').':'.$log['relatedid'].'</a>';
	} elseif(in_array($log['operation'], $rdata['usergroup'])) {
		$rtype = 'usergroup';
		$related = $_G['cache']['group'][$log['relatedid']]['grouptitle'];
	} elseif(in_array($log['operation'], $rdata['report'])) {
		$rtype = 'report';
		$related = cplang('logs_report_id').':'.$log['relatedid'];
	}
	$log = makecreditlog($log, $otherinfo);
	showtablerow('', ['class="bold"'], [
		"<a href=\"home.php?mod=space&uid={$log['uid']}\" target=\"_blank\">{$log['username']}",
		$log['dateline'],
		$log['operation'] ? $log['optype'] : $log['title'],
		$log['update'],
		$haveaccredit ? $log['ac'] : '',
		$log['operation'] ? $log['opinfo'] : $log['text'],
		$related.($log['relatedid'] ? '&nbsp;&nbsp;<a href="'.ADMINSCRIPT.'?action=logs&operation=credit&srch_rtype='.$rtype.'&srch_rid='.$log['relatedid'].'" target="_blank">'.cplang('sameinfo').'</a>' : ''),
	]);
}

showsubmit('', '', '', '', $multipage);
showformfooter();