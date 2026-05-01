<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

const NOROBOT = true;

if($_G['setting']['regverify'] == 2 && $_G['groupid'] == 8 && submitcheck('verifysubmit')) {

	if(($verify_member = table_common_member_validate::t()->fetch($_G['uid'])) && $verify_member['status'] == 1) {
		table_common_member_validate::t()->update($_G['uid'], [
			'submittimes' => $verify_member['submittimes'] + 1,
			'submitdate' => $_G['timestamp'],
			'status' => '0',
			'message' => dhtmlspecialchars($_GET['regmessagenew'])
		]);
		showmessage('submit_verify_succeed', 'home.php?mod=spacecp&ac=profile');
	} else {
		showmessage('undefined_action');
	}

}

