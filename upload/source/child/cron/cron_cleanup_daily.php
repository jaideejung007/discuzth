<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once libfile('function/cache');
updatecache('forumrecommend');

if(table_common_advertisement::t()->close_endtime()) {
	updatecache(['setting', 'advs']);
}
table_forum_threaddisablepos::t()->truncate();
table_common_searchindex::t()->truncate();
table_forum_threadmod::t()->delete_by_dateline($_G['timestamp'] - 31536000);
table_forum_forumrecommend::t()->delete_old();
table_home_visitor::t()->delete_by_dateline($_G['timestamp'] - 7776000);
table_forum_postcache::t()->delete_by_dateline(TIMESTAMP - 86400);
table_forum_newthread::t()->delete_by_dateline(TIMESTAMP - 1296000);
table_common_seccheck::t()->truncate();
table_restful_stat::t()->clearstat(dgmdate(TIMESTAMP - 2592000, 'Ymd'));

if($_G['setting']['heatthread']['type'] == 2 && $_G['setting']['heatthread']['period']) {
	$partakeperoid = 86400 * $_G['setting']['heatthread']['period'];
	table_forum_threadpartake::t()->delete_threadpartake($_G['timestamp'] - $partakeperoid);
}

table_common_member_count::t()->clear_today_data();

table_forum_trade::t()->update_closed($_G['timestamp']);
table_forum_tradelog::t()->clear_failure(7);
table_forum_tradelog::t()->expiration_payed(7);
table_forum_tradelog::t()->expiration_finished(7);

if($_G['setting']['cachethreadon']) {
	removedir($_G['setting']['cachethreaddir'], TRUE);
	touch($_G['setting']['cachethreaddir'].'/index.htm');
}
removedir($_G['setting']['attachdir'].'image', TRUE);
@touch($_G['setting']['attachdir'].'image/index.htm');

table_forum_attachment_unused::t()->clear();

table_forum_polloption_image::t()->clear();

$uids = $members = [];
$members = table_common_member::t()->fetch_all_ban_by_groupexpiry(TIMESTAMP);
if(($uids = array_keys($members))) {
	$setarr = [];
	foreach(table_common_member_field_forum::t()->fetch_all($uids) as $uid => $member) {
		$member['groupterms'] = dunserialize($member['groupterms']);
		$member['groupid'] = $members[$uid]['groupid'];
		$member['credits'] = $members[$uid]['credits'];

		if(!empty($member['groupterms']['main']['groupid'])) {
			$groupidnew = $member['groupterms']['main']['groupid'];
			$adminidnew = $member['groupterms']['main']['adminid'];
			unset($member['groupterms']['main']);
			unset($member['groupterms']['ext'][$member['groupid']]);
			$setarr['groupexpiry'] = groupexpiry($member['groupterms']);
		} else {
			$query = table_common_usergroup::t()->fetch_by_credits($member['credits'], 'member');
			$groupidnew = $query['groupid'];
			$adminidnew = 0;
		}
		$setarr['adminid'] = $adminidnew;
		$setarr['groupid'] = $groupidnew;
		table_common_member::t()->update($uid, $setarr);
		table_common_member_field_forum::t()->update($uid, ['groupterms' => ($member['groupterms'] ? serialize($member['groupterms']) : '')]);
	}
}

if(!empty($_G['setting']['advexpiration']['allow'])) {
	$endtimenotice = mktime(0, 0, 0, date('m', TIMESTAMP), date('d', TIMESTAMP), date('Y', TIMESTAMP)) + $_G['setting']['advexpiration']['day'] * 86400;
	$advs = [];
	foreach(table_common_advertisement::t()->fetch_all_endtime($endtimenotice) as $adv) {
		$advs[] = '<a href="admin.php?action=adv&operation=edit&advid='.$adv['advid'].'" target="_blank">'.$adv['title'].'</a>';
	}
	if($advs) {
		$users = explode("\n", $_G['setting']['advexpiration']['users']);
		$users = array_map('trim', $users);
		if($users) {
			foreach(table_common_member::t()->fetch_all_by_username($users) as $member) {
				$noticelang = ['day' => $_G['setting']['advexpiration']['day'], 'advs' => implode('<br />', $advs), 'from_id' => 0, 'from_idtype' => 'advexpire'];
				if(in_array('notice', $_G['setting']['advexpiration']['method'])) {
					notification_add($member['uid'], 'system', 'system_adv_expiration', $noticelang, 1);
				}
				if(in_array('mail', $_G['setting']['advexpiration']['method'])) {
					$advexpvar = [
						'tpl' => 'adv_expiration',
						'var' => $noticelang,
						'svar' => $noticelang,
					];
					if(!sendmail("{$member['username']} <{$member['email']}>", $advexpvar)) {
						runlog('sendmail', "{$member['email']} sendmail failed.");
					}
				}
			}
		}
	}
}


$count = table_common_card::t()->count_by_where("status = '1' AND cleardateline <= '{$_G['timestamp']}'");
if($count) {
	table_common_card::t()->update_to_overdue($_G['timestamp']);
	$card_info = serialize(['num' => $count]);
	$cardlog = [
		'info' => $card_info,
		'dateline' => $_G['timestamp'],
		'operation' => 9
	];
	table_common_card_log::t()->insert($cardlog);
}

table_common_member_action_log::t()->delete_by_dateline($_G['timestamp'] - 86400);

table_forum_collectioninvite::t()->delete_by_dateline($_G['timestamp'] - 86400 * 7);

loadcache('seccodedata', true);
$_G['cache']['seccodedata']['register']['show'] = 0;
savecache('seccodedata', $_G['cache']['seccodedata']);

function removedir($dirname, $keepdir = FALSE) {
	$dirname = str_replace(["\n", "\r", '..'], ['', '', ''], $dirname);

	if(!is_dir($dirname)) {
		return FALSE;
	}
	$handle = opendir($dirname);
	while(($file = readdir($handle)) !== FALSE) {
		if($file != '.' && $file != '..') {
			$dir = $dirname.DIRECTORY_SEPARATOR.$file;
			is_dir($dir) ? removedir($dir) : unlink($dir);
		}
	}
	closedir($handle);
	return $keepdir || ((@rmdir($dirname) ? true : false));
}

