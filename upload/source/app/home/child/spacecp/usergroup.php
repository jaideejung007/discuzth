<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_G['inajax'] && $_GET['showextgroups']) {
	require_once libfile('function/forumlist');
	loadcache('usergroups');
	$extgroupids = $_G['member']['extgroupids'] ? explode("\t", $_G['member']['extgroupids']) : [];
	$extgroupids = fixedupgroup($extgroupids);
	$group = [];
	if($_G['uid'] && $_G['group']['grouptype'] == 'member' && $_G['group']['groupcreditslower'] != 999999999) {
		$group['upgradecredit'] = $_G['group']['groupcreditslower'] - $_G['member']['credits'];
		$group['upgradeprogress'] = 100 - ceil($group['upgradecredit'] / ($_G['group']['groupcreditslower'] - $_G['group']['groupcreditshigher']) * 100);
		$group['upgradeprogress'] = max($group['upgradeprogress'], 2);
	}
	include template('forum/viewthread_profile_node');
	include template('common/extgroups');
	exit;
}

$do = in_array(getgpc('do'), ['buy', 'exit', 'switch', 'list', 'forum', 'expiry']) ? trim($_GET['do']) : 'usergroup';

$extgroupids = $_G['member']['extgroupids'] ? explode("\t", $_G['member']['extgroupids']) : [];
foreach($extgroupids as $extgroupid) {
	if(!empty($_G['cache']['usergroups'][$extgroupid]['upgroupid'])) {
		$extgroupids[] = $_G['cache']['usergroups'][$extgroupid]['upgroupid'];
	}
}
space_merge($space, 'count');
$credits = $space['credits'];
$forumselect = '';
$activeus = [];
$activeus[$do] = ' class="a"';

if(in_array($do, ['buy', 'exit'])) {

	if($_G['groupid'] == 4 && $_G['member']['groupexpiry'] > 0 && $_G['member']['groupexpiry'] > TIMESTAMP) {
		showmessage('usergroup_switch_not_allow');
	}

	$groupid = intval($_GET['groupid']);

	$group = table_common_usergroup::t()->fetch($groupid);
	if($group['type'] != 'special' || $group['system'] == 'private' || $group['radminid'] != 0) {
		$group = null;
	}
	if(empty($group)) {
		showmessage('usergroup_not_found');
	}
	$join = $do == 'buy' ? 1 : 0;
	$group['dailyprice'] = $group['minspan'] = 0;

	if($group['system'] != 'private') {
		list($group['dailyprice'], $group['minspan']) = explode("\t", $group['system']);
		if($group['dailyprice'] > -1 && $group['minspan'] == 0) {
			$group['minspan'] = 1;
		}
	}
	$creditstrans = $_G['setting']['creditstrans'];
	if(!isset($_G['setting']['creditstrans'])) {
		showmessage('credits_transaction_disabled');
	}

	if(!submitcheck('buysubmit')) {
		$usermoney = $space['extcredits'.$creditstrans];
		$usermaxdays = $group['dailyprice'] > 0 ? intval($usermoney / $group['dailyprice']) : 0;
		$group['minamount'] = floatval($group['dailyprice']) * floatval($group['minspan']);
	} else {
		$memberfieldforum = table_common_member_field_forum::t()->fetch($_G['uid']);
		$groupterms = dunserialize($memberfieldforum['groupterms']);
		unset($memberfieldforum);
		require_once libfile('function/forum');
		if($join) {
			$extgroupidsarray = [];
			foreach(array_unique(array_merge($extgroupids, [$groupid])) as $extgroupid) {
				if($extgroupid) {
					$extgroupidsarray[] = $extgroupid;
				}
			}
			$extgroupidsnew = implode("\t", fixedupgroup($extgroupidsarray));
			if($group['dailyprice']) {
				if(($days = intval($_GET['days'])) < $group['minspan']) {
					showmessage('usergroups_span_invalid', '', ['minspan' => $group['minspan']]);
				}

				if($space['extcredits'.$creditstrans] - ($amount = $days * $group['dailyprice']) < ($minbalance = 0)) {
					showmessage('credits_balance_insufficient', '', ['title' => $_G['setting']['extcredits'][$creditstrans]['title'], 'minbalance' => ($minbalance + $amount)]);
				}

				$groupterms['ext'][$groupid] = ($groupterms['ext'][$groupid] > TIMESTAMP ? $groupterms['ext'][$groupid] : TIMESTAMP) + $days * 86400;

				$groupexpirynew = groupexpiry($groupterms);

				table_common_member::t()->update($_G['uid'], ['groupexpiry' => $groupexpirynew, 'extgroupids' => $extgroupidsnew]);
				updatemembercount($_G['uid'], [$creditstrans => "-$amount"], true, 'UGP', $extgroupidsnew);

				table_common_member_field_forum::t()->update($_G['uid'], ['groupterms' => serialize($groupterms)]);

			} else {
				table_common_member::t()->update($_G['uid'], ['extgroupids' => $extgroupidsnew]);
			}

			showmessage('usergroups_join_succeed', 'home.php?mod=spacecp&ac=usergroup'.($_GET['gid'] ? "&gid={$_GET['gid']}" : '&do=list'), ['group' => $group['grouptitle']], ['showdialog' => 3, 'showmsg' => true, 'locationtime' => true]);

		} else {

			if($groupid != $_G['groupid']) {
				if(isset($groupterms['ext'][$groupid])) {
					unset($groupterms['ext'][$groupid]);
				}
				$groupexpirynew = groupexpiry($groupterms);
				table_common_member_field_forum::t()->update($_G['uid'], ['groupterms' => serialize($groupterms)]);

			} else {
				$groupexpirynew = 'groupexpiry';
			}

			$extgroupidsarray = [];
			foreach($extgroupids as $extgroupid) {
				if($extgroupid && $extgroupid != $groupid) {
					$extgroupidsarray[] = $extgroupid;
				}
			}
			$extgroupidsnew = implode("\t", fixedupgroup($extgroupidsarray));
			table_common_member::t()->update($_G['uid'], ['groupexpiry' => $groupexpirynew, 'extgroupids' => $extgroupidsnew]);

			showmessage('usergroups_exit_succeed', 'home.php?mod=spacecp&ac=usergroup'.($_GET['gid'] ? "&gid={$_GET['gid']}" : '&do=list'), ['group' => $group['grouptitle']], ['showdialog' => 3, 'showmsg' => true, 'locationtime' => true]);

		}

	}

} elseif($do == 'switch') {

	$groupid = intval($_GET['groupid']);
	$extgroupids = fixedupgroup($extgroupids);
	if(!in_array($groupid, $extgroupids)) {
		showmessage('usergroup_not_found');
	}
	if($_G['groupid'] == 4 && $_G['member']['groupexpiry'] > 0 && $_G['member']['groupexpiry'] > TIMESTAMP) {
		showmessage('usergroup_switch_not_allow');
	}
	$group = table_common_usergroup::t()->fetch($groupid);
	if(!$group['allowvisit']) {
		showmessage('usergroup_switch_not_allowvisit');
	}
	if($group['upgroupid'] > 0) {
		$creditsext = (new credit())->countcredit_usergroup($_G['uid'], $group['upgroupid']);
		$checkcredit = $creditsext !== null ? $creditsext : $_G['member']['credits'];
		$group = table_common_usergroup::t()->fetch_by_credits_special($checkcredit, $group['upgroupid']);
		$groupid = $group['groupid'];
	}
	if(submitcheck('groupsubmit')) {
		$memberfieldforum = table_common_member_field_forum::t()->fetch($_G['uid']);
		$groupterms = dunserialize($memberfieldforum['groupterms']);
		unset($memberfieldforum);
		$extgroupidsnew = $_G['groupid'];
		$groupexpirynew = $groupterms['ext'][$groupid];
		foreach($extgroupids as $extgroupid) {
			if($extgroupid && $extgroupid != $groupid) {
				$extgroupidsnew .= "\t".$extgroupid;
			}
		}
		$extgroupidsnew = implode("\t", fixedupgroup(explode("\t", $extgroupidsnew), $groupid));
		if($_G['adminid'] > 0 && $group['radminid'] > 0) {
			$newadminid = $_G['adminid'] < $group['radminid'] ? $_G['adminid'] : $group['radminid'];
		} elseif($_G['adminid'] > 0) {
			$newadminid = $_G['adminid'];
		} else {
			$newadminid = $group['radminid'];
		}

		table_common_member::t()->update($_G['uid'], ['groupid' => $groupid, 'adminid' => $newadminid, 'groupexpiry' => $groupexpirynew, 'extgroupids' => $extgroupidsnew]);
		$_G['member']['groupid'] = $groupid;
		$_G['member']['adminid'] = $newadminid;
		checkusergroup();
		showmessage('usergroups_switch_succeed', 'home.php?mod=spacecp&ac=usergroup'.($_GET['gid'] ? "&gid={$_GET['gid']}" : '&do=list'), ['group' => $group['grouptitle']], ['showdialog' => 3, 'showmsg' => true, 'locationtime' => true]);
	}

} elseif($do == 'forum') {

	if($_G['setting']['verify']['enabled']) {
		$myverify = [];
		getuserprofile('verify1');
		for($i = 1; $i <= 6; $i++) {
			if($_G['member']['verify'.$i] == 1) {
				$myverify[] = $i;
			}
		}
		$ar = [1, 2, 3, 4, 5];
	}
	$language = lang('forum/misc');
	$permlang = $language;
	loadcache('forums');
	$fids = array_keys($_G['cache']['forums']);
	$perms = ['viewperm', 'postperm', 'replyperm', 'getattachperm', 'postattachperm', 'postimageperm'];
	$defaultperm = [
		['viewperm' => 1, 'postperm' => 0, 'replyperm' => 0, 'getattachperm' => 1, 'postattachperm' => 0, 'postimageperm' => 0],
		['viewperm' => 1, 'postperm' => 1, 'replyperm' => 1, 'getattachperm' => 1, 'postattachperm' => 1, 'postimageperm' => 1],
	];
	if($_G['setting']['verify']['enabled']) {
		for($i = 1; $i <= 6; $i++) {
			if($_G['setting']['verify'][$i]['available']) {
				$verifyicon[$i] = !empty($_G['setting']['verify'][$i]['icon']) ? '<img src="'.$_G['setting']['verify'][$i]['icon'].'" alt="'.$_G['setting']['verify'][$i]['title'].'" class="vm" title="'.$_G['setting']['verify'][$i]['title'].'" />' : $_G['setting']['verify'][$i]['title'];
			}
		}
	}
	$forumperm = $verifyperm = $myverifyperm = [];
	$query = table_forum_forum::t()->fetch_all_info_by_fids($fids);
	foreach($query as $forum) {
		foreach($perms as $perm) {
			if($forum[$perm]) {
				if($_G['setting']['verify']['enabled']) {
					for($i = 1; $i <= 6; $i++) {
						$verifyperm[$forum['fid']][$perm] .= preg_match("/(^|\t)(v".$i.")(\t|$)/", $forum[$perm]) ? $verifyicon[$i] : '';
						$includePerm = preg_match("/(^|\t)(v".$i.")(\t|$)/", $forum[$perm]) ? $verifyicon[$i] : '';
						if(in_array($i, $myverify) && $includePerm) {
							$myverifyperm[$forum['fid']][$perm] = 1;
						}
					}
				}
				$groupids = array_merge([$_G['groupid']], explode("\t", $_G['member']['extgroupids']));
				$forumperm[$forum['fid']][$perm] = preg_match("/(^|\t)(".implode('|', $groupids).")(\t|$)/", $forum[$perm]) ? 1 : 0;
			} else {
				$forumperm[$forum['fid']][$perm] = $defaultperm[$_G['groupid'] != 7 ? 1 : 0][$perm];
			}
		}
	}

} elseif($do == 'list' || $do == 'expiry') {

	if(!empty($_G['group']['upgroupid'])) {
		$tgroupid = $_G['groupid'];
		$_G['groupid'] = $_G['group']['upgroupid'];
		$_G['cache']['usergroups'][$_G['groupid']]['grouptitle'] = $_G['cache']['usergroups'][$tgroupid]['grouptitle'];
	}

	$memberfieldforum = table_common_member_field_forum::t()->fetch($_G['uid']);
	$groupterms = dunserialize($memberfieldforum['groupterms']);
	unset($memberfieldforum);
	$expgrouparray = $expirylist = $termsarray = [];

	if(!empty($groupterms['ext']) && is_array($groupterms['ext'])) {
		$termsarray = $groupterms['ext'];
	}
	if(!empty($groupterms['main']['time']) && (empty($termsarray[$_G['groupid']]) || $termsarray[$_G['groupid']] > $groupterm['main']['time'])) {
		$termsarray[$_G['groupid']] = $groupterms['main']['time'];
	}

	foreach($termsarray as $expgroupid => $expiry) {
		if($expiry <= TIMESTAMP) {
			$expgrouparray[] = $expgroupid;
		}
	}

	if(!empty($groupterms['ext'])) {
		foreach($groupterms['ext'] as $extgroupid => $time) {
			$expirylist[$extgroupid] = ['time' => dgmdate($time, 'd'), 'type' => 'ext', 'noswitch' => $time < TIMESTAMP];
		}
	}

	if(!empty($groupterms['main'])) {
		$expirylist[$_G['groupid']] = ['time' => dgmdate($groupterms['main']['time'], 'd'), 'type' => 'main'];
	}

	$groupids = [];
	foreach($_G['cache']['usergroups'] as $groupid => $usergroup) {
		if(!empty($usergroup['pubtype']) && empty($usergroup['upgroupid'])) {
			$groupids[] = $groupid;
		}
	}
	$expiryids = array_keys($expirylist);
	if(!$expiryids && $_G['member']['groupexpiry']) {
		table_common_member::t()->update($_G['uid'], ['groupexpiry' => 0]);
	}
	$extgroupids = fixedupgroup($extgroupids);
	$groupids = array_merge($expiryids, $groupids, fixedupgroup($extgroupids));
	$usermoney = $space['extcredits'.$_G['setting']['creditstrans']];
	if($groupids) {
		foreach(table_common_usergroup::t()->fetch_all_usergroup($groupids) as $group) {
			$isexp = in_array($group['groupid'], $expgrouparray);
			if($_G['cache']['usergroups'][$group['groupid']]['pubtype'] == 'buy') {
				list($dailyprice) = explode("\t", $group['system']);
				$expirylist[$group['groupid']]['dailyprice'] = $dailyprice;
				$expirylist[$group['groupid']]['usermaxdays'] = $dailyprice > 0 ? round($usermoney / $dailyprice) : 0;
			} else {
				$expirylist[$group['groupid']]['usermaxdays'] = 0;
			}
			$expirylist[$group['groupid']]['maingroup'] = $group['type'] != 'special' || $group['system'] == 'private' || $group['radminid'] > 0;
			$expirylist[$group['groupid']]['grouptitle'] = $isexp ? '<s>'.$group['grouptitle'].'</s>' : $group['grouptitle'];
		}
	}

	foreach($expirylist as $groupid => $data) {
		if($groupid == $_G['groupid']) {
			unset($expirylist[$groupid]);
			continue;
		}
		$expirylist[$groupid]['grouptitle'] = $expirylist[$groupid]['grouptitle'].g_icon($groupid, 1);
		if(!empty($_G['cache']['usergroups'][$groupid]['upgroupid'])) {
			$upgroupid = $_G['cache']['usergroups'][$groupid]['upgroupid'];
			if($upgroupid > 0 && !empty($expirylist[$upgroupid])) {
				if($groupid != $upgroupid) {
					$expirylist[$upgroupid]['grouptitle'] = $expirylist[$groupid]['grouptitle'];
					unset($expirylist[$groupid]);
				}
			}
		}
	}
} else {

	$extgroupids = fixedupgroup($extgroupids);
	$language = lang('forum/misc');
	require_once libfile('function/forumlist');
	$permlang = $language;
	unset($language);
	$maingroup = $_G['group'];
	$maingroup['grouptitle'] .= g_icon($maingroup['groupid'], 1);
	$ptype = in_array(getgpc('ptype'), [0, 1, 2]) ? intval(getgpc('ptype')) : 0;
	$cachekey = [];
	foreach($_G['cache']['usergroups'] as $gid => $value) {
		$cachekey[] = 'usergroup_'.$gid;
	}
	loadcache($cachekey);
	$_G['group'] = $maingroup;
	$sidegroup = $usergroups = $activegs = [];
	$nextupgradeid = $nextexist = 0;
	$memberfieldforum = table_common_member_field_forum::t()->fetch($_G['uid']);
	$groupterms = dunserialize($memberfieldforum['groupterms']);
	unset($memberfieldforum);
	$switchmaingroup = $_G['group']['grouppublic'] || isset($groupterms['ext']) ? 1 : 0;
	foreach($_G['cache']['usergroups'] as $gid => $group) {
		$group['grouptitle'] = strip_tags($group['grouptitle']).g_icon($gid, 1);
		if($group['type'] == 'special') {
			$type = $_G['cache']['usergroup_'.$gid]['radminid'] ? 'admin' : 'user';
			if($group['upgroupid'] > 0) {
				$type = 'upgrade';
			}
		} elseif($group['type'] == 'system') {
			$type = $_G['cache']['usergroup_'.$gid]['radminid'] ? 'admin' : 'user';
		} elseif($group['type'] == 'member') {
			$type = 'upgrade';
		}
		if($nextupgradeid && $group['type'] == 'member') {
			$_GET['gid'] = $gid;
			$nextupgradeid = 0;
		}
		$g = '';
		if($group['type'] == 'special' && $group['upgroupid'] > 0) {
			if($group['upgroupid'] == $gid) {
				$upgroups[$gid] = $_G['setting']['upgroup_name'][$gid];
			}
			$type = 'up'.$group['upgroupid'];
		}
		$g .= '<a href="home.php?mod=spacecp&ac=usergroup&gid='.$gid.'"'.(!empty($_GET['gid']) && $_GET['gid'] == $gid ? ' class="xi1"' : '').'>'.$group['grouptitle'].'</a>';
		if(in_array($gid, $extgroupids)) {
			$usergroups['my'] .= $g;
		}
		$usergroups[$type] = ($usergroups[$type] ?? '').$g;
		if(!empty($_GET['gid']) && $_GET['gid'] == $gid) {
			$switchtype = $type;
			if(!empty($_GET['gid'])) {
				$activegs[$switchtype] = ' a';
			}
			$currentgrouptitle = $group['grouptitle'];
			$sidegroup = $_G['cache']['usergroup_'.$gid];
			if($_G['cache']['usergroup_'.$gid]['radminid']) {
				$admingids[] = $gid;
			}
		} elseif(empty($_GET['gid']) && $_G['groupid'] == $gid && $group['type'] == 'member') {
			$nextupgradeid = 1;
		}
	}
	$usergroups['my'] = '<a href="home.php?mod=spacecp&ac=usergroup">'.$maingroup['grouptitle'].'</a>'.($usergroups['my'] ?? '');
	if($activegs == []) {
		$activegs['my'] = ' a';
	}

	$bperms = ['allowvisit', 'readaccess', 'allowinvisible', 'allowsearch', 'allowcstatus', 'disablepostctrl', 'allowsendpm', 'allowfriend', 'allowstatdata'];
	if($_G['setting']['portalstatus']) {
		$bperms[] = 'allowpostarticle';
	}
	$pperms = ['allowpost', 'allowreply', 'allowpostpoll', 'allowvote', 'allowpostreward', 'allowpostactivity', 'allowpostdebate', 'allowposttrade', 'allowat', 'allowreplycredit', 'allowposttag', 'allowcreatecollection', 'maxsigsize', 'allowsigbbcode', 'allowsigimgcode', 'allowrecommend', 'raterange', 'allowcommentpost', 'allowmediacode'];
	$aperms = ['allowgetattach', 'allowgetimage', 'allowpostattach', 'allowpostimage', 'allowsetattachperm', 'maxattachsize', 'maxsizeperday', 'maxattachnum', 'attachextensions'];
	$sperms = ['allowpoke', 'allowclick', 'allowcomment', 'maxspacesize', 'maximagesize'];
	if(helper_access::check_module('blog')) {
		$sperms[] = 'allowblog';
	}
	if(helper_access::check_module('album')) {
		$sperms[] = 'allowupload';
	}
	if(helper_access::check_module('share')) {
		$sperms[] = 'allowshare';
	}
	if(helper_access::check_module('doing')) {
		$sperms[] = 'allowdoing';
	}
	$allperms = [];
	$allkey = array_merge($bperms, $pperms, $aperms, $sperms);
	if($sidegroup) {
		foreach($allkey as $pkey) {
			if(in_array($pkey, ['maxattachsize', 'maxsizeperday', 'maxspacesize', 'maximagesize'])) {
				$sidegroup[$pkey] = $sidegroup[$pkey] ? sizecount($sidegroup[$pkey]) : 0;
			}
			$allperms[$pkey][$sidegroup['groupid']] = $sidegroup[$pkey];
		}
	}

	foreach($maingroup as $pkey => $v) {
		if(in_array($pkey, ['maxattachsize', 'maxsizeperday', 'maxspacesize', 'maximagesize'])) {
			$maingroup[$pkey] = $maingroup[$pkey] ? sizecount($maingroup[$pkey]) : 0;
		}
	}

	$publicgroup = [];
	$extgroupids[] = $_G['groupid'];
	foreach(table_common_usergroup::t()->fetch_all_switchable(array_unique($extgroupids)) as $group) {
		$group['allowsetmain'] = in_array($group['groupid'], $extgroupids);
		$publicgroup[$group['groupid']] = $group;
	}
	$group = $group[count($group)] ?? NULL;
	$_GET['perms'] = 'member';
	if($sidegroup) {
		$group = $sidegroup;
	}

	$creditstype = $upgroup_creditsformulaexp = '';
	loadcache('usergroup_'.$group['groupid']);
	$upgroupid = $_G['cache']['usergroup_'.$group['groupid']]['upgroupid'];
	if($_G['cache']['usergroup_'.$group['groupid']]['grouptype'] == 'special' && !empty($upgroupid) && $_G['cache']['usergroup_'.$upgroupid]['creditsformula']) {
		$creditstype = $_G['setting']['upgroup_name'][$upgroupid];
		$upgroup_creditsformulaexp = strip_tags($_G['cache']['usergroup_'.$upgroupid]['creditsformulaexp']);
	}

	$upgroup_credits = 0;
	$upgroup_name = '';
	$upgroupid = $_G['cache']['usergroup_'.$_G['groupid']]['upgroupid'];
	if($_G['cache']['usergroup_'.$_G['groupid']]['grouptype'] == 'special' && !empty($upgroupid)) {
		$upgroup_credits = (new credit())->countcredit_usergroup($_G['uid'], $upgroupid);
		loadcache('usergroup_'.$upgroupid);
		if($_G['cache']['usergroup_'.$upgroupid]['creditsformula'] && !empty($_G['setting']['upgroup_name'][$upgroupid])) {
			$upgroup_name = $_G['setting']['upgroup_name'][$upgroupid];
		}
		$creditleft = $group['groupcreditshigher'] - $upgroup_credits;
	} else {
		$creditleft = $group['groupcreditshigher'] - $_G['member']['credits'];
	}
}

include_once template('home/spacecp_usergroup');

function fixedupgroup($extgroupids, $exceptgid = 0) {
	global $_G;
	$extgroupids = array_unique($extgroupids);
	$upgroupids = [];
	foreach($extgroupids as $k => $extgroupid) {
		if($exceptgid > 0 && $extgroupid == $exceptgid) {
			unset($extgroupids[$k]);
		}
		if($_G['cache']['usergroups'][$extgroupid]['upgroupid']) {
			$upgroupids[] = $_G['cache']['usergroups'][$extgroupid]['upgroupid'];
		}
	}
	$upgroupids = array_unique($upgroupids);
	if($upgroupids) {
		foreach($_G['cache']['usergroups'] as $gid => $v) {
			if($exceptgid > 0 && $gid == $exceptgid) {
				continue;
			}
			if(in_array($v['upgroupid'], $upgroupids)) {
				$extgroupids[] = $gid;
			}
		}
		foreach($extgroupids as $k => $extgroupid) {
			if($_G['cache']['usergroups'][$extgroupid]['upgroupid']) {
				$upgroup_credits = (new credit())->countcredit_usergroup($_G['member']['uid'], $_G['cache']['usergroups'][$extgroupid]['upgroupid']);
				$credits = $upgroup_credits ?: $_G['member']['credits'];
				if($_G['cache']['usergroups'][$extgroupid]['creditshigher'] > $credits ||
					$_G['cache']['usergroups'][$extgroupid]['creditslower'] <= $credits) {
					unset($extgroupids[$k]);
				}
			}
		}
	}
	$extgroupids = array_unique($extgroupids);
	return $extgroupids;
}

