<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$creditid = 0;
$creditnum = $_G['group']['inviteprice'];
if($_G['setting']['creditstrans']) {
	$creditid = intval($_G['setting']['creditstransextra'][6] ? $_G['setting']['creditstransextra'][6] : $_G['setting']['creditstrans']);
} elseif($creditnum) {
	showmessage('trade_credit_invalid', '', [], ['return' => 1]);
}

space_merge($space, 'count');

$baseurl = 'home.php?mod=spacecp&ac=invite';

$siteurl = getsiteurl();

$maxcount = 50;

$config = $_G['setting']['inviteconfig'];
$creditname = $config['inviterewardcredit'];
$allowinvite = ($_G['setting']['regstatus'] > 1 && $creditname && $_G['group']['allowinvite']) ? 1 : 0;
$unit = $_G['setting']['extcredits'][$creditname]['unit'];
$credittitle = $_G['setting']['extcredits'][$creditname]['title'];
$creditname = 'extcredits'.$creditname;

$inviteurl = $invite_code = '';

$creditkey = 'extcredits'.$creditid;
$extcredits = $_G['setting']['extcredits'][$creditid];

$mailvar = [
	'avatar' => avatar($space['uid'], 'middle'),
	'uid' => $space['uid'],
	'username' => $space['username'],
	'sitename' => $_G['setting']['sitename'],
	'siteurl' => $siteurl
];

if(!$creditnum) {
	$inviteurl = getinviteurl(0, 0);
}
if(!$allowinvite) {
	showmessage('close_invite', '', [], $_G['inajax'] ? ['showdialog' => 1, 'showmsg' => true, 'closetime' => true] : []);
}

if(submitcheck('emailinvite')) {

	if(!$_G['group']['allowmailinvite']) {
		showmessage('mail_invite_not_allow', $baseurl);
	}

	$_POST['email'] = str_replace("\n", ',', $_POST['email']);
	$newmails = [];
	$mails = explode(',', $_POST['email']);
	foreach($mails as $value) {
		$value = trim($value);
		if(isemail($value)) {
			$newmails[] = $value;
		}
	}
	$newmails = array_unique($newmails);
	$invitenum = count($newmails);

	if($invitenum < 1) {
		showmessage('mail_can_not_be_empty', $baseurl);
	}

	$msetarr = [];
	if($creditnum) {
		$allcredit = $invitenum * $creditnum;
		if($space[$creditkey] < $allcredit) {
			showmessage('mail_credit_inadequate', $baseurl);
		}

		foreach($newmails as $value) {
			$code = strtolower(random(6));
			$setarr = [
				'uid' => $_G['uid'],
				'code' => $code,
				'email' => daddslashes($value),
				'type' => 1,
				'inviteip' => $_G['clientip'],
				'dateline' => $_G['timestamp'],
				'status' => 3,
				'endtime' => ($_G['group']['maxinviteday'] ? ($_G['timestamp'] + $_G['group']['maxinviteday'] * 24 * 3600) : 0)
			];
			$id = table_common_invite::t()->insert($setarr, true);

			$mailvar['inviteurl'] = getinviteurl($id, $code);

			createmail($value, $mailvar);
		}

		updatemembercount($_G['uid'], [$creditkey => "-$allcredit"]);

	} else {

		$mailvar['inviteurl'] = $inviteurl;
		foreach($newmails as $value) {
			createmail($value, $mailvar);
		}
	}

	showmessage('send_result_succeed', $baseurl);

} else if(submitcheck('invitesubmit')) {

	$invitenum = intval($_POST['invitenum']);
	if($invitenum < 1) $invitenum = 1;

	if($_G['group']['maxinvitenum']) {
		$daytime = $_G['timestamp'] - 24 * 3600;
		$invitecount = table_common_invite::t()->count_by_uid_dateline($_G['uid'], $daytime);
		if($invitecount + $invitenum > $_G['group']['maxinvitenum']) {
			showmessage('max_invitenum_error', NULL, ['maxnum' => $_G['group']['maxinvitenum']], ['showdialog' => 1, 'showmsg' => true, 'closetime' => true]);
		}
	}

	$allcredit = $invitenum * $creditnum;
	if($space[$creditkey] < $allcredit) {
		showmessage('mail_credit_inadequate', $baseurl, [], ['showdialog' => 1, 'showmsg' => true, 'closetime' => true]);
	}

	$havecode = false;
	$dateline = $_G['timestamp'];
	for($i = 0; $i < $invitenum; $i++) {
		$code = strtolower(random(6));
		$havecode = true;
		$invitedata = [
			'uid' => $_G['uid'],
			'code' => $code,
			'dateline' => $dateline,
			'endtime' => $_G['group']['maxinviteday'] ? ($_G['timestamp'] + $_G['group']['maxinviteday'] * 24 * 3600) : 0,
			'inviteip' => $_G['clientip']
		];
		table_common_invite::t()->insert($invitedata);
	}

	if($havecode) {
		require_once libfile('class/credit');
		require_once libfile('function/credit');
		$creditobj = new credit();
		$creditobj->updatemembercount([$creditkey => 0 - $allcredit], $_G['uid']);
		credit_log($_G['uid'], 'INV', $_G['uid'], [$creditkey => 0 - $allcredit]);
	}
	showmessage('do_success', $baseurl, ['deduction' => $allcredit, 'dateline' => $dateline], ['showdialog' => 1, 'showmsg' => true, 'closetime' => true, 'return' => false]);
}

if($_GET['op'] == 'resend') {

	$id = $_GET['id'] ? intval($_GET['id']) : 0;

	if(submitcheck('resendsubmit')) {

		if(empty($id)) {
			showmessage('send_result_resend_error', $baseurl);
		}

		if($value = table_common_invite::t()->fetch_by_id_uid($id, $_G['uid'])) {
			if($creditnum) {
				$inviteurl = getinviteurl($value['id'], $value['code']);
			}
			$mailvar['inviteurl'] = $inviteurl;

			createmail($value['email'], $mailvar);
			showmessage('send_result_succeed', dreferer(), ['id' => $id], ['showdialog' => 1, 'showmsg' => true, 'closetime' => true]);

		} else {
			showmessage('send_result_resend_error', $baseurl, [], ['showdialog' => 1, 'showmsg' => true, 'closetime' => true]);
		}
	}

} elseif($_GET['op'] == 'delete') {

	$id = $_GET['id'] ? intval($_GET['id']) : 0;
	if(empty($id)) {
		showmessage('there_is_no_record_of_invitation_specified', $baseurl);
	}
	if($value = table_common_invite::t()->fetch_by_id_uid($id, $_G['uid'])) {
		if(submitcheck('deletesubmit')) {
			table_common_invite::t()->delete($id);
			showmessage('do_success', dreferer(), ['id' => $id], ['showdialog' => 1, 'showmsg' => true, 'closetime' => true]);
		}
	} else {
		showmessage('there_is_no_record_of_invitation_specified', $baseurl, [], ['showdialog' => 1, 'showmsg' => true, 'closetime' => true]);
	}

} elseif($_GET['op'] == 'showinvite') {
	foreach(table_common_invite::t()->fetch_all_by_uid($_G['uid']) as $value) {
		if(!$value['fuid'] && !$value['type']) {
			$inviteurl = getinviteurl($value['id'], $value['code']);
			$list[$value['code']] = $inviteurl;
		}
	}
} else {

	$list = $flist = $dels = [];
	$invitedcount = $count = 0;

	foreach(table_common_invite::t()->fetch_all_by_uid($_G['uid']) as $value) {
		if($value['fuid']) {
			$flist[] = $value;
			$invitedcount++;
		} else {

			if($_G['timestamp'] > $value['endtime']) {
				$dels[] = $value['id'];
				continue;
			}

			$inviteurl = getinviteurl($value['id'], $value['code']);

			if($value['type']) {
				$maillist[] = [
					'email' => $value['email'],
					'url' => $inviteurl,
					'id' => $value['id']
				];
			} else {
				$list[$value['code']] = $inviteurl;
				$count++;
			}
		}
	}

	if($dels) {
		table_common_invite::t()->delete($dels);
	}

	$uri = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : ($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
	$uri = substr($uri, 0, strrpos($uri, '/') + 1);

	$actives = ['invite' => ' class="a"'];
}

$navtitle = lang('core', 'title_invite_friend');

include template('home/spacecp_invite');

function createmail($mail, $mailvar) {
	global $_G;

	$mailvar['saymsg'] = empty($_POST['saymsg']) ? '' : getstr($_POST['saymsg'], 500);

	require_once libfile('function/mail');
	$tplarray = [
		'tpl' => 'invitemail',
		'var' => $mailvar,
		'svar' => $mailvar
	];

	if(!sendmail($mail, $tplarray)) {
		runlog('sendmail', "$mail sendmail failed.");
	}
}

function getinviteurl($inviteid, $invitecode) {
	global $_G;

	if($inviteid && $invitecode) {
		$inviteurl = getsiteurl()."home.php?mod=invite&amp;id={$inviteid}&amp;c={$invitecode}";
	} else {
		$invite_code = helper_invite::generate_key($_G['uid']);
		$inviteurl = getsiteurl()."home.php?mod=invite&amp;u={$_G['uid']}&amp;c=$invite_code";
	}
	return $inviteurl;
}

