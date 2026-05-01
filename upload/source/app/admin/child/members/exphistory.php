<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// 用户历史资料下载
if(!isset($_GET['uid'])) {
	cpmsg('members_no_find_user', '', 'error');
}
$uid = intval($_GET['uid']);
$detail = '';
if($uid) {
	$profiles = table_common_member_profile_history::t()->fetch_all_by_uid($uid);
	$user = table_common_member::t()->fetch($uid);
	foreach($profiles as $hid => $profile) {
		unset($profile['hid']);
		$profile = array_merge(['uid' => $uid, 'username' => $user['username']], $profile);
		foreach($profile as $key => $value) {
			$value = preg_replace('/\s+/', ' ', $value);
			if($key == 'gender') $value = lang('space', 'gender_'.$value);
			$detail .= strlen($value) > 11 && is_numeric($value) ? '['.$value.'],' : $value.',';
		}
		$detail = $detail."\n";
	}
}
$title = ['realname' => '', 'gender' => '', 'birthyear' => '', 'birthmonth' => '', 'birthday' => '', 'constellation' => '',
	'zodiac' => '', 'telephone' => '', 'mobile' => '', 'idcardtype' => '', 'idcard' => '', 'address' => '', 'zipcode' => '', 'nationality' => '',
	'birthcountry' => '', 'birthprovince' => '', 'birthcity' => '', 'birthdist' => '', 'birthcommunity' => '',
	'residecountry' => '', 'resideprovince' => '', 'residecity' => '', 'residedist' => '',
	'residecommunity' => '', 'residesuite' => '', 'graduateschool' => '', 'education' => '', 'company' => '', 'occupation' => '',
	'position' => '', 'revenue' => '', 'affectivestatus' => '', 'lookingfor' => '', 'bloodtype' => '', 'height' => '', 'weight' => '',
	'alipay' => '', 'icq' => '', 'qq' => '', 'yahoo' => '', 'msn' => '', 'taobao' => '', 'site' => '', 'bio' => '', 'interest' => '',
	'field1' => '', 'field2' => '', 'field3' => '', 'field4' => '', 'field5' => '', 'field6' => '', 'field7' => '', 'field8' => '', 'fields' => ''];
foreach(table_common_member_profile_setting::t()->range_setting() as $value) {
	if(isset($title[$value['fieldid']])) {
		$title[$value['fieldid']] = $value['title'];
	}
}
foreach($title as $k => $v) {
	$subject .= ($v ? $v : $k).',';
}
$detail = 'UID,'.$lang['username'].','.$subject."\n".$detail;
$filename = date('Ymd', TIMESTAMP).'_history.csv';

ob_end_clean();
header('Content-Encoding: none');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');
header('Expires: 0');
echo $detail;
const FOOTERDISABLED = 1;
exit();
	