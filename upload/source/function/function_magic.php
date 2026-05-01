<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function checkmagicperm($perms, $id) {
	$id = $id ? intval($id) : '';
	return strexists("\t".trim($perms)."\t", "\t".trim($id)."\t") || !$perms;
}

function getmagic($magicid, $magicnum, $weight, $totalweight, $uid, $maxmagicsweight, $force = 0) {
	if($weight + $totalweight > $maxmagicsweight && !$force) {
		showmessage('magics_weight_range_invalid', '', ['less' => $weight + $totalweight - $maxmagicsweight]);
	} else {
		if(table_common_member_magic::t()->count_magic($uid, $magicid)) {
			table_common_member_magic::t()->increase($uid, $magicid, ['num' => $magicnum], false, true);
		} else {
			table_common_member_magic::t()->insert([
				'uid' => $uid,
				'magicid' => $magicid,
				'num' => $magicnum
			]);
		}
	}
}

function getmagicweight($uid, $magicarray) {
	$totalweight = 0;
	$query = table_common_member_magic::t()->fetch_all_magic($uid);
	foreach($query as $magic) {
		$totalweight += $magicarray[$magic['magicid']]['weight'] * $magic['num'];
	}

	return $totalweight;
}

function getpostinfo($id, $type, $colsarray = '') {
	global $_G;
	$type = in_array($type, ['tid', 'pid', 'blogid']) && !empty($type) ? $type : 'tid';

	switch($type) {
		case 'tid':
			$info = table_forum_thread::t()->fetch_by_tid_displayorder($id, 0);
			break;
		case 'pid':
			$info = table_forum_post::t()->fetch_post($_G['tid'], $id);
			if($info && $info['invisible'] == 0) {
				$thread = table_forum_thread::t()->fetch_thread($_G['tid']);
				$thread['thread_author'] = $thread['author'];
				$thread['thread_authorid'] = $thread['authorid'];
				$thread['thread_status'] = $thread['status'];
				$thread['thread_replycredit'] = $thread['replycredit'];
				$info = array_merge($thread, $info);
			} else {
				$info = [];
			}
			break;
		case 'blogid':
			$info = table_home_blog::t()->fetch($id);
			if(!($info && $info['status'] == '0')) {
				$info = [];
			}
			break;
	}

	if(empty($info)) {
		showmessage('magics_target_nonexistence');
	} else {
		return daddslashes($info, 1);
	}
}

function getuserinfo($username) {
	$member = table_common_member::t()->fetch_by_username($username);
	if(!$member) {
		showmessage('magics_target_member_nonexistence');
	} else {
		return daddslashes($member, 1);
	}
}

function givemagic($username, $magicid, $magicnum, $totalnum, $totalprice, $givemessage, $magicarray) {
	global $_G;

	$member = table_common_member::t()->fetch_by_username($username);
	if(!$member) {
		showmessage('magics_target_member_nonexistence');
	} elseif($member['uid'] == $_G['uid']) {
		showmessage('magics_give_myself');
	}
	$member = array_merge(table_common_usergroup_field::t()->fetch($member['groupid']), $member);
	$totalweight = getmagicweight($member['uid'], $magicarray);
	$magicweight = $magicarray[$magicid]['weight'] * $magicnum;
	if($magicarray[$magicid]['weight'] && $magicweight + $totalweight > $member['maxmagicsweight']) {
		$num = floor(($member['maxmagicsweight'] - $totalweight) / $magicarray[$magicid]['weight']);
		$num = max(0, $num);
		showmessage('magics_give_weight_range_invalid', '', ['num' => $num]);
	}

	getmagic($magicid, $magicnum, $magicweight, $totalweight, $member['uid'], $member['maxmagicsweight']);

	notification_add($member['uid'], 'magic', 'magics_receive', ['magicname' => $magicarray[$magicid]['name'], 'msg' => $givemessage]);
	updatemagiclog($magicid, '3', $magicnum, $magicarray[$magicid]['price'], $member['uid']);

	if(empty($totalprice)) {
		usemagic($magicid, $totalnum, $magicnum);
		showmessage('magics_give_succeed', 'home.php?mod=magic&action=mybox', ['toname' => $username, 'num' => $magicnum, 'magicname' => $magicarray[$magicid]['name']]);
	}
}


function magicthreadmod($tid) {
	foreach(table_forum_threadmod::t()->fetch_all_by_tid_magicid($tid) as $threadmod) {
		if(!$threadmod['magicid'] && in_array($threadmod['action'], ['CLS', 'ECL', 'STK', 'EST', 'HLT', 'EHL'])) {
			showmessage('magics_mod_forbidden');
		}
	}
}


function magicshowsetting($setname, $varname, $value, $type = 'radio', $width = '20%') {
	$check = [];

	echo '<p class="mtm mbn">'.$setname.'</p>';
	if($type == 'radio') {
		$value ? $check['true'] = 'checked="checked"' : $check['false'] = 'checked="checked"';
		echo "<input type=\"radio\" name=\"$varname\" class=\"pr\" value=\"1\" {$check['true']} /> ".lang('core', 'yes')." &nbsp; &nbsp; \n".
			"<input type=\"radio\" name=\"$varname\" class=\"pr\" value=\"0\" {$check['false']} /> ".lang('core', 'no')."\n";
	} elseif($type == 'text') {
		echo "<input type=\"text\" name=\"$varname\" class=\"px p_fre\" value=\"".dhtmlspecialchars($value)."\" size=\"12\" autocomplete=\"off\" />\n";
	} elseif($type == 'hidden') {
		echo "<input type=\"hidden\" name=\"$varname\" value=\"".dhtmlspecialchars($value)."\" />\n";
	} else {
		echo $type;
	}

}

function magicshowtips($tips) {
	echo '<p>'.$tips.'</p>';
}

function magicshowtype($type = '') {
	if($type != 'bottom') {
		echo '<p>';
	} else {
		echo '</p>';
	}
}


function usemagic($magicid, $totalnum, $num = 1) {
	global $_G;

	if($totalnum == $num) {
		table_common_member_magic::t()->delete_magic($_G['uid'], $magicid);
	} else {
		table_common_member_magic::t()->increase($_G['uid'], $magicid, ['num' => -$num]);
	}
}

function updatemagicthreadlog($tid, $magicid, $action = 'MAG', $expiration = 0, $extra = 0) {
	global $_G;
	$_G['username'] = !$extra ? $_G['username'] : '';
	$data = [
		'tid' => $tid,
		'uid' => $_G['uid'],
		'magicid' => $magicid,
		'username' => $_G['username'],
		'dateline' => $_G['timestamp'],
		'expiration' => $expiration,
		'action' => $action,
		'status' => 1
	];
	table_forum_threadmod::t()->insert($data);
}

function updatemagiclog($magicid, $action, $amount, $price, $targetuid = 0, $idtype = '', $targetid = 0) {
	global $_G;
	list($price, $credit) = explode('|', $price);
	$data = [
		'uid' => $_G['uid'],
		'magicid' => $magicid,
		'action' => $action,
		'dateline' => $_G['timestamp'],
		'amount' => $amount,
		'price' => $price,
		'credit' => $credit ?? 0,
		'idtype' => $idtype,
		'targetid' => $targetid,
		'targetuid' => $targetuid
	];
	table_common_magiclog::t()->insert($data);
}


function magic_check_idtype($id, $idtype) {
	global $_G;

	include_once libfile('function/spacecp');
	$value = '';
	$tablename = gettablebyidtype($idtype);
	if($tablename) {
		$value = C::t($tablename)->fetch_by_id_idtype($id);
		if($value['uid'] != $_G['uid']) {
			$value = null;
		}
	}
	if(empty($value)) {
		showmessage('magicuse_bad_object');
	}
	return $value;
}


function magic_peroid($magic, $uid) {
	global $_G;
	if($magic['useperoid']) {
		$dateline = 0;
		if($magic['useperoid'] == 1) {
			$dateline = TIMESTAMP - (TIMESTAMP + $_G['setting']['timeoffset'] * 3600) % 86400;
		} elseif($magic['useperoid'] == 4) {
			$dateline = TIMESTAMP - 86400;
		} elseif($magic['useperoid'] == 2) {
			$dateline = TIMESTAMP - 86400 * 7;
		} elseif($magic['useperoid'] == 3) {
			$dateline = TIMESTAMP - 86400 * 30;
		}
		$num = table_common_magiclog::t()->count_by_uid_magicid_action_dateline($uid, $magic['magicid'], 2, $dateline);
		return $magic['usenum'] - $num;
	} else {
		return true;
	}
}

