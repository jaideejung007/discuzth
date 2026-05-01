<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($_GET['uid']) && empty($_GET['username']) && empty($_GET['ip'])) {

	/*search={"nav_repeat":"action=members&operation=repeat"}*/
	shownav('user', 'nav_members');
	showsubmenu('nav_members', [
		['search', 'members&operation=search', 0],
		['clean', 'members&operation=clean', 0],
		['nav_repeat', 'members&operation=repeat', 1],
		['add', 'members&operation=add', 0],
	]);

	showformheader('members&operation=repeat');
	showtableheader();
	showsetting('members_search_repeatuser', 'username', '', 'text');
	showsetting('members_search_uid', 'uid', '', 'text');
	showsetting('members_search_repeatip', 'ip', $_GET['inputip'], 'text');
	showsubmit('submit', 'submit');
	showtablefooter();
	showformfooter();
	/*search*/

} else {

	$ips = [];
	$urladd = '';
	if(!empty($_GET['username'])) {
		$uid = table_common_member::t()->fetch_uid_by_username($_GET['username']);
		$searchmember = $uid ? table_common_member_status::t()->fetch($uid) : [];
		$searchmember['username'] = $_GET['username'];
		$urladd .= '&username='.$_GET['username'];
	} elseif(!empty($_GET['uid'])) {
		$searchmember = table_common_member_status::t()->fetch($_GET['uid']);
		$themember = table_common_member::t()->fetch($_GET['uid']);
		$searchmember['username'] = $themember['username'];
		$urladd .= '&uid='.$_GET['uid'];
		unset($_GET['uid']);
	} elseif(!empty($_GET['ip'])) {
		$regip = $lastip = $_GET['ip'];
		$ips[] = $_GET['ip'];
		$search_condition['lastip'] = $_GET['ip'];
		$urladd .= '&ip='.$_GET['ip'];
	}

	if($searchmember) {
		$ips = [];
		foreach(['regip', 'lastip'] as $iptype) {
			if($searchmember[$iptype] != '' && $searchmember[$iptype] != 'hidden' && $searchmember[$iptype] != 'Manual Acting') {
				$ips[] = $searchmember[$iptype];
			}
		}
		$ips = !empty($ips) ? array_unique($ips) : ['unknown'];
	}
	$searchmember['username'] .= ' (IP '.implode(',', dhtmlspecialchars($ips)).')';
	$membernum = !empty($ips) && $ips[0] != 'unknown' ? table_common_member_status::t()->count_by_ip($ips) : table_common_member_status::t()->count();

	$members = '';
	if($membernum) {
		$usergroups = [];
		foreach(table_common_usergroup::t()->range() as $group) {
			switch($group['type']) {
				case 'system':
					$group['grouptitle'] = '<b>'.$group['grouptitle'].'</b>';
					break;
				case 'special':
					$group['grouptitle'] = '<i>'.$group['grouptitle'].'</i>';
					break;
			}
			$usergroups[$group['groupid']] = $group;
		}
		$_G['setting']['memberperpage'] = 100;
		$start_limit = ($page - 1) * $_G['setting']['memberperpage'];
		$multipage = multi($membernum, $_G['setting']['memberperpage'], $page, ADMINSCRIPT.'?action=members&operation=repeat&submit=yes'.$urladd);
		$allstatus = !empty($ips) && $ips[0] != 'unknown' ? table_common_member_status::t()->fetch_all_by_ip($ips, $start_limit, $_G['setting']['memberperpage'])
			: table_common_member_status::t()->range($start_limit, $_G['setting']['memberperpage']);
		$allcount = table_common_member_count::t()->fetch_all(array_keys($allstatus));
		$allmember = table_common_member::t()->fetch_all(array_keys($allstatus));
		foreach($allstatus as $uid => $member) {
			$member = array_merge($member, (array)$allcount[$uid], (array)$allmember[$uid]);
			$memberextcredits = [];
			foreach($_G['setting']['extcredits'] as $id => $credit) {
				$memberextcredits[] = $_G['setting']['extcredits'][$id]['title'].': '.$member['extcredits'.$id];
			}
			$members .= showtablerow('', ['class="td25"', '', 'title="'.implode("\n", $memberextcredits).'"'], [
				"<input type=\"checkbox\" name=\"uidarray[]\" value=\"{$member['uid']}\"".($member['adminid'] == 1 ? 'disabled' : '')." class=\"checkbox\">",
				"<a href=\"home.php?mod=space&uid={$member['uid']}\" target=\"_blank\">{$member['username']}</a>",
				$member['credits'],
				$member['posts'],
				$usergroups[$member['adminid']]['grouptitle'],
				$usergroups[$member['groupid']]['grouptitle'],
				"<a href=\"".ADMINSCRIPT."?action=members&operation=group&uid={$member['uid']}\" class=\"act\">{$lang['usergroup']}</a><a href=\"".ADMINSCRIPT."?action=members&operation=access&uid={$member['uid']}\" class=\"act\">{$lang['members_access']}</a>".
				($_G['setting']['extcredits'] ? "<a href=\"".ADMINSCRIPT."?action=members&operation=credit&uid={$member['uid']}\" class=\"act\">{$lang['credits']}</a>" : "<span disabled>{$lang['edit']}</span>").
				"<a href=\"".ADMINSCRIPT."?action=members&operation=medal&uid={$member['uid']}\" class=\"act\">{$lang['medals']}</a>".
				"<a href=\"".ADMINSCRIPT."?action=members&operation=repeat&uid={$member['uid']}\" class=\"act\">{$lang['members_repeat']}</a>".
				"<a href=\"".ADMINSCRIPT."?action=members&operation=edit&uid={$member['uid']}\" class=\"act\">{$lang['detail']}</a>"
			], TRUE);
		}
	}

	shownav('user', 'nav_repeat');
	showchildmenu([['nav_members', 'members&operation=list']], cplang('nav_repeat').' - '.$searchmember['username']);

	showformheader('members&operation=clean');
	$searchadd = '';
	if(is_array($ips) && $ips[0] != 'unknown') {
		foreach($ips as $ip) {
			$searchadd .= '<a href="'.ADMINSCRIPT.'?action=members&operation=repeat&inputip='.rawurlencode($ip).'" class="act lightlink normal">'.cplang('search').'IP '.dhtmlspecialchars($ip).'</a>';
		}
	}
	showtableheader(cplang('members_search_result', ['membernum' => $membernum]).'<a href="'.ADMINSCRIPT.'?action=members&operation=repeat" class="act lightlink normal">'.cplang('research').'</a>'.$searchadd);
	showsubtitle(['', 'username', 'credits', 'posts', 'admingroup', 'usergroup', '']);
	echo $members;
	showtablerow('', ['class="td25"', 'class="lineheight" colspan="7"'], ['', cplang('members_admin_comment')]);
	showsubmit('submit', 'submit', '<input type="checkbox" name="chkall" onclick="checkAll(\'prefix\', this.form, \'uidarray\')" class="checkbox">'.cplang('del'), '', $multipage);
	showtablefooter();
	showformfooter();

}
	