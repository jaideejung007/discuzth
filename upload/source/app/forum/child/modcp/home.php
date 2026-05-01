<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_MODCP')) {
	exit('Access Denied');
}


if($op == 'addnote' && submitcheck('submit')) {
	$newaccess = 4 + ($_GET['newaccess'][2] << 1) + $_GET['newaccess'][3];
	$newexpiration = TIMESTAMP + (intval($_GET['newexpiration']) > 0 ? intval($_GET['newexpiration']) : 30) * 86400;
	$newmessage = nl2br(dhtmlspecialchars(trim($_GET['newmessage'])));
	if($newmessage != '') {
		table_common_adminnote::t()->insert([
			'admin' => $_G['username'],
			'access' => $newaccess,
			'adminid' => $_G['adminid'],
			'dateline' => $_G['timestamp'],
			'expiration' => $newexpiration,
			'message' => $newmessage,
		]);
	}
}

if($op == 'delete' && submitcheck('notlistsubmit')) {
	if(is_array($_GET['delete']) && $deleteids = dimplode($_GET['delete'])) {
		table_common_adminnote::t()->delete_note($_GET['delete'], ($_G['adminid'] == 1 ? '' : $_G['username']));
	}
}

$access = match (intval($_G['adminid'])) {
	1 => '1,2,3,4,5,6,7',
	2 => '2,3,6,7',
	default => '1,3,5,7',
};

$notelist = [];
foreach(table_common_adminnote::t()->fetch_all_by_access(explode(',', $access)) as $note) {
	if($note['expiration'] < TIMESTAMP) {
		table_common_adminnote::t()->delete_note($note['id']);
	} else {
		$note['expiration'] = ceil(($note['expiration'] - $note['dateline']) / 86400);
		$note['dateline'] = dgmdate($note['dateline']);
		$note['checkbox'] = '<input type="checkbox" name="delete[]" class="pc" '.($note['admin'] == $_G['member']['username'] || $_G['adminid'] == 1 ? "value=\"{$note['id']}\"" : 'disabled').'>';
		$note['admin'] = '<a href="home.php?mod=space&username='.rawurlencode($note['admin']).'" target="_blank">'.$note['admin'].'</a>';
		$notelist[] = $note;
	}
}

