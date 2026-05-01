<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('unarchivesubmit', 1) && !submitcheck('confirmed', 1)) {

	cpmsg('members_no_find_unarchiveuser', '', 'error');

} else {

	if(submitcheck('unarchivesubmit', 1) && empty($_GET['uidarray'])) {
		cpmsg('members_no_find_unarchiveuser', '', 'error');
	}

	if(!empty($_GET['deleteall'])) {
		unset($search_condition['uidarray']);
		$_GET['uidarray'] = '';
	}

	$uids = 0;
	$extra = '';
	$unarchivememberlimit = 300;
	$unarchivestart = intval($_GET['unarchivestart']);

	if(!empty($_GET['uidarray'])) {
		$uids = [];
		$allmember = table_common_member::t()->fetch_all($_GET['uidarray']);
		$count = count($allmember);
		$membernum = 0;
		foreach($allmember as $uid => $member) {
			if($member['adminid'] !== 1 && $member['groupid'] !== 1) {
				if($count < 2000 || !empty($_GET['uidarray'])) {
					$extra .= '<input type="hidden" name="uidarray[]" value="'.$member['uid'].'" />';
				}
				$uids[] = $member['uid'];
				$membernum++;
			}
		}
	} elseif($tmpsearch_condition) {
		$membernum = countmembers($search_condition, $urladd);
		$uids = searchmembers($search_condition, $unarchivememberlimit, 0);
	}

	$allnum = intval($_GET['allnum']);
	$conditions = $uids ? 'm.uid IN ('.dimplode($uids).')' : '0';

	if((empty($membernum) || empty($uids))) {
		if($unarchivestart) {
			cpmsg('members_unarchive_succeed', '', 'succeed', ['numunarchived' => $allnum]);
		}
		cpmsg('members_no_find_unarchiveuser', '', 'error');
	}

	if(!submitcheck('confirmed')) {

		cpmsg('members_unarchive_confirm', 'action=members&operation=unarchive&submit=yes&confirmed=yes'.$urladd, 'form', ['membernum' => $membernum], $extra, '');

	} else {

		$numunarchived = $numunarchived ? $numunarchived : count($uids);

		foreach($uids as $uid) {
			table_common_member_archive::t()->move_to_master($uid);
		}

		if($_GET['uidarray']) {
			cpmsg('members_unarchive_succeed', '', 'succeed', ['numunarchived' => $numunarchived]);
		} else {
			$allnum += $membernum < $unarchivememberlimit ? $membernum : $unarchivememberlimit;
			$nextlink = "action=members&operation=unarchive&confirmed=yes&submit=yes&allnum=$allnum&unarchivestart=".($unarchivestart + $unarchivememberlimit).$urladd;
			cpmsg(cplang('members_delete_user_processing_next', ['deletestart' => $unarchivestart, 'nextdeletestart' => $unarchivestart + $unarchivememberlimit]), $nextlink, 'loadingform', []);
		}

	}
}
	