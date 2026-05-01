<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($tableext) {
	cpmsg('members_edit_credits_failure', '', 'error');
}
$membercount = C::t('common_member_count'.$tableext)->fetch($member['uid']);
$membergroup = table_common_usergroup::t()->fetch($member['groupid']);
$member = array_merge($member, $membercount, $membergroup);

if(!submitcheck('creditsubmit')) {

	eval("\$membercredit = @round({$_G['setting']['creditsformula']});");

	if(($jscreditsformula = table_common_setting::t()->fetch_setting('creditsformula'))) {
		$jscreditsformula = str_replace(['digestposts', 'posts', 'threads'], [$member['digestposts'], $member['posts'], $member['threads']], $jscreditsformula);
	}

	$creditscols = ['members_credit_ranges', 'credits'];
	$creditsvalue = [$member['type'] == 'member' ? "{$member['creditshigher']}~{$member['creditslower']}" : 'N/A', '<input type="text" class="txt" name="jscredits" id="jscredits" value="'.$membercredit.'" size="6" disabled style="padding:0;width:6em;border:none; background-color:transparent">'];
	for($i = 1; $i <= 8; $i++) {
		$jscreditsformula = str_replace('extcredits'.$i, "extcredits[$i]", $jscreditsformula);
		$creditscols[] = isset($_G['setting']['extcredits'][$i]) ? $_G['setting']['extcredits'][$i]['title'] : 'extcredits'.$i;
		$creditsvalue[] = isset($_G['setting']['extcredits'][$i]) ? '<input type="text" class="txt" size="3" name="extcreditsnew['.$i.']" id="extcreditsnew['.$i.']" value="'.$member['extcredits'.$i].'" onkeyup="membercredits()"> '.$_G['setting']['extcredits'][$i]['unit'] : '<input type="text" class="txt" size="3" value="N/A" disabled>';
	}

	echo <<<EOT
<script language="JavaScript">
	var extcredits = new Array();
	function membercredits() {
		var credits = 0;
		for(var i = 1; i <= 8; i++) {
			e = $('extcreditsnew['+i+']');
			if(e && parseInt(e.value)) {
				extcredits[i] = parseInt(e.value);
			} else {
				extcredits[i] = 0;
			}
		}
		$('jscredits').value = Math.round($jscreditsformula);
	}
</script>
EOT;
	shownav('user', 'members_credit');
	showchildmenu([['nav_members', 'members&operation=list'],
		[$member['username'].' ', 'members&operation=edit&uid='.$member['uid']]], cplang('members_credit'));

	/*search={"members_credit":"action=members&operation=credit"}*/
	showtips('members_credit_tips');
	showformheader("members&operation=credit&uid={$_GET['uid']}");
	showboxheader('<em class="right"><a href="'.ADMINSCRIPT.'?action=logs&operation=credit&srch_uid='.$_GET['uid'].'&frame=yes" target="_blank">'.cplang('members_credit_logs').'</a></em>'.cplang('usergroup').': '.$member['grouptitle'], 'nobottom');
	showtableheader();
	showsubtitle($creditscols);
	showtablerow('', ['', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"'], $creditsvalue);
	showtablefooter();
	showboxfooter();
	showtableheader('', 'notop');
	showtitle('members_edit_reason');
	showsetting('members_credit_reason', 'reason', '', 'textarea');
	showsetting('members_credit_reason_notify', 'reasonnotify', '', 'radio');
	showsubmit('creditsubmit');
	showtablefooter();
	showformfooter();
	/*search*/

} else {

	$diffarray = [];
	$sql = $comma = $notify = '';
	if(is_array($_GET['extcreditsnew'])) {
		foreach($_GET['extcreditsnew'] as $id => $value) {
			if($member['extcredits'.$id] != ($value = intval($value))) {
				$diffarray[$id] = $value - $member['extcredits'.$id];
				$sql .= $comma."extcredits$id='$value'";
				$comma = ', ';
				$notify .= (empty($notify) ? '' : ', ').$_G['setting']['extcredits'][$id]['title'].' => '.$value;
			}
		}
	}

	if($diffarray) {
		if($_G['setting']['log']['rate']) {
			foreach($diffarray as $id => $diff) {
				$errorlog = [
					'timestamp' => TIMESTAMP,
					'operator_username' => $_G['member']['username'],
					'operator_adminid' => $_G['adminid'],
					'member_username' => $member['username'],
					'extcredits' => $id,
					'diff' => $diff,
					'tid' => 0,
					'subject' => '',
					'reason' => $_GET['reason'],
					'd' => '',
				];
				$member_log = $member;
				logger('rate', $member_log, $_G['member']['uid'], $errorlog);

			}
		}
		updatemembercount($_GET['uid'], $diffarray);
		if(isset($_GET['reasonnotify']) && $_GET['reasonnotify']) {
			$notearr = [
				'user' => "<a href=\"home.php?mod=space&uid={$_G['uid']}\">{$_G['username']}</a>",
				'day' => isset($_GET['expirydatenew']) ? addslashes($_GET['expirydatenew']) : 0,
				'extcredits' => $notify,
				'reason' => addslashes($_GET['reason']),
				'from_id' => 0,
				'from_idtype' => 'changecredits'
			];
			notification_add($member['uid'], 'system', 'member_change_credits', $notearr, 1);
		}
	}

	cpmsg('members_edit_credits_succeed', "action=members&operation=credit&uid={$_GET['uid']}", 'succeed');

}
	