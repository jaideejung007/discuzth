<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$membermf = C::t('common_member_field_forum'.$tableext)->fetch($_GET['uid']);
$member = array_merge($member, $membermf);

if(!submitcheck('medalsubmit')) {

	$medals = '';
	$membermedals = [];
	loadcache('medals');
	foreach(explode("\t", $member['medals']) as $key => $membermedal) {
		list($medalid, $medalexpiration) = explode('|', $membermedal);
		if(isset($_G['cache']['medals'][$medalid]) && (!$medalexpiration || $medalexpiration > TIMESTAMP)) {
			$membermedals[$key] = $medalid;
		} else {
			unset($membermedals[$key]);
		}
	}

	foreach(table_forum_medal::t()->fetch_all_data(1) as $medal) {
		$image = preg_match('/^https?:\/\//is', $medal['image']) ? $medal['image'] : STATICURL.'image/common/'.$medal['image'];
		$medals .= showtablerow('', ['class="td25"', 'class="td23"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"medals[{$medal['medalid']}]\" value=\"1\" ".(in_array($medal['medalid'], $membermedals) ? 'checked' : '').' />',
			"<img src=\"$image\" />",
			$medal['name']

		], TRUE);
	}

	if(!$medals) {
		cpmsg('members_edit_medals_nonexistence', '', 'error');
	}

	shownav('user', 'nav_members_confermedal');
	showchildmenu([['nav_members', 'members&operation=list'],
		[$member['username'].' ', 'members&operation=edit&uid='.$member['uid']]], cplang('nav_members_confermedal'));
	showformheader("members&operation=medal&uid={$_GET['uid']}");
	showtableheader("{$lang['members_confermedal_to']} <a href='home.php?mod=space&uid={$_GET['uid']}' target='_blank'>{$member['username']}</a>", 'fixpadding');
	showsubtitle(['medals_grant', 'medals_image', 'name']);
	echo $medals;
	showsubmit('medalsubmit');
	showtablefooter();
	showformfooter();

} else {

	$medalsdel = $medalsadd = $medalsnew = $origmedalsarray = $medalsarray = [];
	if(is_array($_GET['medals'])) {
		foreach($_GET['medals'] as $medalid => $newgranted) {
			if($newgranted) {
				$medalsarray[] = $medalid;
			}
		}
	}
	loadcache('medals');
	foreach($member['medals'] = explode("\t", $member['medals']) as $key => $modmedalid) {
		list($medalid, $medalexpiration) = explode('|', $modmedalid);
		if(isset($_G['cache']['medals'][$medalid]) && (!$medalexpiration || $medalexpiration > TIMESTAMP)) {
			$origmedalsarray[] = $medalid;
		}
	}
	foreach(array_unique(array_merge($origmedalsarray, $medalsarray)) as $medalid) {
		if($medalid) {
			$orig = in_array($medalid, $origmedalsarray);
			$new = in_array($medalid, $medalsarray);
			if($orig != $new) {
				if($orig && !$new) {
					$medalsdel[] = $medalid;
				} elseif(!$orig && $new) {
					$medalsadd[] = $medalid;
				}
			}
		}
	}
	if(!empty($medalsarray)) {
		foreach(table_forum_medal::t()->fetch_all_by_id($medalsarray) as $modmedal) {
			if(empty($modmedal['expiration'])) {
				$medalsnew[] = $modmedal['medalid'];
				$medalstatus = 0;
			} else {
				$modmedal['expiration'] = TIMESTAMP + $modmedal['expiration'] * 86400;
				$medalsnew[] = $modmedal['medalid'].'|'.$modmedal['expiration'];
				$medalstatus = 1;
			}
			if(in_array($modmedal['medalid'], $medalsadd)) {
				$data = [
					'uid' => $_GET['uid'],
					'medalid' => $modmedal['medalid'],
					'type' => 0,
					'dateline' => $_G['timestamp'],
					'expiration' => $modmedal['expiration'],
					'status' => $medalstatus,
				];
				table_forum_medallog::t()->insert($data);
				table_common_member_medal::t()->insert(['uid' => $_GET['uid'], 'medalid' => $modmedal['medalid']], 0, 1);
			}
		}
	}
	if(!empty($medalsdel)) {
		table_forum_medallog::t()->update_type_by_uid_medalid(4, $_GET['uid'], $medalsdel);
		table_common_member_medal::t()->delete_by_uid_medalid($_GET['uid'], $medalsdel);
	}
	$medalsnew = implode("\t", $medalsnew);

	C::t('common_member_field_forum'.$tableext)->update($_GET['uid'], ['medals' => $medalsnew]);

	cpmsg('members_edit_medals_succeed', "action=members&operation=medal&uid={$_GET['uid']}", 'succeed');

}
	