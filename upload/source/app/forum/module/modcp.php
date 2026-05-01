<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

const IN_MODCP = true;

if(!$_G['setting']['forumstatus'] && !in_array($_GET['action'], ['', 'home', 'moderate', 'ban', 'ipban', 'member', 'log', 'login', 'logout'])) {
	showmessage('forum_status_off');
} else if(!$_G['setting']['forumstatus']) {
	unset($_G['fid'], $_GET['fid'], $_POST['fid']);
}

$cpscript = basename($_G['PHP_SELF']);
if(!empty($_G['forum']) && $_G['forum']['status'] == 3) {
	showmessage('group_admin_enter_panel', 'forum.php?mod=group&action=manage&fid='.$_G['fid']);
}

$modsession = new discuz_panel(MODCP_PANEL);
if(getgpc('login_panel') && getgpc('cppwd') && submitcheck('submit')) {
	$modsession->dologin($_G['uid'], getgpc('cppwd'), true);
}

if(!$modsession->islogin) {
	$_GET['action'] = 'login';
}

if(getgpc('action') == 'logout') {
	$modsession->dologout();
	showmessage('modcp_logout_succeed', 'forum.php');
}

$modforums = $modsession->get('modforums');
$_GET['action'] = empty($_GET['action']) ? (($_G['setting']['forumstatus'] && $_G['fid']) ? 'thread' : 'home') : $_GET['action'];
$op = getgpc('op');
if($modforums === null) {
	$modforums = ['fids' => '', 'list' => [], 'recyclebins' => []];
	$comma = '';
	if($_G['adminid'] == 3) {
		foreach(table_forum_moderator::t()->fetch_all_by_uid_forum($_G['uid']) as $tforum) {
			$modforums['fids'] .= $comma.$tforum['fid'];
			$comma = ',';
			$modforums['recyclebins'][$tforum['fid']] = $tforum['recyclebin'];
			$modforums['list'][$tforum['fid']] = strip_tags($tforum['name']);
		}
	} else {
		$query = table_forum_forum::t()->fetch_all_info_by_fids(0, 1, 0, 0, 0, 1, 1);
		if(!empty($_G['member']['accessmasks'])) {
			$fids = array_keys($query);
			$accesslist = table_forum_access::t()->fetch_all_by_fid_uid($fids, $_G['uid']);
			foreach($query as $key => $val) {
				$query[$key]['allowview'] = $accesslist[$key];
			}
		}
		foreach($query as $tforum) {
			$tforum['allowview'] = !isset($tforum['allowview']) ? 0 : $tforum['allowview'];
			if($tforum['allowview'] == 1 || ($tforum['allowview'] == 0 && ((!$tforum['viewperm'] && $_G['group']['readaccess']) || ($tforum['viewperm'] && forumperm($tforum['viewperm']))))) {
				$modforums['fids'] .= $comma.$tforum['fid'];
				$comma = ',';
				$modforums['recyclebins'][$tforum['fid']] = $tforum['recyclebin'];
				$modforums['list'][$tforum['fid']] = strip_tags($tforum['name']);
			}
		}
	}

	$modsession->set('modforums', $modforums, true);
}

$threadclasslist = [];
if($_G['fid'] && in_array($_G['fid'], explode(',', $modforums['fids']))) {
	foreach(table_forum_threadclass::t()->fetch_all_by_fid($_G['fid']) as $tc) {
		$threadclasslist[] = $tc;
	}
}

if($_G['fid'] && $_G['forum']['ismoderator']) {
	dsetcookie('modcpfid', $_G['fid']);
	$forcefid = "&amp;fid={$_G['fid']}";
} elseif(!empty($modforums) && count($modforums['list']) == 1) {
	$forcefid = "&amp;fid={$modforums['fids']}";
} else {
	$forcefid = '';
}

$script = $modtpl = '';
switch($_GET['action']) {

	case 'announcement':
		$_G['group']['allowpostannounce'] && $script = 'announcement';
		break;

	case 'member':
		$op == 'edit' && $_G['group']['allowedituser'] && $script = 'member';
		$op == 'ban' && ($_G['group']['allowbanuser'] || $_G['group']['allowbanvisituser']) && $script = 'member';
		$op == 'ipban' && $_G['group']['allowbanip'] && $script = 'member';
		break;

	case 'moderate':
		($op == 'threads' || $op == 'replies') && $_G['group']['allowmodpost'] && $script = 'moderate';
		$op == 'members' && $_G['group']['allowmoduser'] && $script = 'moderate';
		break;

	case 'forum':
		$op == 'editforum' && $_G['group']['alloweditforum'] && $script = 'forum';
		$op == 'recommend' && $_G['group']['allowrecommendthread'] && $script = 'forum';
		$op == 'member' && $_G['group']['alloweditforum'] && $script = 'forum';
		break;

	case 'forumaccess':
		$_G['group']['allowedituser'] && $script = 'forumaccess';
		break;

	case 'log':
		$_G['group']['allowviewlog'] && $script = 'log';
		break;

	case 'login':
		$script = $modsession->islogin ? 'home' : 'login';
		break;

	case 'thread':
		$script = 'thread';
		break;

	case 'recyclebin':
		$script = 'recyclebin';
		break;

	case 'recyclebinpost':
		$script = 'recyclebinpost';
		break;

	case 'plugin':
		$script = 'plugin';
		break;

	case 'report':
		$script = 'report';
		break;

	default:
		$_GET['action'] = $script = 'home';
		$modtpl = 'modcp_home';
}

$script = empty($script) ? 'noperm' : $script;
$modtpl = empty($modtpl) ? (!empty($script) ? 'modcp_'.$script : '') : $modtpl;
$modtpl = 'forum/'.$modtpl;
$op = isset($op) ? trim($op) : '';

if($script != 'log') {
	require_once libfile('function/misc');

	$extralog = implodearray(['GET' => $_GET, 'POST' => $_POST], ['cppwd', 'formhash', 'submit', 'addsubmit']);
	
	if($_G['setting']['log']['modcp']) {
		$errorlog = [
			'timestamp' => TIMESTAMP,
			'operator_username' => $_G['username'],
			'operator_adminid' => $_G['adminid'],
			'clientip' => $_G['clientip'],
			'action' => getgpc('action'),
			'op' => $op,
			'fid' => $_G['fid'],
			'extralog' => clearlogstring($extralog),
		];
		$member_log = getuserbyuid($_G['adminid']);
		logger('modcp', $member_log, $_G['member']['uid'], $errorlog);
	}
	
}

require childfile($script);

$reportnum = $modpostnum = $modthreadnum = $modforumnum = 0;
$modforumnum = count($modforums['list']);
$modnum = '';
if($modforumnum) {
	if(!empty($_G['setting']['moddetail'])) {
		if($_G['group']['allowmodpost']) {
			$modnum = table_common_moderate::t()->count_by_idtype_status_fid('tid', 0, explode(',', $modforums['fids']));
			$modnum += table_common_moderate::t()->count_by_idtype_status_fid('pid', 0, explode(',', $modforums['fids']));
		}
		if($_G['group']['allowmoduser']) {
			$modnum += table_common_member_validate::t()->count_by_status(0);
		}
	}
}

$joinchecknum = '';
if($_G['fid'] && $_G['forum']['ismoderator'] && $_G['forum']['jointype'] == 2) {
	$joinchecknum = table_forum_groupuser::t()->fetch_count_by_fid($_G['fid'], 1);
}

$access = match (intval($_G['adminid'])) {
	1 => '1,2,3,4,5,6,7',
	2 => '2,3,6,7',
	default => '1,3,5,7',
};
$notenum = table_common_adminnote::t()->count_by_access(explode(',', $access));

include template('forum/modcp');

function getposttableselect() {
	global $_G;

	loadcache('posttable_info');
	if(!empty($_G['cache']['posttable_info']) && is_array($_G['cache']['posttable_info'])) {
		$posttableselect = '<select name="posttableid" id="posttableid" class="ps">';
		foreach($_G['cache']['posttable_info'] as $posttableid => $data) {
			$posttableselect .= '<option value="'.$posttableid.'"'.($_GET['posttableid'] == $posttableid ? ' selected="selected"' : '').'>'.($data['memo'] ? $data['memo'] : 'post_'.$posttableid).'</option>';
		}
		$posttableselect .= '</select>';
	} else {
		$posttableselect = '';
	}
	return $posttableselect;
}

