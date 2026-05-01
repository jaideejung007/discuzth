<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('invitesubmit')) {

	$where = '1';
	$pageadd = '';
	$uid = $fuid = 0;
	if($srch_uid = trim($_GET['srch_uid'])) {
		if($uid = max(0, intval($srch_uid))) {
			$where .= " AND i.`uid`='$uid'";
			$pageadd .= '&srch_uid='.$uid;
		} else {
			$srch_uid = '';
		}
	} elseif($srch_username = trim($_GET['srch_username'])) {
		$uid = ($uid = table_common_member::t()->fetch_uid_by_username($srch_username)) ? $uid : table_common_member_archive::t()->fetch_uid_by_username($srch_username);
		if($uid) {
			$where .= " AND i.`uid`='$uid'";
			$pageadd .= '&srch_username='.rawurlencode($srch_username);
		} else {
			$srch_username = '';
		}
	}
	if($srch_fuid = trim($_GET['srch_fuid'])) {
		if($fuid = max(0, intval($srch_fuid))) {
			$where .= " AND i.`fuid`='$fuid'";
			$pageadd .= '&srch_fuid='.$fuid;
		} else {
			$srch_fuid = '';
		}
	}
	if($srch_fusername = trim($_GET['srch_fusername'])) {
		$where .= " AND i.`fusername`='$srch_fusername'";
		$pageadd .= '&srch_fusername='.rawurlencode($srch_fusername);
	}
	if($srch_buydate_start = trim($_GET['srch_buydate_start'])) {
		if($buydate_start = strtotime($srch_buydate_start)) {
			$where .= " AND i.`dateline`>'$buydate_start'";
			$pageadd .= '&srch_buydate_start='.$srch_buydate_start;
		} else {
			$srch_buydate_start = '';
		}
	}
	if($srch_buydate_end = trim($_GET['srch_buydate_end'])) {
		if($buydate_end = strtotime($srch_buydate_end)) {
			$where .= " AND i.`dateline`<'$buydate_end'";
			$pageadd .= '&srch_buydate_end='.$srch_buydate_end;
		} else {
			$srch_buydate_end = '';
		}
	}
	if($srch_ip = trim($_GET['srch_ip'])) {
		$pageadd .= '&srch_ip='.rawurlencode($srch_ip);
		$inviteip = str_replace('*', '%', addcslashes($srch_ip, '%_'));
		$srch_ip = dhtmlspecialchars($srch_ip);
		$where .= " AND i.`inviteip` LIKE '$inviteip%'";
	}
	if($srch_code = trim($_GET['srch_code'])) {
		$pageadd .= '&srch_ip='.rawurlencode($srch_code);
		$where .= " AND i.`code`='$srch_code'";
		$srch_code = dhtmlspecialchars($srch_code);
	}

	showformheader("logs&operation=$operation");

	showtableheader('search', 'fixpadding');
	showtablerow('', ['class="td23"', 'width="150"', 'class="td23"'],
		[
			cplang('username'), '<input type="text" name="srch_username" class="txt" value="'.$srch_username.'" />',
			cplang('logs_invite_ip'), '<input type="text" name="srch_ip" class="txt" value="'.$srch_ip.'" size="5" />',
		]
	);
	showtablerow('', ['class="td23"', 'width="150"', 'class="td23"'],
		[
			cplang('uid'), '<input type="text" name="srch_uid" class="txt" value="'.$srch_uid.'" />',
			cplang('logs_invite_code'), '<input type="text" name="srch_code" class="txt" value="'.$srch_code.'" size="5" />',
		]
	);
	showtablerow('', ['class="td23"', 'width="150"', 'class="td23"'],
		[
			cplang('logs_invite_target'), '<input type="text" name="srch_fusername" class="txt" value="'.$srch_fusername.'" />',
			cplang('logs_invite_buydate'), '<input type="text" name="srch_buydate_start" class="txt" value="'.$srch_buydate_start.'" onclick="showcalendar(event, this)" />- <input type="text" name="srch_buydate_end" class="txt" value="'.$srch_buydate_end.'" onclick="showcalendar(event, this)" />',
		]
	);
	showtablerow('', ['class="td23"', 'width="150"', 'class="td23"'],
		[
			cplang('logs_invite_target').cplang('uid'), '<input type="text" name="srch_fuid" class="txt" value="'.$srch_fuid.'" />',
			'', '',
		]
	);
	showtablerow('', ['colspan="4"'], ['<input type="submit" name="srchlogbtn" class="btn" value="'.$lang['search'].'" />']);
	showtablefooter();
	showformfooter();
	
	echo '<script src="'.STATICURL.'js/calendar.js" type="text/javascript"></script>';
	showtableheader('', 'fixpadding');
	showtablerow('class="header"', ['width="35"', 'class="td23"', 'class="td24"', 'class="td24"', 'class="td23"', 'class="td24"', 'class="td24"'], [
		'',
		cplang('logs_invite_buyer'),
		cplang('logs_invite_buydate'),
		cplang('logs_invite_expiration'),
		cplang('logs_invite_ip'),
		cplang('logs_invite_code'),
		cplang('logs_invite_status'),
	]);

	$tpp = $_GET['lpp'] ? intval($_GET['lpp']) : $_G['tpp'];
	$start_limit = ($page - 1) * $tpp;

	$dels = [];
	$invitecount = table_common_invite::t()->count_by_search($uid, $fuid, $srch_fusername, $buydate_start, $buydate_end, $inviteip, $srch_code);
	if($invitecount) {
		$multipage = multi($invitecount, $tpp, $page, ADMINSCRIPT."?action=logs&operation=invite&lpp=$lpp$pageadd", 0, 3);

		$invitearr = table_common_invite::t()->fetch_all_by_search($uid, $fuid, $srch_fusername, $buydate_start, $buydate_end, $inviteip, $srch_code, $start_limit, $tpp);
		$members = table_common_member::t()->fetch_all(table_common_invite::t()->get_uids());
		foreach($invitearr as $invite) {
			$invite['username'] = $members[$invite['uid']]['username'];
			if(!$invite['fuid'] && $_G['timestamp'] > $invite['endtime']) {
				$dels[] = $invite['id'];
				continue;
			}

			$invite['statuslog'] = $lang['logs_invite_status_'.$invite['status']];
			$username = "<a href=\"home.php?mod=space&uid={$invite['uid']}\">{$invite['username']}</a>";
			$invite['dateline'] = dgmdate($invite['dateline'], 'Y-n-j H:i');
			$invite['expiration'] = dgmdate($invite['endtime'], 'Y-n-j H:i');
			$stats = $invite['statuslog'].($invite['status'] == 2 ? '&nbsp;[<a href="home.php?mod=space&uid='.$invite['fuid'].'" target="_blank">'.$lang['logs_invite_target'].':'.$invite['fusername'].'</a>]' : '');

			showtablerow('', ['', 'class="bold"'], [
				'<input type="checkbox" class="checkbox" name="delete[]" value="'.$invite['id'].'" />',
				$username,
				$invite['dateline'],
				$invite['expiration'],
				$invite['inviteip'],
				$invite['code'],
				$stats
			]);
		}
		showhiddenfields(['pageadd' => $pageadd]);

		if($dels) {
			table_common_invite::t()->delete($dels);
		}
	}

} else {

	if($_GET['delete']) {
		table_common_invite::t()->delete($_GET['delete']);
	}

	header("Location: {$_G['siteurl']}".ADMINSCRIPT."?action=logs&operation=invite&lpp={$_GET['lpp']}{$_GET['pageadd']}");
}
	