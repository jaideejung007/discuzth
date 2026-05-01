<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(!$_G['uid'] && getglobal('setting/privacy/view/profile')) {
	showmessage('home_no_privilege', '', [], ['login' => true]);
}

require_once libfile('function/spacecp');
require_once libfile('function/credit');

$inarchive = isset($space['_inarchive']) && $space['_inarchive'];
space_merge($space, 'count', $inarchive);
space_merge($space, 'field_home', $inarchive);
space_merge($space, 'field_forum', $inarchive);
space_merge($space, 'profile', $inarchive);
space_merge($space, 'status', $inarchive);
getonlinemember([$space['uid']]);

if($_G['uid'] != $space['uid'] && !$_G['group']['allowviewprofile']) {
	if(!$_G['uid']) {
		showmessage('home_no_privilege', '', [], ['login' => true]);
	} else {
		showmessage('no_privilege_profile');
	}
}

$space['admingroup'] = $_G['cache']['usergroups'][$space['adminid']];
$space['admingroup']['icon'] = g_icon($space['adminid'], 1);

$space['group'] = $_G['cache']['usergroups'][$space['groupid']];
$space['group']['icon'] = g_icon($space['groupid'], 1);
$encodeusername = rawurlencode($space['username']);

if($space['extgroupids']) {
	$newgroup = [];
	$e_ids = explode("\t", $space['extgroupids']);
	foreach($e_ids as $e_id) {
		$newgroup[] = $_G['cache']['usergroups'][$e_id]['grouptitle'].g_icon($e_id, 1);
	}
	$space['extgroupids'] = implode(',', $newgroup);
}

$space['regdate'] = dgmdate($space['regdate']);
if($space['lastvisit']) $space['lastvisit'] = dgmdate($space['lastvisit']);
if($space['lastactivity']) {
	$space['lastactivitydb'] = $space['lastactivity'];
	$space['lastactivity'] = dgmdate($space['lastactivity']);
}
if($space['lastpost']) $space['lastpost'] = dgmdate($space['lastpost']);
if($space['lastsendmail']) $space['lastsendmail'] = dgmdate($space['lastsendmail']);


if($_G['uid'] == $space['uid'] || getglobal('group/allowviewip')) {
	$space['regip_loc'] = ip::convert($space['regip']);
	$space['lastip_loc'] = ip::convert($space['lastip']);
	$space['regip'] = ip::to_display($space['regip']);
	$space['lastip'] = ip::to_display($space['lastip']);
}

$space['buyerrank'] = 0;
if($space['buyercredit']) {
	foreach($_G['setting']['ec_credit']['rank'] as $level => $credit) {
		if($space['buyercredit'] <= $credit) {
			$space['buyerrank'] = $level;
			break;
		}
	}
}

$space['sellerrank'] = 0;
if($space['sellercredit']) {
	foreach($_G['setting']['ec_credit']['rank'] as $level => $credit) {
		if($space['sellercredit'] <= $credit) {
			$space['sellerrank'] = $level;
			break;
		}
	}
}

$space['attachsize'] = formatsize($space['attachsize']);

$space['timeoffset'] = empty($space['timeoffset']) ? '9999' : $space['timeoffset'];
if(strtotime($space['regdate']) + $space['oltime'] * 3600 > TIMESTAMP) {
	$space['oltime'] = 0;
}
require_once libfile('function/friend');
$isfriend = friend_check($space['uid'], 1);
if(!$_G['adminid']) {
	if(getglobal('setting/privacy/view/profile') == 1 && !$isfriend && !$space['self']) {
		showmessage('specified_user_is_not_your_friend', '', [], []);
	}
	if(getglobal('setting/privacy/view/profile') == 2 && !$space['self']) {
		showmessage('is_blacklist', '', [], []);
	}
}
loadcache('profilesetting');
include_once libfile('function/profile');
$profiles = [];
$privacy = $space['privacy']['profile'] ? $space['privacy']['profile'] : [];

if($_G['setting']['verify']['enabled']) {
	space_merge($space, 'verify');
}
foreach($_G['cache']['profilesetting'] as $fieldid => $field) {
	
	if($_G['setting']['nsprofiles']) {
		break;
	}
	if(!$field['available'] || in_array($fieldid, ['birthcountry', 'birthprovince', 'birthdist', 'birthcommunity', 'residecountry', 'resideprovince', 'residedist', 'residecommunity'])) {
		continue;
	}
	if(
		$field['available'] && (strlen($space[$fieldid]) > 0 || ($fieldid == 'birthcity' && strlen($space['birthcountry']) && strlen($space['birthprovince']) || $fieldid == 'residecity' && strlen($space['residecountry']) && strlen($space['resideprovince']))) &&
		($space['self'] || empty($privacy[$fieldid]) || ($isfriend && $privacy[$fieldid] == 1)) &&
		(!$_G['inajax'] && !$field['invisible'] || $_G['inajax'] && $field['showincard'])
	) {
		$val = profile_show($fieldid, $space);
		if($val !== false) {
			if($fieldid == 'realname' && $_G['uid'] != $space['uid'] && !ckrealname(1)) {
				continue;
			}
			if($field['formtype'] == 'file' && $val) {
				$imgurl = getglobal('setting/attachurl').'./profile/'.$val;
				$val = '<span><a href="'.$imgurl.'" target="_blank"><img src="'.$imgurl.'"  style="max-width: 500px;" /></a></span>';
			}
			if($val == '') $val = '-';
			$profiles[$fieldid] = ['title' => $field['title'], 'value' => $val];
		}
	}
}

$count = table_forum_moderator::t()->count_by_uid($space['uid']);
if($count) {
	foreach(table_forum_moderator::t()->fetch_all_by_uid($space['uid']) as $result) {
		$moderatefids[] = $result['fid'];
	}
	$query = table_forum_forum::t()->fetch_all_info_by_fids($moderatefids);
	foreach($query as $result) {
		$manage_forum[$result['fid']] = $result['name'];
	}
}

if(!$_G['inajax'] && $_G['setting']['groupstatus']) {
	$groupcount = table_forum_groupuser::t()->fetch_all_group_for_user($space['uid'], 1);
	if($groupcount > 0) {
		$fids = table_forum_groupuser::t()->fetch_all_fid_by_uids($space['uid']);
		$usergrouplist = table_forum_forum::t()->fetch_all_info_by_fids($fids);
	}
}

if($space['medals']) {
	loadcache('medals');
	foreach($space['medals'] = explode("\t", $space['medals']) as $key => $medalid) {
		list($medalid, $medalexpiration) = explode('|', $medalid);
		if(isset($_G['cache']['medals'][$medalid]) && (!$medalexpiration || $medalexpiration > TIMESTAMP)) {
			$space['medals'][$key] = $_G['cache']['medals'][$medalid];
			$space['medals'][$key]['medalid'] = $medalid;
		} else {
			unset($space['medals'][$key]);
		}
	}
}
$upgradecredit = $space['uid'] && $space['group']['type'] == 'member' && $space['group']['creditslower'] != 9999999 ? $space['group']['creditslower'] - $space['credits'] : false;
$allowupdatedoing = $space['uid'] == $_G['uid'] && checkperm('allowdoing');

dsetcookie('home_diymode', 1);

$navtitle = lang('space', 'sb_profile', ['who' => $space['username']]);
$metakeywords = lang('space', 'sb_profile', ['who' => $space['username']]);
$metadescription = lang('space', 'sb_profile', ['who' => $space['username']]);

$clist = [];
if(in_array($_G['adminid'], [1, 2, 3])) {
	include_once libfile('function/member');
	$clist = crime('getactionlist', $space['uid']);
}

show_view();

if(!getglobal('privacy')) {
	if(!$_G['inajax']) {
		include_once template('home/space_profile');
	} else {
		$_GET['do'] = 'card';
		if(helper_access::check_module('follower')) {
			$follow = table_home_follow::t()->fetch_by_uid_followuid($_G['uid'], $space['uid']);
		}
		include_once template('home/space_card');
	}
}
