<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$invitecode = trim($_GET['invitecode']);
if(!$invitecode) {
	showmessage('no_invitation_code', '', [], ['handle' => false]);
}
$result = [];
if($invite = table_common_invite::t()->fetch_by_code($invitecode)) {
	if(empty($invite['fuid']) && (empty($invite['endtime']) || $_G['timestamp'] < $invite['endtime'])) {
		$result['uid'] = $invite['uid'];
		$result['id'] = $invite['id'];
	}
}
if(empty($result)) {
	showmessage('wrong_invitation_code', '', [], ['handle' => false]);
}
	