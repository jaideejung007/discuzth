<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function discuz_uc_avatar($uid, $size = '', $returnsrc = FALSE) {
	global $_G;
	return avatar($uid, $size, $returnsrc, FALSE, $_G['setting']['avatarmethod'], $_G['setting']['ucenterurl']);
}

function dunlink($attach) {
	global $_G;
	$filename = $attach['attachment'];
	$havethumb = $attach['thumb'];
	$remote = $attach['remote'];
	if($remote) {
		ftpcmd('delete', 'forum/'.$filename);
		$havethumb && ftpcmd('delete', 'forum/'.getimgthumbname($filename));
	} else {
		@unlink($_G['setting']['attachdir'].'/forum/'.$filename);
		$havethumb && @unlink($_G['setting']['attachdir'].'/forum/'.getimgthumbname($filename));
	}
	if($attach['aid']) {
		@unlink($_G['setting']['attachdir'].'image/'.$attach['aid'].'_100_100.jpg');
	}
}

function formulaperm($formula) {
	global $_G;
	if($_G['forum']['ismoderator']) {
		return TRUE;
	}

	$formula = dunserialize($formula);
	$medalperm = $formula['medal'];
	$permusers = $formula['users'];
	$permmessage = $formula['message'];
	if(!empty($formula['viewtype'])) {
		if(!$_G['uid'] || !empty($formula['viewtype_gids']) && !in_array($_G['groupid'], $formula['viewtype_gids'])) {
			loadcache('usergroups');
			$usergroups = [];
			foreach($formula['viewtype_gids'] as $gid) {
				if(empty($_G['cache']['usergroups'][$gid]['grouptitle'])) {
					continue;
				}
				$usergroups[] = $_G['cache']['usergroups'][$gid]['grouptitle'];
			}
			showmessage('forum_member_error', NULL, ['groups' => implode(',', $usergroups)], ['login' => 1]);
		}
		$joinuser = table_forum_groupuser::t()->fetch_userinfo($_G['uid'], $formula['viewtype_fid']);
		if(!$joinuser) {
			showmessage('forum_member_apply', '',
				['url' => 'forum.php?mod=member&action=join&fid='.$formula['viewtype_fid'].'&formhash='.formhash()],
				['alert' => 'info']);
		} elseif(!$joinuser['level']) {
			showmessage('forum_member_applied');
		}
	}

	if($_G['setting']['medalstatus'] && $medalperm) {
		$exists = 1;
		$_G['forum_formulamessage'] = '';
		$medalpermc = $medalperm;
		if($_G['uid']) {
			$memberfieldforum = table_common_member_field_forum::t()->fetch($_G['uid']);
			$medals = explode("\t", $memberfieldforum['medals']);
			unset($memberfieldforum);
			foreach($medalperm as $k => $medal) {
				foreach($medals as $r) {
					list($medalid) = explode('|', $r);
					if($medalid == $medal) {
						$exists = 0;
						unset($medalpermc[$k]);
					}
				}
			}
		} else {
			$exists = 0;
		}
		if($medalpermc) {
			loadcache('medals');
			foreach($medalpermc as $medal) {
				if($_G['cache']['medals'][$medal]) {
					$_G['forum_formulamessage'] .= '<img src="'.$_G['cache']['medals'][$medal]['image'].'" style="vertical-align:middle;" />&nbsp;'.$_G['cache']['medals'][$medal]['name'].'&nbsp; ';
				}
			}
			showmessage('forum_permforum_nomedal', NULL, ['forum_permforum_nomedal' => $_G['forum_formulamessage']], ['login' => 1]);
		}
	}
	$formulatext = $formula[0];
	$formula = trim($formula[1]);
	if($_G['adminid'] == 1 || $_G['forum']['ismoderator'] || in_array($_G['groupid'], explode("\t", $_G['forum']['spviewperm']))) {
		return FALSE;
	}
	if($permusers) {
		$permusers = str_replace(["\r\n", "\r"], ["\n", "\n"], $permusers);
		$permusers = explode("\n", trim($permusers));
		if(!in_array($_G['member']['username'], $permusers)) {
			showmessage('forum_permforum_disallow', NULL, [], ['login' => 1]);
		}
	}
	if(!$formula) {
		return FALSE;
	}
	if(strexists($formula, '$memberformula[')) {
		preg_match_all("/\\\$memberformula\['(\w+?)'\]/", $formula, $a);
		$profilefields = [];
		foreach($a[1] as $field) {
			switch($field) {
				case 'regdate':
					$formula = preg_replace_callback('/\{(\d{4})\-(\d{1,2})\-(\d{1,2})\}/', 'formulaperm_callback_123', $formula);
				case 'regday':
					break;
				case 'regip':
				case 'lastip':
					$formula = preg_replace('/\{([0-9a-fA-F\.\:\/]+?)\}/', "'\\1'", $formula);
					$formula = preg_replace('/(\$memberformula\[\'(regip|lastip)\'\])\s*=+\s*\'([0-9a-fA-F\.\:\/]+?)\'/', "ip::check_ip(\\1, '\\3')", $formula);
				case 'buyercredit':
				case 'sellercredit':
					space_merge($_G['member'], 'status');
					break;
				case str_starts_with($field, 'field'):
					space_merge($_G['member'], 'profile');
					$profilefields[] = $field;
					break;
			}
		}
		$memberformula = [];
		if($_G['uid']) {
			$memberformula = $_G['member'];
			if(in_array('regday', $a[1])) {
				$memberformula['regday'] = intval((TIMESTAMP - $memberformula['regdate']) / 86400);
			}
			if(in_array('regdate', $a[1])) {
				$memberformula['regdate'] = date('Y-m-d', $memberformula['regdate']);
			}
			$memberformula['lastip'] = $memberformula['lastip'] ? $memberformula['lastip'] : $_G['clientip'];
		} else {
			if(isset($memberformula['regip'])) {
				$memberformula['regip'] = $_G['clientip'];
			}
			if(isset($memberformula['lastip'])) {
				$memberformula['lastip'] = $_G['clientip'];
			}
		}
	}
	@eval("\$formulaperm = ($formula) ? TRUE : FALSE;");
	if(!$formulaperm) {
		if(!$permmessage) {
			$language = lang('forum/misc');
			$search = ['regdate', 'regday', 'regip', 'lastip', 'buyercredit', 'sellercredit', 'digestposts', 'posts', 'threads', 'oltime'];
			$replace = [$language['formulaperm_regdate'], $language['formulaperm_regday'], $language['formulaperm_regip'], $language['formulaperm_lastip'], $language['formulaperm_buyercredit'], $language['formulaperm_sellercredit'], $language['formulaperm_digestposts'], $language['formulaperm_posts'], $language['formulaperm_threads'], $language['formulaperm_oltime']];
			for($i = 1; $i <= 8; $i++) {
				$search[] = 'extcredits'.$i;
				$replace[] = $_G['setting']['extcredits'][$i]['title'] ? $_G['setting']['extcredits'][$i]['title'] : $language['formulaperm_extcredits'].$i;
			}
			if($profilefields) {
				loadcache(['fields_required', 'fields_optional']);
				foreach($profilefields as $profilefield) {
					$search[] = $profilefield;
					$replace[] = !empty($_G['cache']['fields_optional']['field_'.$profilefield]) ? $_G['cache']['fields_optional']['field_'.$profilefield]['title'] : $_G['cache']['fields_required']['field_'.$profilefield]['title'];
				}
			}
			$i = 0;
			$_G['forum_usermsg'] = '';
			foreach($search as $s) {
				if(in_array($s, ['digestposts', 'posts', 'threads', 'oltime', 'extcredits1', 'extcredits2', 'extcredits3', 'extcredits4', 'extcredits5', 'extcredits6', 'extcredits7', 'extcredits8'])) {
					$_G['forum_usermsg'] .= strexists($formulatext, $s) ? '<br />&nbsp;&nbsp;&nbsp;'.$replace[$i].': '.(@eval('return intval(getuserprofile(\''.$s.'\'));')) : '';
				} elseif(in_array($s, ['regdate', 'regip', 'regday'])) {
					$_G['forum_usermsg'] .= strexists($formulatext, $s) ? '<br />&nbsp;&nbsp;&nbsp;'.$replace[$i].': '.(@eval('return $memberformula[\''.$s.'\'];')) : '';
				}
				$i++;
			}
			$search = array_merge($search, ['and', 'or', '>=', '<=', '==']);
			$replace = array_merge($replace, ['&nbsp;&nbsp;<b>'.$language['formulaperm_and'].'</b>&nbsp;&nbsp;', '&nbsp;&nbsp;<b>'.$language['formulaperm_or'].'</b>&nbsp;&nbsp;', '&ge;', '&le;', '=']);
			$_G['forum_formulamessage'] = str_replace($search, $replace, $formulatext);
		} else {
			$_G['forum_formulamessage'] = $permmessage;
		}

		if(!$permmessage) {
			showmessage('forum_permforum_nopermission', NULL, ['formulamessage' => $_G['forum_formulamessage'], 'usermsg' => $_G['forum_usermsg']], ['login' => 1]);
		} else {
			showmessage('forum_permforum_nopermission_custommsg', NULL, ['formulamessage' => $_G['forum_formulamessage']], ['login' => 1]);
		}
	}
	return TRUE;
}

function formulaperm_callback_123($matches) {
	return '\''.$matches[1].'-'.sprintf('%02d', $matches[2]).'-'.sprintf('%02d', $matches[3]).'\'';
}

function medalformulaperm($formula, $type) {
	global $_G;

	$formula = dunserialize($formula);
	$permmessage = $formula['message'];
	$formula = $formula['medal'];
	if(!empty($formula['usergroupallow']) && is_array($formula['usergroups']) && !in_array($_G['groupid'], $formula['usergroups'])) {
		loadcache('usergroups');
		$message = [];
		foreach($formula['usergroups'] as $groupid) {
			$message[] = $_G['cache']['usergroups'][$groupid]['grouptitle'].' ';
		}
		$_G['forum_formulamessage'] = implode(', ', $message);
		$_G['forum_usermsg'] = $_G['cache']['usergroups'][$_G['groupid']]['grouptitle'];
		return FALSE;
	}
	$formulatext = $formula[0];
	$formula = $formula[1];
	if(!$formula) {
		return FALSE;
	}
	if(strexists($formula, '$memberformula[')) {
		preg_match_all("/\\\$memberformula\['(\w+?)'\]/", $formula, $a);
		$profilefields = [];
		foreach($a[1] as $field) {
			switch($field) {
				case 'regdate':
					$formula = preg_replace_callback('/\{(\d{4})\-(\d{1,2})\-(\d{1,2})\}/', 'medalformulaperm_callback_123', $formula);
				case 'regday':
					break;
				case 'regip':
				case 'lastip':
					$formula = preg_replace('/\{([0-9a-fA-F\.\:\/]+?)\}/', "'\\1'", $formula);
					$formula = preg_replace('/(\$memberformula\[\'(regip|lastip)\'\])\s*=+\s*\'([0-9a-fA-F\.\:\/]+?)\'/', "ip::check_ip(\\1, '\\3')", $formula);
				case 'buyercredit':
				case 'sellercredit':
					space_merge($_G['member'], 'status');
					break;
				case str_starts_with($field, 'field'):
					space_merge($_G['member'], 'profile');
					$profilefields[] = $field;
					break;
			}
		}
		$memberformula = [];
		if($_G['uid']) {
			$memberformula = $_G['member'];
			if(in_array('regday', $a[1])) {
				$memberformula['regday'] = intval((TIMESTAMP - $memberformula['regdate']) / 86400);
			}
			if(in_array('regdate', $a[1])) {
				$memberformula['regdate'] = date('Y-m-d', $memberformula['regdate']);
			}
			$memberformula['lastip'] = $memberformula['lastip'] ? $memberformula['lastip'] : $_G['clientip'];
		} else {
			if(isset($memberformula['regip'])) {
				$memberformula['regip'] = $_G['clientip'];
			}
			if(isset($memberformula['lastip'])) {
				$memberformula['lastip'] = $_G['clientip'];
			}
		}
	}
	@eval("\$formulaperm = ($formula) ? TRUE : FALSE;");
	if(!$formulaperm || $type == 1) {
		if(!$permmessage) {
			$language = lang('forum/misc');
			$search = ['regdate', 'regday', 'regip', 'lastip', 'buyercredit', 'sellercredit', 'digestposts', 'posts', 'threads', 'oltime'];
			$replace = [$language['formulaperm_regdate'], $language['formulaperm_regday'], $language['formulaperm_regip'], $language['formulaperm_lastip'], $language['formulaperm_buyercredit'], $language['formulaperm_sellercredit'], $language['formulaperm_digestposts'], $language['formulaperm_posts'], $language['formulaperm_threads'], $language['formulaperm_oltime']];
			for($i = 1; $i <= 8; $i++) {
				$search[] = 'extcredits'.$i;
				$replace[] = $_G['setting']['extcredits'][$i]['title'] ? $_G['setting']['extcredits'][$i]['title'] : $language['formulaperm_extcredits'].$i;
			}
			if($profilefields) {
				loadcache(['fields_required', 'fields_optional']);
				foreach($profilefields as $profilefield) {
					$search[] = $profilefield;
					$replace[] = !empty($_G['cache']['fields_optional']['field_'.$profilefield]) ? $_G['cache']['fields_optional']['field_'.$profilefield]['title'] : $_G['cache']['fields_required']['field_'.$profilefield]['title'];
				}
			}
			$i = 0;
			$_G['forum_usermsg'] = '';
			foreach($search as $s) {
				if(in_array($s, ['digestposts', 'posts', 'threads', 'oltime', 'extcredits1', 'extcredits2', 'extcredits3', 'extcredits4', 'extcredits5', 'extcredits6', 'extcredits7', 'extcredits8'])) {
					$_G['forum_usermsg'] .= strexists($formulatext, $s) ? '<br />&nbsp;&nbsp;&nbsp;'.$replace[$i].': '.(@eval('return intval(getuserprofile(\''.$s.'\'));')) : '';
				} elseif(in_array($s, ['regdate', 'regip'])) {
					$_G['forum_usermsg'] .= strexists($formulatext, $s) ? '<br />&nbsp;&nbsp;&nbsp;'.$replace[$i].': '.(@eval('return $memberformula[\''.$s.'\'];')) : '';
				}
				$i++;
			}
			$search = array_merge($search, ['and', 'or', '>=', '<=', '==']);
			$replace = array_merge($replace, ['&nbsp;&nbsp;<b>'.$language['formulaperm_and'].'</b>&nbsp;&nbsp;', '&nbsp;&nbsp;<b>'.$language['formulaperm_or'].'</b>&nbsp;&nbsp;', '&ge;', '&le;', '=']);
			$permmessage = str_replace($search, $replace, $formulatext);
		}
		if(!$formulaperm) {
			$_G['forum_formulamessage'] = $permmessage;
		}

		return $permmessage;
	} elseif($formulaperm && $type == 2) {
		return FALSE;
	}
	return TRUE;
}

function medalformulaperm_callback_123($matches) {
	return '\''.$matches[1].'-'.sprintf('%02d', $matches[2]).'-'.sprintf('%02d', $matches[3]).'\'';
}

function groupexpiry($terms) {
	$terms = is_array($terms) ? $terms : dunserialize($terms);
	$groupexpiry = isset($terms['main']['time']) ? intval($terms['main']['time']) : 0;
	if(is_array($terms['ext'])) {
		foreach($terms['ext'] as $expiry) {
			if((!$groupexpiry && $expiry) || $expiry < $groupexpiry) {
				$groupexpiry = $expiry;
			}
		}
	}
	return $groupexpiry;
}


function typeselect($curtypeid = 0) {
	global $_G;
	if($threadtypes = $_G['forum']['threadtypes']) {
		$html = '<select name="typeid" id="typeid"><option value="0">&nbsp;</option>';
		foreach($threadtypes['types'] as $typeid => $name) {
			$html .= '<option value="'.$typeid.'" '.($curtypeid == $typeid ? 'selected' : '').'>'.strip_tags($name).'</option>';
		}
		$html .= '</select>';
		return $html;
	} else {
		return '';
	}
}

function updatemodworks($modaction, $posts = 1) {
	global $_G;
	$today = dgmdate(TIMESTAMP, 'Y-m-d');
	if($_G['setting']['modworkstatus'] && $modaction && $posts) {
		$affect_rows = table_forum_modwork::t()->increase_count_posts_by_uid_modaction_dateline(1, $posts, $_G['uid'], $modaction, $today);
		if(!$affect_rows) {
			table_forum_modwork::t()->insert([
				'uid' => $_G['uid'],
				'modaction' => $modaction,
				'dateline' => $today,
				'count' => 1,
				'posts' => $posts,
			]);
		}
	}
}

function buildbitsql($fieldname, $position, $value) {
	$t = " `$fieldname`=`$fieldname`";
	if($value) {
		$t .= ' | '.setstatus($position, 1);
	} else {
		$t .= ' & '.setstatus($position, 0);
	}
	return $t.' ';
}

function showmessagenoperm($type, $fid, $formula = '') {
	global $_G;
	loadcache('usergroups');
	if($formula) {
		$formula = dunserialize($formula);
		$permmessage = stripslashes($formula['message']);
	}

	$usergroups = $nopermgroup = $forumnoperms = [];
	$nopermdefault = [
		'viewperm' => [],
		'getattachperm' => [],
		'postperm' => [7],
		'replyperm' => [7],
		'postattachperm' => [7],
	];
	$perms = ['viewperm', 'postperm', 'replyperm', 'getattachperm', 'postattachperm'];

	foreach($_G['cache']['usergroups'] as $gid => $usergroup) {
		$usergroups[$gid] = $usergroup['type'];
		$grouptype = $usergroup['type'] == 'member' ? 0 : 1;
		$nopermgroup[$grouptype][] = $gid;
	}
	if($fid == $_G['forum']['fid']) {
		$forum = $_G['forum'];
	} else {
		$forum = table_forum_forumfield::t()->fetch($fid);
	}

	foreach($perms as $perm) {
		$permgroups = explode("\t", $forum[$perm]);
		$membertype = $forum[$perm] ? array_intersect($nopermgroup[0], $permgroups) : TRUE;
		$forumnoperm = $forum[$perm] ? array_diff(array_keys($usergroups), $permgroups) : $nopermdefault[$perm];
		foreach($forumnoperm as $groupid) {
			$nopermtype = $membertype && $groupid == 7 ? 'login' : ($usergroups[$groupid] == 'system' || $usergroups[$groupid] == 'special' ? 'none' : ($membertype ? 'upgrade' : 'none'));
			$forumnoperms[$fid][$perm][$groupid] = [$nopermtype, $permgroups];
		}
	}

	$v = $forumnoperms[$fid][$type][$_G['groupid']][0];
	$gids = $forumnoperms[$fid][$type][$_G['groupid']][1];
	$comma = $permgroups = '';
	if(is_array($gids)) {
		foreach($gids as $gid) {
			if($gid && $_G['cache']['usergroups'][$gid]) {
				$permgroups .= $comma.$_G['cache']['usergroups'][$gid]['grouptitle'];
				$comma = ', ';
			} elseif($_G['setting']['verify']['enabled'] && str_starts_with($gid, 'v')) {
				$vid = substr($gid, 1);
				$permgroups .= $comma.$_G['setting']['verify'][$vid]['title'];
				$comma = ', ';
			}

		}
	}

	$custom = 0;
	if($permmessage) {
		$message = $permmessage;
		$custom = 1;
	} else {
		if($v) {
			$message = $type.'_'.$v.'_nopermission';
		} else {
			$message = 'group_nopermission';
		}
	}

	showmessage($message, NULL, ['fid' => $fid, 'permgroups' => $permgroups, 'grouptitle' => $_G['group']['grouptitle']], ['login' => 1], $custom);
}

function loadforum($fid = null, $tid = null) {
	global $_G;
	$tid = intval($tid ?? getgpc('tid'));
	if(isset($fid)) {
		$fid = intval($fid);
	} else {
		$fid = getgpc('fid');
		if(!$fid && getgpc('gid')) {
			$fid = intval(getgpc('gid'));
		}
	}
	if(isset($_G['forum']['fid']) && $_G['forum']['fid'] == $fid || isset($_G['thread']['tid']) && $_G['thread']['tid'] == $tid) {
		return null;
	}
	if(!empty($_GET['archiver'])) {
		if($fid) {
			dheader('location: archiver/?fid-'.$fid.'.html');
		} elseif($tid) {
			dheader('location: archiver/?tid-'.$tid.'.html');
		} else {
			dheader('location: archiver/');
		}
	}
	if(defined('IN_ARCHIVER') && $_G['setting']['archiverredirect'] && !IS_ROBOT) {
		dheader('location: ../forum.php'.($_G['mod'] ? '?mod='.$_G['mod'].(!empty($_GET['fid']) ? '&fid='.$_GET['fid'] : (!empty($_GET['tid']) ? '&tid='.$_GET['tid'] : '')) : ''));
	}
	if(isset($_G['setting']['forumpicstyle'])) {
		$_G['setting']['forumpicstyle'] = dunserialize($_G['setting']['forumpicstyle']);
		empty($_G['setting']['forumpicstyle']['thumbwidth']) && $_G['setting']['forumpicstyle']['thumbwidth'] = 203;
		empty($_G['setting']['forumpicstyle']['thumbheight']) && $_G['setting']['forumpicstyle']['thumbheight'] = 0;
	} else {
		$_G['setting']['forumpicstyle'] = ['thumbwidth' => 203, 'thumbheight' => 0];
	}
	if($fid) {
		$fid = is_numeric($fid) ? intval($fid) : (!empty($_G['setting']['forumfids'][$fid]) ? $_G['setting']['forumfids'][$fid] : 0);
	}

	$modthreadkey = isset($_GET['modthreadkey']) && $_GET['modthreadkey'] == modauthkey($tid) ? $_GET['modthreadkey'] : '';
	$_G['forum_auditstatuson'] = (bool)$modthreadkey;

	$metadescription = $hookscriptmessage = '';
	$adminid = $_G['adminid'];

	if(!empty($tid) || !empty($fid)) {

		if(!empty ($tid)) {
			$archiveid = !empty($_GET['archiveid']) ? intval($_GET['archiveid']) : null;
			$_G['thread'] = get_thread_by_tid($tid, $archiveid);
			$_G['thread']['allreplies'] = $_G['thread']['replies'] + $_G['thread']['comments'];
			if(!$_G['forum_auditstatuson'] && !empty($_G['thread'])
				&& !($_G['thread']['displayorder'] >= 0 || (in_array($_G['thread']['displayorder'], [-4, -3, -2]) && $_G['uid'] && $_G['thread']['authorid'] == $_G['uid']))) {
				$_G['thread'] = null;
			}

			$_G['forum_thread'] = &$_G['thread'];

			if(empty($_G['thread'])) {
				$fid = $tid = 0;
			} else {
				$fid = $_G['thread']['fid'];
				$tid = $_G['thread']['tid'];
			}
		}

		if($fid) {
			$forum = table_forum_forum::t()->fetch_info_by_fid($fid);
		}

		if($forum) {
			if($_G['uid']) {
				if($_G['member']['accessmasks']) {
					$query = table_forum_access::t()->fetch_all_by_fid_uid($fid, $_G['uid']);
					$forum['allowview'] = $query[0]['allowview'];
					$forum['allowpost'] = $query[0]['allowpost'];
					$forum['allowreply'] = $query[0]['allowreply'];
					$forum['allowgetattach'] = $query[0]['allowgetattach'];
					$forum['allowgetimage'] = $query[0]['allowgetimage'];
					$forum['allowpostattach'] = $query[0]['allowpostattach'];
					$forum['allowpostimage'] = $query[0]['allowpostimage'];
				}
				if($adminid == 3) {
					$forum['ismoderator'] = table_forum_moderator::t()->fetch_uid_by_fid_uid($fid, $_G['uid']);
				}
			}
			$forum['ismoderator'] = !empty($forum['ismoderator']) || $adminid == 1 || $adminid == 2 ? 1 : 0;
			$fid = $forum['fid'];
			$gorup_admingroupids = $_G['setting']['group_admingroupids'] ? dunserialize($_G['setting']['group_admingroupids']) : ['1' => '1'];

			if($forum['status'] == 3) {
				if(!empty($forum['moderators'])) {
					$forum['moderators'] = dunserialize($forum['moderators']);
				} else {
					require_once libfile('function/group');
					$forum['moderators'] = update_groupmoderators($fid);
				}
				if($_G['uid'] && $_G['adminid'] != 1) {
					$forum['ismoderator'] = !empty($forum['moderators'][$_G['uid']]) ? 1 : 0;
					$_G['adminid'] = 0;
					if($forum['ismoderator'] || $gorup_admingroupids[$_G['groupid']]) {
						$_G['adminid'] = $_G['adminid'] ? $_G['adminid'] : 3;
						if(!empty($gorup_admingroupids[$_G['groupid']])) {
							$forum['ismoderator'] = 1;
							$_G['adminid'] = 2;
						}

						$group_userperm = dunserialize($_G['setting']['group_userperm']);
						if(is_array($group_userperm)) {
							$_G['group'] = array_merge($_G['group'], $group_userperm);
							$_G['group']['allowmovethread'] = $_G['group']['allowcopythread'] = $_G['group']['allowedittypethread'] = 0;
						}
					}
				}
			}
			foreach(['threadtypes', 'threadsorts', 'creditspolicy', 'modrecommend'] as $key) {
				$forum[$key] = !empty($forum[$key]) ? dunserialize($forum[$key]) : [];
				if(!is_array($forum[$key])) {
					$forum[$key] = [];
				}
			}

			if(!empty($forum['threadtypes']['types'])) {
				safefilter($forum['threadtypes']['types']);
			}
			if(!empty($forum['threadtypes']['options']['name'])) {
				safefilter($forum['threadtypes']['options']['name']);
			}
			if(!empty($forum['threadsorts']['types'])) {
				safefilter($forum['threadsorts']['types']);
			}

			if($forum['status'] == 3) {
				$_G['isgroupuser'] = 0;
				$_G['basescript'] = 'group';
				if($forum['level'] == 0) {
					$levelinfo = table_forum_grouplevel::t()->fetch_by_credits($forum['commoncredits']);
					$levelid = $levelinfo['levelid'];
					$forum['level'] = $levelid;
					table_forum_forum::t()->update_group_level($levelid, $fid);
				}
				if($forum['level'] != -1) {
					loadcache('grouplevels');
					$grouplevel = $_G['grouplevels'][$forum['level']];
					if(!empty($grouplevel['icon'])) {
						$valueparse = parse_url($grouplevel['icon']);
						if(!isset($valueparse['host'])) {
							$grouplevel['icon'] = $_G['setting']['attachurl'].'common/'.$grouplevel['icon'];
						}
					}
				}

				$group_postpolicy = $grouplevel['postpolicy'];
				if(is_array($group_postpolicy)) {
					$forum = array_merge($forum, $group_postpolicy);
				}
				$forum['allowfeed'] = $_G['setting']['group_allowfeed'];
				if($_G['uid']) {
					if(!empty($forum['moderators'][$_G['uid']])) {
						$_G['isgroupuser'] = 1;
					} else {
						$groupuserinfo = table_forum_groupuser::t()->fetch_userinfo($_G['uid'], $fid);
						$_G['isgroupuser'] = $groupuserinfo['level'];
						if($_G['isgroupuser'] <= 0 && empty($forum['ismoderator'])) {
							$_G['group']['allowrecommend'] = $_G['cache']['usergroup_'.$_G['groupid']]['allowrecommend'] = 0;
							$_G['group']['allowcommentpost'] = $_G['cache']['usergroup_'.$_G['groupid']]['allowcommentpost'] = 0;
							$_G['group']['allowcommentitem'] = $_G['cache']['usergroup_'.$_G['groupid']]['allowcommentitem'] = 0;
							$_G['group']['raterange'] = $_G['cache']['usergroup_'.$_G['groupid']]['raterange'] = [];
							$_G['group']['allowvote'] = $_G['cache']['usergroup_'.$_G['groupid']]['allowvote'] = 0;
						} else {
							$_G['isgroupuser'] = 1;
						}
					}
				}
			}
			if(!empty($forum['fields'])) {
				$forum['fields'] = json_decode($forum['fields'], true);
			}
		} else {
			$fid = 0;
		}
	}

	$_G['fid'] = $fid;
	$_G['tid'] = $tid;
	$_G['forum'] = &$forum;
	$_G['current_grouplevel'] = &$grouplevel;

	if(empty($_G['uid'])) {
		$_G['group']['allowpostactivity'] = $_G['group']['allowpostpoll'] = $_G['group']['allowvote'] = $_G['group']['allowpostreward'] = $_G['group']['allowposttrade'] = $_G['group']['allowpostdebate'] = $_G['group']['allowpostrushreply'] = 0;
	}
	if(!empty($_G['forum']['widthauto'])) {
		$_G['widthauto'] = $_G['forum']['widthauto'];
	}
}

function get_thread_by_tid($tid, $forcetableid = null) {
	global $_G;

	$ret = [];
	if(!is_numeric($tid)) {
		return $ret;
	}
	loadcache('threadtableids');
	$threadtableids = [0];
	if(!empty($_G['cache']['threadtableids'])) {
		if($forcetableid === null || ($forcetableid > 0 && !in_array($forcetableid, $_G['cache']['threadtableids']))) {
			$threadtableids = array_merge($threadtableids, $_G['cache']['threadtableids']);
		} else {
			$threadtableids = [intval($forcetableid)];
		}
	}
	$threadtableids = array_unique($threadtableids);
	foreach($threadtableids as $tableid) {
		$tableid = $tableid > 0 ? $tableid : 0;
		$ret = table_forum_thread::t()->fetch_thread($tid, $tableid);
		if($ret) {
			$ret['threadtable'] = table_forum_thread::t()->get_table_name($tableid);
			$ret['threadtableid'] = $tableid;
			$ret['posttable'] = 'forum_post'.($ret['posttableid'] ? '_'.$ret['posttableid'] : '');
			break;
		}
	}

	if(!is_array($ret)) {
		$ret = [];
	} elseif(getglobal('setting/optimizeviews')) {
		if(($row = table_forum_threadaddviews::t()->fetch($tid))) {
			$ret['addviews'] = intval($row['addviews']);
			$ret['views'] += $ret['addviews'];
		}
	}

	return $ret;
}

function get_post_by_pid($pid, $fields = '*', $addcondiction = '', $forcetable = null) {
	global $_G;

	$ret = [];
	if(!is_numeric($pid)) {
		return $ret;
	}

	loadcache('posttable_info');

	$posttableids = [0];
	if($_G['cache']['posttable_info']) {
		if(isset($forcetable)) {
			if(is_numeric($forcetable) && array_key_exists($forcetable, $_G['cache']['posttable_info'])) {
				$posttableids[] = $forcetable;
			} elseif(str_starts_with($forcetable, 'forum_post')) {
				$posttableids[] = $forcetable;
			}
		} else {
			$posttableids = array_keys($_G['cache']['posttable_info']);
		}
	}

	foreach($posttableids as $id) {
		$table = empty($id) ? 'forum_post' : (is_numeric($id) ? 'forum_post_'.$id : $id);
		$ret = table_forum_post::t()->fetch_by_pid_condition($id, $pid, $addcondiction, $fields);
		if($ret) {
			$ret['posttable'] = $table;
			break;
		}
	}

	if(!is_array($ret)) {
		$ret = [];
	}

	return $ret;
}

function get_post_by_tid_pid($tid, $pid) {
	static $postlist = [];
	if(empty($postlist[$pid])) {
		$postlist[$pid] = table_forum_post::t()->fetch_post('tid:'.$tid, $pid, false);
		if($postlist[$pid] && $postlist[$pid]['tid'] == $tid) {
			$user = getuserbyuid($postlist[$pid]['authorid']);
			$postlist[$pid]['adminid'] = $user['adminid'];
		} else {
			$postlist[$pid] = [];
		}
	}
	return $postlist[$pid];
}

function set_rssauth() {
	global $_G;
	if($_G['setting']['rssstatus'] && $_G['uid']) {
		$auth = authcode($_G['uid']."\t".($_G['fid'] ? $_G['fid'] : '').
			"\t".substr(md5($_G['member']['password']), 0, 8), 'ENCODE', md5($_G['config']['security']['authkey']));
	} else {
		$auth = '0';
	}
	$_G['rssauth'] = rawurlencode($auth);
}

function rssforumperm($forum) {
	$is_allowed = $forum['type'] != 'group' && (!$forum['viewperm'] || ($forum['viewperm'] && forumperm($forum['viewperm'], 7)));
	return $is_allowed;
}

function upload_icon_banner(&$data, $file, $type) {
	global $_G;
	$data['extid'] = empty($data['extid']) ? $data['fid'] : $data['extid'];
	if(empty($data['extid'])) return '';

	if($data['status'] == 3 && $_G['setting']['group_imgsizelimit']) {
		$file['size'] > ($_G['setting']['group_imgsizelimit'] * 1024) && showmessage('file_size_overflow', '', ['size' => $_G['setting']['group_imgsizelimit'] * 1024]);
	}
	$upload = new discuz_upload();
	$uploadtype = $data['status'] == 3 ? 'group' : 'common';

	if(!$upload->init($file, $uploadtype, $data['extid'], $type)) {
		return false;
	}

	if(!$upload->save()) {
		if(!defined('IN_ADMINCP')) {
			showmessage($upload->errormessage());
		} else {
			cpmsg($upload->errormessage(), '', 'error');
		}
	}
	if($data['status'] == 3 && $type == 'icon') {
		require_once libfile('class/image');
		$img = new image;
		$img->Thumb($upload->attach['target'], './'.$uploadtype.'/'.$upload->attach['attachment'], 200, 200, 'fixwr');
	}
	if($_G['setting']['ftp']['on'] == 2) {
		ftpcmd('upload', $uploadtype.'/'.$upload->attach['attachment']);
	}
	return $upload->attach['attachment'];
}

function arch_multi($total, $perpage, $page, $link) {
	$pages = @ceil($total / $perpage) + 1;
	$pagelink = '';
	if($pages > 1) {
		$pagelink .= lang('forum/archiver', 'page').": \n";
		$pagestart = $page - 10 < 1 ? 1 : $page - 10;
		$pageend = $page + 10 >= $pages ? $pages : $page + 10;
		for($i = $pagestart; $i < $pageend; $i++) {
			$pagelink .= ($i == $page ? "<strong>[$i]</strong>" : "<a href=\"$link&page=$i\">$i</a>")." \n";
		}
	}
	return $pagelink;
}

function loadarchiver($path) {
	global $_G;
	if(!$_G['setting']['archiver']) {
		require_once childfile('header', 'forum/archiver');
		echo '<div id="content">'.lang('message', 'forum_archiver_disabled').'</div>';
		require_once libfile('footer', 'forum/archiver');
		exit;
	}
	return childfile($path, 'forum/archiver');
}

function update_threadpartake($tid, $getsetarr = false) {
	global $_G;
	$setarr = [];
	if($_G['uid'] && $tid) {
		if($_G['setting']['heatthread']['period']) {
			$partaked = table_forum_threadpartake::t()->fetch_threadpartake($tid, $_G['uid']);
			$partaked = $partaked['uid'];
			if(!$partaked) {
				table_forum_threadpartake::t()->insert(['tid' => $tid, 'uid' => $_G['uid'], 'dateline' => TIMESTAMP]);
				$setarr = table_forum_thread::t()->increase($tid, ['heats' => 1], false, 0, $getsetarr);
			}
		} else {
			$setarr = table_forum_thread::t()->increase($tid, ['heats' => 1], false, 0, $getsetarr);

		}
	}
	if($getsetarr) {
		return $setarr;
	}
}

function getthreadcover($tid, $cover = 0, $getfilename = 0) {
	global $_G;
	if(empty($tid)) {
		return '';
	}
	$coverpath = '';
	$covername = 'threadcover/'.substr(md5($tid), 0, 2).'/'.substr(md5($tid), 2, 2).'/'.$tid.'.jpg';
	if($getfilename) {
		return $covername;
	}
	if($cover) {
		$coverpath = ($cover < 0 ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$covername;
	}
	return $coverpath;
}

function convertunusedattach($aid, $tid, $pid) {
	if(!$aid) {
		return;
	}
	global $_G;
	$attach = table_forum_attachment_n::t()->fetch_by_aid_uid(127, $aid, $_G['uid']);
	if(!$attach) {
		return;
	}
	if($_G['setting']['ftp']['on'] == 2) {
		ftpcmd('upload', 'forum/'.$attach['attachment']);
		@unlink($_G['setting']['attachdir'].'/forum/'.$attach['attachment']);
		if($attach['thumb']) {
			ftpcmd('upload', 'forum/'.getimgthumbname($attach['attachment']));
			@unlink($_G['setting']['attachdir'].'/forum/'.getimgthumbname($attach['attachment']));
		}
	}
	$attach = daddslashes($attach);
	$attach['tid'] = $tid;
	$attach['pid'] = $pid;
	table_forum_attachment_n::t()->insert_attachment('tid:'.$tid, $attach);
	table_forum_attachment::t()->update($attach['aid'], ['tid' => $tid, 'pid' => $pid, 'tableid' => getattachtableid($tid)]);
	table_forum_attachment_unused::t()->delete($attach['aid']);
}

function updateattachtid($idtype, $ids, $oldtid, $newtid) {
	foreach(table_forum_attachment_n::t()->fetch_all_by_id('tid:'.$oldtid, $idtype, $ids) as $attach) {
		$attach['tid'] = $newtid;
		table_forum_attachment_n::t()->insert_attachment('tid:'.$newtid, $attach, false, true);
	}
	table_forum_attachment_n::t()->delete_by_id('tid:'.$oldtid, $idtype, $ids);
	table_forum_attachment::t()->update_by_id($idtype, $ids, $newtid);
}

function updatepost($data, $condition, $unbuffered = false, $posttableid = false) {
	return false;
}

function insertpost($data) {
	if(isset($data['tid'])) {
		$thread = table_forum_thread::t()->fetch_thread($data['tid']);
		$tableid = $thread['posttableid'];
		
		if(!$data['first'] && $thread['replies'] <= 0 && table_forum_sofa::t()->fetch($thread['tid'])) {
			table_forum_sofa::t()->delete($thread['tid']);
		}
	} else {
		$tableid = $data['tid'] = 0;
	}
	$pid = table_forum_post_tableid::t()->insert(['pid' => null], true);


	$data = array_merge($data, ['pid' => $pid]);

	table_forum_post::t()->insert_post($tableid, $data);
	if($pid % 1024 == 0) {
		table_forum_post_tableid::t()->delete_by_lesspid($pid);
	}
	savecache('max_post_id', $pid);
	return $pid;
}

function threadmodstatus($string) {
	global $_G;
	$postmodperiods = periodscheck('postmodperiods', 0);
	if($postmodperiods) {
		$modnewthreads = $modnewreplies = 1;
	} else {
		$censormod = censormod($string);
		$modnewthreads = (!$_G['group']['allowdirectpost'] || $_G['group']['allowdirectpost'] == 1) && $_G['forum']['modnewposts'] || $censormod ? 1 : 0;
		$modnewreplies = (!$_G['group']['allowdirectpost'] || $_G['group']['allowdirectpost'] == 2) && $_G['forum']['modnewposts'] == 2 || $censormod ? 1 : 0;

		if($_G['forum']['status'] == 3) {
			$modnewthreads = !$_G['group']['allowgroupdirectpost'] || $_G['group']['allowgroupdirectpost'] == 1 || $censormod ? 1 : 0;
			$modnewreplies = !$_G['group']['allowgroupdirectpost'] || $_G['group']['allowgroupdirectpost'] == 2 || $censormod ? 1 : 0;
		}
	}

	$_G['group']['allowposturl'] = $_G['forum']['status'] != 3 ? $_G['group']['allowposturl'] : $_G['group']['allowgroupposturl'];
	if($_G['group']['allowposturl'] == 1) {
		if(!$postmodperiods) {
			$censormod = censormod($string);
		}
		if($censormod) {
			$modnewthreads = $modnewreplies = 1;
		}
	}
	return [$modnewthreads, $modnewreplies];
}

function threadpubsave($tid, $passapproval = false) {
	global $_G;
	if($_G['setting']['plugins']['func'][HOOKTYPE]['threadpubsave']) {
		$hookparam = func_get_args();
		hookscript('threadpubsave', 'global', 'funcs', ['param' => $hookparam, 'step' => 'check'], 'threadpubsave');
	}
	$thread = table_forum_thread::t()->fetch_by_tid_displayorder($tid, -4, '=', !$passapproval ? $_G['uid'] : null);
	if(!$thread) {
		return 0;
	}
	$threadpost = table_forum_post::t()->fetch_threadpost_by_tid_invisible($tid);
	$thread['message'] = $threadpost['message'];

	$modworksql = 0;
	$displayorder = 0;
	$dateline = $_G['timestamp'];
	$moderatepids = $saveposts = [];
	$return = 1;

	list($modnewthreads) = threadmodstatus($thread['subject']."\t".$thread['message']);
	if($modnewthreads && $passapproval === false) {
		updatemoderate('tid', $tid);
		manage_addnotify('verifythread');
		$displayorder = -2;
		$modworksql = 1;
		$return = -1;
		table_forum_post::t()->update_by_tid('tid:'.$tid, $tid, ['dateline' => $dateline], false, false, 1);
	} else {
		table_forum_post::t()->update_by_tid('tid:'.$tid, $tid, ['dateline' => $dateline, 'invisible' => '0'], false, false, 1);
	}

	table_forum_thread::t()->update($tid, ['displayorder' => $displayorder, 'dateline' => $_G['timestamp'], 'lastpost' => $_G['timestamp']]);
	$posts = $thread['replies'] + 1;
	if($thread['replies']) {
		$saveposts = table_forum_post::t()->fetch_all_by_tid('tid:'.$tid, $tid, true, '', 0, 0, 0);
		foreach($saveposts as $post) {
			$dateline++;
			$invisible = 0;
			list(, $modnewreplies) = threadmodstatus($post['subject']."\t".$post['message']);
			if($modnewreplies) {
				$moderatepids[] = $post['pid'];
				$verifypost = true;
				$invisible = -2;
				$modworksql = 1;
				$return = -2;
			}
			table_forum_post::t()->update_post('tid:'.$tid, $post['pid'], ['dateline' => $dateline, 'invisible' => $invisible]);
			updatepostcredits('+', $thread['authorid'], 'reply', $thread['fid']);
		}
	}
	if($moderatepids) {
		updatemoderate('pid', $moderatepids);
		manage_addnotify('verifypost');
	}
	updatepostcredits('+', $thread['authorid'], 'post', $thread['fid']);
	$attachcount = table_forum_attachment_n::t()->count_by_id('tid:'.$thread['tid'], 'tid', $thread['tid']);
	updatecreditbyaction('postattach', $thread['authorid'], [], '', $attachcount, 1, $thread['fid']);
	if($_G['forum']['status'] == 3) {
		table_forum_groupuser::t()->update_counter_for_user($thread['authorid'], $thread['fid'], 1);
	}

	$subject = str_replace("\t", ' ', $thread['subject']);
	$subject = cutstr($subject, 80);
	$lastpost = $thread['tid']."\t".$subject."\t".$thread['lastpost']."\t".$thread['lastposter'];
	table_forum_forum::t()->update($_G['fid'], ['lastpost' => $lastpost]);
	table_forum_forum::t()->update_forum_counter($thread['fid'], 1, $posts, $posts, $modworksql);
	if($_G['forum']['type'] == 'sub') {
		table_forum_forum::t()->update($_G['forum']['fup'], ['lastpost' => $lastpost]);
	}
	if($_G['setting']['plugins']['func'][HOOKTYPE]['threadpubsave']) {
		hookscript('threadpubsave', 'global', 'funcs', ['param' => $hookparam, 'step' => 'save', 'posts' => $saveposts], 'threadpubsave');
	}
	return $return;
}

function getrelatecollection($tid, $all = false, &$num = null, &$more = null) {
	global $_G;

	$maxdisplay = $_G['setting']['collectionnum'];
	if(!$maxdisplay) return;

	$tidrelate = table_forum_collectionrelated::t()->fetch($tid);
	$ctids = explode("\t", $tidrelate['collection'], -1);
	$num = count($ctids);

	if(!$ctids || !$num) {
		$more = $num = 0;
		return null;
	}
	if($all !== true && $num > $maxdisplay) {
		$more = 1;
	} else {
		$maxdisplay = 0;
	}
	return table_forum_collection::t()->fetch_all($ctids, 'follownum', 'DESC', 0, $maxdisplay, '', $tid);
}

function set_atlist_cookie($uids) {
	global $_G;
	$atlist = $tmp = [];
	$num = 0;
	$maxlist = 10;
	if(empty($uids)) {
		return;
	}
	$newnum = count($uids);
	if($newnum >= $maxlist) {
		$uids = array_slice($uids, 0, $maxlist);
		dsetcookie('atlist', implode(',', $uids), 86400 * 360);
		return;
	}
	if($_G['cookie']['atlist']) {
		$atlist = explode(',', $_G['cookie']['atlist']);
		foreach($atlist as $key => $val) {
			if(!in_array($val, $uids)) {
				$num++;
				if($num == ($maxlist - $newnum)) {
					break;
				}
				$tmp[$key] = $val;
			}
		}
	}
	dsetcookie('atlist', implode(',', $uids).($tmp ? ','.implode(',', $tmp) : ''), 86400 * 360);
}

function viewthread_is_search_referer() {
	$regex = '((http|https)\:\/\/)?';
	$regex .= '([a-z]*.)?(toutiao.com|m.sm.cn|www.so.com|sogou.com|bing.com|baidu.com|google.com|google.cn|google.com.hk)(.[a-z]{2,3})?\/';
	if(preg_match("/^$regex/", $_SERVER['HTTP_REFERER'])) {
		return true;
	}
	return false;
}


function stringtopic($value, $key = '', $force = false, $rlength = 0) {
	if($key === '') {
		$key = $value;
	}
	$basedir = !getglobal('setting/attachdir') ? './data/attachment' : getglobal('setting/attachdir');
	$url = !getglobal('setting/attachurl') ? './data/attachment/' : getglobal('setting/attachurl');
	$subdir1 = substr(md5($key), 0, 2);
	$subdir2 = substr(md5($key), 2, 2);
	$target = 'temp/'.$subdir1.'/'.$subdir2.'/';
	$targetname = substr(md5($key), 8, 16).'.png';
	discuz_upload::check_dir_exists('temp', $subdir1, $subdir2);
	if(!$force && file_exists($basedir.'/'.$target.$targetname)) {
		return $url.$target.$targetname;
	}
	$value = str_replace("\n", '', $value);
	$fontfile = $fontname = '';
	$ttfenabled = false;
	$size = 10;
	$w = 130;
	$rowh = 25;
	$value = explode("\r", $value);
	if($rlength) {
		$temp = [];
		foreach($value as $str) {
			$strlen = dstrlen($str);
			if($strlen > $rlength) {
				for($i = 0; $i < $strlen; $i++) {
					$sub = cutstr($str, $rlength, '');
					$temp[] = $sub;
					$str = substr($str, strlen($sub));
					$strlen = $strlen - $rlength;
				}
			} else {
				$temp[] = $str;
			}
		}
		$value = $temp;
		unset($temp);
	}
	if(function_exists('imagettftext')) {
		$fontroot = DISCUZ_ROOT.'./source/data/seccode/font/ch/';
		$dirs = opendir($fontroot);
		while($entry = readdir($dirs)) {
			if($entry != '.' && $entry != '..' && in_array(strtolower(fileext($entry)), ['ttf', 'ttc'])) {
				$fontname = $entry;
				break;
			}
		}
		if(!empty($fontname)) {
			$fontfile = DISCUZ_ROOT.'./source/data/seccode/font/ch/'.$fontname;
		}
		if($fontfile) {
			if(strtoupper(CHARSET) != 'UTF-8') {
				include DISCUZ_ROOT.'./source/class/class_chinese.php';
				$cvt = new Chinese(CHARSET, 'utf8');
				$value = $cvt->Convert(implode("\r", $value));
				$value = explode("\r", $value);
			}
			$ttfenabled = true;
		}
	}

	foreach($value as $str) {
		if($ttfenabled) {
			$box = imagettfbbox($size, 0, $fontfile, $str);
			$height = max($box[1], $box[3]) - min($box[5], $box[7]);
			$len = (max($box[2], $box[4]) - min($box[0], $box[6]));
			$rowh = max([$height, $rowh]);
		} else {
			$len = strlen($str) * 12;
		}
		$w = max([$len, $w]);
	}
	$h = $rowh * count($value) + count($value) * 2;
	$im = @imagecreate($w, $h);
	$background_color = imagecolorallocate($im, 255, 255, 255);
	$text_color = imagecolorallocate($im, 60, 60, 60);
	$h = $ttfenabled ? $rowh : 4;
	foreach($value as $str) {
		if($ttfenabled) {
			imagettftext($im, $size, 0, 0, $h, $text_color, $fontfile, $str);
			$h += 2;
		} else {
			imagestring($im, $size, 0, $h, $str, $text_color);
		}
		$h += $rowh;
	}
	imagepng($im, $basedir.'/'.$target.$targetname);
	imagedestroy($im);
	return $url.$target.$targetname;
}

function getreplybg($replybg = '') {
	global $_G;
	$style = '';
	if(getglobal('setting/allowreplybg')) {
		if($replybg) {
			$bgurl = $replybg;
			if(file_exists($_G['setting']['attachurl'].'common/'.$replybg)) {
				$bgurl = $_G['setting']['attachurl'].'common/'.$replybg;
			}
		} elseif($_G['setting']['globalreplybg']) {
			$bgurl = $_G['setting']['globalreplybg'];
			if(file_exists($_G['setting']['attachurl'].'common/'.$_G['setting']['globalreplybg'])) {
				$bgurl = $_G['setting']['attachurl'].'common/'.$_G['setting']['globalreplybg'];
			}
		}
		if($bgurl) {
			$style = ' style="background-image: url('.$bgurl.');"';
		}
	}
	return $style;
}

function safefilter(&$data) {
	if(is_array($data)) {
		foreach($data as $k => $v) {
			safefilter($data[$k]);
		}
	} else {
		$data = str_replace([
			'[/color]', '[b]', '[/b]', '[s]', '[/s]', '[i]', '[/i]', '[u]', '[/u]',
		], [
			'</font>', '<b>', '</b>', '<strike>', '</strike>', '<i>', '</i>', '<u>', '</u>'
		], preg_replace([
			'/\[color=([#\w]+?)\]/i',
			'/\[color=((rgb|rgba)\([\d\s\.,]+?\))\]/i',
		], [
			"<font color=\"\\1\">",
			"<font style=\"color:\\1\">",
		], strip_tags($data)));
	}
}

