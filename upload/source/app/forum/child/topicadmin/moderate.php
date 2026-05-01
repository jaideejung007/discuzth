<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!empty($_G['tid'])) {
	$_GET['moderate'] = [$_G['tid']];
}

$allow_operation = ['delete', 'highlight', 'open', 'close', 'stick', 'digest', 'bump', 'down', 'recommend', 'type', 'move', 'recommend_group'];

$operations = empty($_GET['operations']) ? [] : $_GET['operations'];
if($operations && $operations != array_intersect($operations, $allow_operation) || (!$_G['group']['allowdelpost'] && in_array('delete', $operations)) || (!$_G['group']['allowstickthread'] && in_array('stick', $operations))) {
	showmessage('admin_moderate_invalid');
}

$threadlist = $loglist = $posttablearr = $authors = [];
$crimenum = $crimeauthor = '';
$recommend_group_count = 0;
$operation = getgpc('operation');
loadcache('threadtableids');
$threadtableids = !empty($_G['cache']['threadtableids']) ? $_G['cache']['threadtableids'] : [];
if(!in_array(0, $threadtableids)) {
	$threadtableids = array_merge([0], $threadtableids);
}

if($_GET['moderate']) {
	foreach($threadtableids as $tableid) {
		foreach(table_forum_thread::t()->fetch_all_by_tid_fid_displayorder($_GET['moderate'], $_G['fid'], null, '', 0, $_G['tpp'], '', '', $tableid) as $thread) {
			if($thread['closed'] > 1 && $operation && !in_array($operation, ['delete', 'highlight', 'stick', 'digest', 'bump', 'down']) || $thread['displayorder'] < 0 && $thread['displayorder'] != -4) {
				if($operation == 'recommend_group') {
					$recommend_group_count++;
				}
				continue;
			}
			$thread['lastposterenc'] = rawurlencode($thread['lastposter']);
			$thread['dblastpost'] = $thread['lastpost'];
			$thread['lastpost'] = dgmdate($thread['lastpost'], 'u');
			$posttablearr[$thread['posttableid'] ? $thread['posttableid'] : 0][] = $thread['tid'];
			$authors[$thread['authorid']] = 1;
			$threadlist[$thread['tid']] = $thread;
			$_G['tid'] = empty($_G['tid']) ? $thread['tid'] : $_G['tid'];
		}
		if(!empty($threadlist)) {
			break;
		}
	}
}
if(empty($threadlist)) {
	if($recommend_group_count) {
		showmessage('recommend_group_invalid');
	}
	showmessage('admin_moderate_invalid');
}

$authorcount = count(array_keys($authors));
$modpostsnum = count($threadlist);
$single = $modpostsnum == 1;
$frommodcp = getgpc('frommodcp');
switch($frommodcp) {
	case '1':
		$_G['referer'] = "forum.php?mod=modcp&action=thread&fid={$_G['fid']}&op=thread&do=list";
		break;
	case '2':
		$_G['referer'] = 'forum.php?mod=modcp&action=forum&op=recommend'.(getgpc('show') ? "&show=getgpc('show')" : '')."&fid={$_G['fid']}";
		break;
	default:
		if(in_array('delete', $operations) || in_array('move', $operations) && !strpos($_SERVER['HTTP_REFERER'], 'search.php?mod=forum')) {
			$_G['referer'] = 'forum.php?mod=forumdisplay&fid='.$_G['fid'].(!empty($_GET['listextra']) ? '&'.rawurldecode($_GET['listextra']) : '');
		} else {
			$_G['referer'] = $_GET['redirect'];
		}
		break;
}

$optgroup = $_GET['optgroup'] = isset($_GET['optgroup']) ? intval($_GET['optgroup']) : 0;
$expirationstick = getgpc('expirationstick');

$defaultcheck = [];
foreach($allow_operation as $v) {
	$defaultcheck[$v] = '';
}
$defaultcheck[$operation] = 'checked="checked"';

if(!submitcheck('modsubmit')) {

	$stickcheck = $closecheck = $digestcheck = ['', '', '', '', ''];
	$expirationdigest = $expirationhighlight = $expirationclose = '';

	if($_GET['optgroup'] == 1 && $single) {
		empty($threadlist[$_G['tid']]['displayorder']) ? $stickcheck[0] = 'selected="selected"' : $stickcheck[$threadlist[$_G['tid']]['displayorder']] = 'selected="selected"';
		empty($threadlist[$_G['tid']]['digest']) ? $digestcheck[0] = 'selected="selected"' : $digestcheck[$threadlist[$_G['tid']]['digest']] = 'selected="selected"';
		$string = sprintf('%02d', $threadlist[$_G['tid']]['highlight']);
		$stylestr = sprintf('%03b', $string[0]);
		for($i = 1; $i <= 3; $i++) {
			$stylecheck[$i] = $stylestr[$i - 1] ? 1 : 0;
		}
		$colorcheck = $string[1];
		$highlight_bgcolor = $threadlist[$_G['tid']]['bgcolor'];
		$_G['forum']['modrecommend'] = is_array($_G['forum']['modrecommend']) ? $_G['forum']['modrecommend'] : [];
		$expirationstick = get_expiration($_G['tid'], 'EST');
		$expirationdigest = get_expiration($_G['tid'], 'EDI');
		$expirationhighlight = get_expiration($_G['tid'], 'EHL');

	} elseif($_GET['optgroup'] == 2 || $_GET['optgroup'] == 5) {
		require_once libfile('function/forumlist');
		$forumselect = forumselect(FALSE, 0, $threadlist[$_G['tid']]['fid'], $_G['adminid'] == 1);
		$typeselect = typeselect($single ? $threadlist[$_G['tid']]['typeid'] : 0);
	} elseif($_GET['optgroup'] == 4 && $single) {
		!empty($threadlist[$_G['tid']]['closed']) ? $closecheck[0] = 'checked="checked"' : $closecheck[1] = 'checked="checked"';
		if($threadlist[$_G['tid']]['closed']) {
			$expirationclose = get_expiration($_G['tid'], 'ECL');
		}
	} elseif($_GET['optgroup'] == 3 && ($modpostsnum == 1 || $authorcount == 1)) {
		include_once libfile('function/member');
		$crimenum = crime('getcount', $threadlist[$_G['tid']]['authorid'], 'crime_delpost');
		$crimeauthor = $threadlist[$_G['tid']]['author'];
	}

	$imgattach = [];
	if(count($threadlist) == 1 && $operation == 'recommend') {
		$imgattach = table_forum_attachment_n::t()->fetch_all_by_id('tid:'.$_G['tid'], 'tid', $_G['tid'], '', [1, -1]);

		$oldthread = table_forum_forumrecommend::t()->fetch($_G['tid']);
		if($oldthread) {
			$threadlist[$_G['tid']]['subject'] = $oldthread['subject'];
			$selectposition[$oldthread['position']] = ' selected="selected"';
			$selectattach = $oldthread['aid'];
		} else {
			$keys = array_keys($imgattach);
			$selectattach = $imgattach[$keys[0]]['aid'];
			$selectposition[0] = ' selected="selected"';
		}
		$expirationrecommend = get_expiration($_G['tid'], 'REC');
	}
	include template('forum/topicadmin');

} else {

	$tidsarr = array_keys($threadlist);
	$moderatetids = dimplode($tidsarr);
	$reason = checkreasonpm();
	$stampaction = 'SPA';
	if(empty($operations)) {
		showmessage('admin_nonexistence');
	} else {
		$images = [];
		foreach($operations as $operation) {

			$stampstatus = 0;
			$updatemodlog = TRUE;
			if($operation == 'stick') {
				$sticklevel = intval($_GET['sticklevel']);
				if($sticklevel < 0 || $sticklevel > 3 || $sticklevel > $_G['group']['allowstickthread']) {
					showmessage('no_privilege_stickthread');
				}
				$expiration = checkexpiration($_GET['expirationstick'], $operation);
				$expirationstick = $sticklevel ? $_GET['expirationstick'] : 0;

				$forumstickthreads = $_G['setting']['forumstickthreads'];
				$forumstickthreads = isset($forumstickthreads) ? dunserialize($forumstickthreads) : [];
				table_forum_thread::t()->update($tidsarr, ['displayorder' => $sticklevel, 'moderated' => 1], true);
				$delkeys = array_keys($threadlist);
				foreach($delkeys as $k) {
					unset($forumstickthreads[$k]);
				}
				table_common_setting::t()->update_setting('forumstickthreads', $forumstickthreads);

				$stickmodify = 0;
				foreach($threadlist as $thread) {
					$stickmodify = (in_array($thread['displayorder'], [2, 3]) || in_array($sticklevel, [2, 3])) && $sticklevel != $thread['displayorder'] ? 1 : $stickmodify;
					table_common_member_secwhite::t()->add($thread['authorid']);
				}

				if($_G['setting']['globalstick'] && $stickmodify) {
					require_once libfile('function/cache');
					updatecache('globalstick');
				}

				$modaction = $sticklevel ? ($expiration ? 'EST' : 'STK') : 'UST';
				table_forum_threadmod::t()->update_by_tid_action($tidsarr, ['STK', 'UST', 'EST', 'UES'], ['status' => 0]);
				table_forum_threadhidelog::t()->delete_by_tid($tidsarr);

				if(!$sticklevel) {
					$stampaction = 'SPD';
				}

				$stampstatus = 1;

			} elseif($operation == 'highlight') {
				if(!$_G['group']['allowhighlightthread']) {
					showmessage('no_privilege_highlightthread');
				}
				$highlight_style = $_GET['highlight_style'];
				$highlight_color = $_GET['highlight_color'];
				$highlight_bgcolor = $_GET['highlight_bgcolor'];
				$expiration = checkexpiration($_GET['expirationhighlight'], $operation);
				$stylebin = '';
				for($i = 1; $i <= 3; $i++) {
					$stylebin .= empty($highlight_style[$i]) ? '0' : '1';
				}

				$highlight_style = bindec($stylebin);
				if($highlight_style < 0 || $highlight_style > 7 || $highlight_color < 0 || $highlight_color > 8) {
					showmessage('parameters_error');
				}
				$bgcolor = dhtmlspecialchars(preg_replace('/[^\[A-Za-z0-9#]/', '', $_GET['highlight_bgcolor']));

				table_forum_thread::t()->update($tidsarr, ['highlight' => $highlight_style.$highlight_color, 'moderated' => 1, 'bgcolor' => $bgcolor], true);
				table_forum_forumrecommend::t()->update($tidsarr, ['highlight' => $highlight_style.$highlight_color]);
				table_forum_threadhidelog::t()->delete_by_tid($tidsarr);

				$modaction = ($highlight_style + $highlight_color) ? ($expiration ? 'EHL' : 'HLT') : 'UHL';
				$expiration = $modaction == 'UHL' ? 0 : $expiration;

				foreach($threadlist as $thread) {
					table_common_member_secwhite::t()->add($thread['authorid']);
				}

				table_forum_threadmod::t()->update_by_tid_action($tidsarr, ['HLT', 'UHL', 'EHL', 'UEH'], ['status' => 0]);

			} elseif($operation == 'digest') {
				$digestlevel = intval($_GET['digestlevel']);
				if($digestlevel < 0 || $digestlevel > 3 || $digestlevel > $_G['group']['allowdigestthread']) {
					showmessage('no_privilege_digestthread');
				}
				$expiration = checkexpiration($_GET['expirationdigest'], $operation);
				$expirationdigest = $digestlevel ? $expirationdigest : 0;

				table_forum_thread::t()->update($tidsarr, ['digest' => $digestlevel, 'moderated' => 1], true);
				table_forum_threadhidelog::t()->delete_by_tid($tidsarr);

				foreach($threadlist as $thread) {
					if($thread['digest'] != $digestlevel) {
						if($digestlevel == $thread['digest']) continue;
						$extsql = [];
						if($digestlevel > 0 && $thread['digest'] == 0) {
							$extsql = ['digestposts' => 1];
						}
						if($digestlevel == 0 && $thread['digest'] > 0) {
							$extsql = ['digestposts' => -1];
						}
						if($digestlevel == 0) {
							$stampaction = 'SPD';
						}
						updatecreditbyaction('digest', $thread['authorid'], $extsql, '', $digestlevel - $thread['digest']);
						table_common_member_secwhite::t()->add($thread['authorid']);
					}
				}

				$modaction = $digestlevel ? ($expiration ? 'EDI' : 'DIG') : 'UDG';
				table_forum_threadmod::t()->update_by_tid_action($tidsarr, ['DIG', 'UDI', 'EDI', 'UED'], ['status' => 0]);

				$stampstatus = 2;

			} elseif($operation == 'recommend') {
				if(!$_G['group']['allowrecommendthread']) {
					showmessage('no_privilege_recommendthread');
				}
				$isrecommend = $_GET['isrecommend'];
				$modrecommend = !empty($_G['forum']['modrecommend']) ? $_G['forum']['modrecommend'] : [];
				$imgw = $modrecommend['imagewidth'] ? intval($modrecommend['imagewidth']) : 200;
				$imgh = $modrecommend['imageheight'] ? intval($modrecommend['imageheight']) : 150;
				$expiration = checkexpiration($_GET['expirationrecommend'], $operation);
				table_forum_thread::t()->update($tidsarr, ['moderated' => 1], true);
				$modaction = $isrecommend ? 'REC' : 'URE';
				$thread = daddslashes($thread, 1);
				$selectattach = $_GET['selectattach'];
				$position = $_GET['position'];

				table_forum_threadmod::t()->update_by_tid_action($tidsarr, ['REC'], ['status' => 0]);
				if($isrecommend) {
					table_forum_threadhidelog::t()->delete_by_tid($tidsarr);
					$oldrecommendlist = $addthread = [];
					foreach(table_forum_forumrecommend::t()->fetch_all($tidsarr) as $row) {
						$oldrecommendlist[$row['tid']] = $row;
					}
					foreach($threadlist as $thread) {
						if(count($threadlist) > 1) {
							if($oldrecommendlist[$thread['tid']]) {
								$oldthread = $oldrecommendlist[$thread['tid']];
								$reducetitle = $oldthread['subject'];
								$selectattach = $oldthread['aid'];
								$typeid = $oldthread['typeid'];
								$position = $oldthread['position'];
							} else {
								$reducetitle = $thread['subject'];
								$typeid = 0;
								$position = 0;
							}
						} else {
							if(empty($_GET['reducetitle'])) {
								$reducetitle = $thread['subject'];
							} else {
								$reducetitle = $_GET['reducetitle'];
							}
							$typeid = $selectattach ? 1 : 0;
							empty($_GET['position']) && $position = 0;
						}
						if($selectattach) {
							$key = md5($selectattach.'|'.$imgw.'|'.$imgh);
							$filename = $selectattach."\t".$imgw."\t".$imgh."\t".$key;
						} else {
							$selectattach = 0;
							$filename = '';
						}

						$addthread[] = [
							'fid' => $thread['fid'],
							'tid' => $thread['tid'],
							'typeid' => $typeid,
							'displayorder' => 0,
							'subject' => $reducetitle,
							'author' => $thread['author'],
							'authorid' => $thread['authorid'],
							'moderatorid' => $_G['uid'],
							'expiration' => $expiration,
							'position' => $position,
							'aid' => $selectattach,
							'filename' => $filename,
							'highlight' => $thread['highlight']
						];

						$reducetitle = '';
					}
					if($addthread) {
						foreach($addthread as $row) {
							table_forum_forumrecommend::t()->insert($row, false, true);
						}
					}

				} else {
					table_forum_forumrecommend::t()->delete($tidsarr);
					$stampaction = 'SPD';
				}
				$stampstatus = 3;

			} elseif($operation == 'bump') {
				if(!$_G['group']['allowbumpthread']) {
					showmessage('no_privilege_bumpthread');
				}
				$modaction = 'BMP';
				$thread = $threadlist;
				$thread = array_pop($thread);

				$expiration = checkexpiration($_GET['expirationbump'], $operation);
				if(!$expiration) {
					$expiration = $_G['timestamp'];
				}


				table_forum_thread::t()->update($tidsarr, ['lastpost' => $expiration, 'moderated' => 1], true);
				table_forum_forum::t()->update($_G['fid'], ['lastpost' => "{$thread['tid']}\t{$thread['subject']}\t$expiration\t{$thread['lastposter']}"]);

				$_G['forum']['threadcaches'] && deletethreadcaches($moderatetids);
			} elseif($operation == 'down') {
				if(!$_G['group']['allowbumpthread']) {
					showmessage('no_privilege_downthread');
				}
				$modaction = 'DWN';
				$downtime = TIMESTAMP - 86400 * 730;
				table_forum_thread::t()->update($tidsarr, ['lastpost' => $downtime, 'moderated' => 1], true);

				$_G['forum']['threadcaches'] && deletethreadcaches($moderatetids);
			} elseif($operation == 'delete') {
				if(!$_G['group']['allowdelpost']) {
					showmessage('no_privilege_delpost');
				}
				loadcache('threadtableids');
				$stickmodify = 0;
				$deleteredirect = $remarkclosed = [];
				foreach($threadlist as $thread) {
					if($thread['digest']) {
						updatecreditbyaction('digest', $thread['authorid'], ['digestposts' => -1], '', -$thread['digest']);
					}
					if(in_array($thread['displayorder'], [2, 3])) {
						$stickmodify = 1;
					}
					if($_G['forum']['status'] == 3 && $thread['closed'] > 1) {
						$deleteredirect[] = $thread['closed'];
					}
					if($thread['isgroup'] == 1 && $thread['closed'] > 1) {
						$remarkclosed[] = $thread['closed'];
					}
				}

				$modaction = 'DEL';
				require_once libfile('function/delete');
				$tids = array_keys($threadlist);
				if($_G['forum']['recyclebin']) {

					deletethread($tids, true, true, true);
					manage_addnotify('verifyrecycle', $modpostsnum);
				} else {

					deletethread($tids, true, true);
					$updatemodlog = FALSE;
				}

				if($_G['group']['allowbanuser'] && ($_GET['banuser'] || $_GET['userdelpost']) && $_G['deleteauthorids']) {
					$members = table_common_member::t()->fetch_all($_G['deleteauthorids']);
					$banuins = [];
					foreach($members as $member) {
						if(($_G['cache']['usergroups'][$member['groupid']]['type'] == 'system' &&
								in_array($member['groupid'], [1, 2, 3, 6, 7, 8])) || $_G['cache']['usergroups'][$member['groupid']]['type'] == 'special') {
							continue;
						}
						$banuins[$member['uid']] = $member['uid'];
					}
					if($banuins) {
						if($_GET['banuser']) {
							table_common_member::t()->update($banuins, ['groupid' => 4]);
						}

						if($_GET['userdelpost']) {
							deletememberpost($banuins);
						}
					}
				}

				$forumstickthreads = $_G['setting']['forumstickthreads'];
				$forumstickthreads = !empty($forumstickthreads) ? dunserialize($forumstickthreads) : [];
				$delkeys = array_keys($threadlist);
				foreach($delkeys as $k) {
					unset($forumstickthreads[$k]);
				}
				table_common_setting::t()->update_setting('forumstickthreads', $forumstickthreads);

				table_forum_forum_threadtable::t()->delete_none_threads();
				if(!empty($deleteredirect)) {
					deletethread($deleteredirect);
				}
				if(!empty($remarkclosed)) {
					table_forum_thread::t()->update($remarkclosed, ['closed' => 0]);
				}

				if($_G['setting']['globalstick'] && $stickmodify) {
					require_once libfile('function/cache');
					updatecache('globalstick');
				}

				updateforumcount($_G['fid']);

				if($_GET['crimerecord']) {
					include_once libfile('function/member');
					foreach($threadlist as $thread) {
						crime('recordaction', $thread['authorid'], 'crime_delpost', lang('forum/misc', 'crime_postreason', ['reason' => $reason, 'tid' => $thread['tid'], 'pid' => 0]));
					}
				}
			} elseif($operation == 'close') {
				if(!$_G['group']['allowclosethread']) {
					showmessage('no_privilege_closethread');
				}
				$expiration = checkexpiration($_GET['expirationclose'], $operation);
				$modaction = $expiration ? 'ECL' : 'CLS';

				table_forum_thread::t()->update($tidsarr, ['closed' => 1, 'moderated' => 1], true);
				table_forum_threadmod::t()->update_by_tid_action($tidsarr, ['CLS', 'OPN', 'ECL', 'UCL', 'EOP', 'UEO'], ['status' => 0]);
			} elseif($operation == 'open') {
				if(!$_G['group']['allowclosethread']) {
					showmessage('no_privilege_openthread');
				}
				$expiration = checkexpiration($_GET['expirationclose'], $operation);
				$modaction = $expiration ? 'EOP' : 'OPN';

				table_forum_thread::t()->update($tidsarr, ['closed' => 0, 'moderated' => 1], true);
				table_forum_threadmod::t()->update_by_tid_action($tidsarr, ['CLS', 'OPN', 'ECL', 'UCL', 'EOP', 'UEO'], ['status' => 0]);
			} elseif($operation == 'move') {
				if(!$_G['group']['allowmovethread']) {
					showmessage('no_privilege_movethread');
				}
				$moveto = $_GET['moveto'];
				$toforum = table_forum_forum::t()->fetch_info_by_fid($moveto);
				if(!$toforum || ($_G['adminid'] != 1 && $toforum['status'] != 1) || $toforum['type'] == 'group') {
					showmessage('admin_move_invalid');
				} elseif($_G['fid'] == $toforum['fid']) {
					continue;
				} else {
					$moveto = $toforum['fid'];
					$modnewthreads = (!$_G['group']['allowdirectpost'] || $_G['group']['allowdirectpost'] == 1) && $toforum['modnewposts'] ? 1 : 0;
					$modnewreplies = (!$_G['group']['allowdirectpost'] || $_G['group']['allowdirectpost'] == 2) && $toforum['modnewposts'] ? 1 : 0;
					if($modnewthreads || $modnewreplies) {
						showmessage('admin_move_have_mod');
					}
				}

				if($_G['adminid'] == 3) {
					$priv = table_forum_forumfield::t()->check_moderator_for_uid($moveto, $_G['uid'], $_G['member']['accessmasks']);
					if((($priv['postperm'] && !in_array($_G['groupid'], explode("\t", $priv['postperm']))) || ($_G['member']['accessmasks'] && ($priv['allowview'] || $priv['allowreply'] || $priv['allowgetattach'] || $priv['allowpostattach']) && !$priv['allowpost'])) && !$priv['istargetmod']) {
						showmessage('admin_move_nopermission');
					}
				}

				$moderate = [];
				$stickmodify = 0;
				$toforumallowspecial = [
					1 => $toforum['allowpostspecial'] & 1,
					2 => $toforum['allowpostspecial'] & 2,
					3 => isset($_G['setting']['extcredits'][$_G['setting']['creditstransextra'][2]]) && ($toforum['allowpostspecial'] & 4),
					4 => $toforum['allowpostspecial'] & 8,
					5 => $toforum['allowpostspecial'] & 16,
					127 => $_G['setting']['threadplugins'] ? dunserialize($toforum['threadplugin']) : [],
				];
				foreach($threadlist as $tid => $thread) {
					$allowmove = 0;
					if(!$thread['special']) {
						$allowmove = 1;
					} else {
						if($thread['special'] != 127) {
							$allowmove = $toforum['allowpostspecial'] ? $toforumallowspecial[$thread['special']] : 0;
						} else {
							if($toforumallowspecial[127]) {
								$posttable = getposttablebytid($thread['tid']);
								$message = table_forum_post::t()->fetch_threadpost_by_tid_invisible($thread['tid']);
								$message = $message['message'];
								$sppos = strrpos($message, chr(0).chr(0).chr(0));
								$specialextra = substr($message, $sppos + 3);
								$allowmove = in_array($specialextra, $toforumallowspecial[127]);
							} else {
								$allowmove = 0;
							}
						}
					}

					if($allowmove) {
						$moderate[] = $tid;
						if(in_array($thread['displayorder'], [2, 3])) {
							$stickmodify = 1;
						}
						if($_GET['type'] == 'redirect') {

							$insertdata = [
								'fid' => $thread['fid'],
								'readperm' => $thread['readperm'],
								'author' => $thread['author'],
								'authorid' => $thread['authorid'],
								'subject' => $thread['subject'],
								'dateline' => $thread['dateline'],
								'lastpost' => $thread['dblastpost'],
								'lastposter' => $thread['lastposter'],
								'views' => 0,
								'replies' => 0,
								'displayorder' => 0,
								'digest' => 0,
								'closed' => $thread['tid'],
								'special' => 0,
								'attachment' => 0,
								'typeid' => $_GET['threadtypeid']
							];
							$newtid = table_forum_thread::t()->insert($insertdata, true);
							if($newtid) {
								table_forum_threadclosed::t()->insert(['tid' => $thread['tid'], 'redirect' => $newtid], true, true);
							}
						}
					}
				}

				if(!$moderatetids = implode(',', $moderate)) {
					showmessage('admin_moderate_invalid');
				}
				$fieldarr = [
					'fid' => $moveto,
					'isgroup' => 0,
					'typeid' => $_GET['threadtypeid'],
					'moderated' => 1
				];
				if($_G['adminid'] == 3) {
					$fieldarr['displayorder'] = 0;
				}
				table_forum_thread::t()->update($tidsarr, $fieldarr, true);
				table_forum_forumrecommend::t()->update($tidsarr, ['fid' => $moveto]);
				loadcache('posttableids');
				$posttableids = $_G['cache']['posttableids'] ? $_G['cache']['posttableids'] : ['0'];
				foreach($posttableids as $id) {
					table_forum_post::t()->update_by_tid($id, $tidsarr, ['fid' => $moveto]);
				}
				$typeoptionvars = table_forum_typeoptionvar::t()->fetch_all_by_tid_optionid($tidsarr);
				foreach($typeoptionvars as $typeoptionvar) {
					table_forum_typeoptionvar::t()->update_by_tid($typeoptionvar['tid'], ['fid' => $moveto]);
					table_forum_optionvalue::t()->update_optionvalue($typeoptionvar['sortid'], $typeoptionvar['tid'], $_G['fid'], "fid='$moveto'");
				}

				if($_G['setting']['globalstick'] && $stickmodify) {
					require_once libfile('function/cache');
					updatecache('globalstick');
				}
				$modaction = 'MOV';
				$_G['toforum'] = $toforum;
				updateforumcount($moveto);
				updateforumcount($_G['fid']);
			} elseif($operation == 'type') {
				if(!$_G['group']['allowedittypethread']) {
					showmessage('no_privilege_edittypethread');
				}
				if(!isset($_G['forum']['threadtypes']['types'][$_GET['typeid']]) && ($_GET['typeid'] != 0 || $_G['forum']['threadtypes']['required'])) {
					showmessage('admin_type_invalid');
				}

				table_forum_thread::t()->update($tidsarr, ['typeid' => $_GET['typeid'], 'moderated' => 1], true);
				$modaction = 'TYP';
			} elseif($operation == 'recommend_group') {
				if($_G['forum']['status'] != 3 || !in_array($_G['adminid'], [1, 2])) {
					showmessage('undefined_action');
				}
				$moveto = $_GET['moveto'];
				$toforum = table_forum_forum::t()->fetch_info_by_fid($moveto);
				if(!$toforum || $toforum['status'] != 1 || $toforum['type'] == 'group') {
					showmessage('admin_move_invalid');
				} elseif($_G['fid'] == $toforum['fid']) {
					continue;
				}
				$moderate = [];
				$toforumallowspecial = [
					1 => $toforum['allowpostspecial'] & 1,
					2 => $toforum['allowpostspecial'] & 2,
					3 => isset($_G['setting']['extcredits'][$_G['setting']['creditstransextra'][2]]) && ($toforum['allowpostspecial'] & 4),
					4 => $toforum['allowpostspecial'] & 8,
					5 => $toforum['allowpostspecial'] & 16,
					127 => $_G['setting']['threadplugins'] ? dunserialize($toforum['threadplugin']) : [],
				];
				foreach($threadlist as $tid => $thread) {
					$allowmove = 0;
					if($thread['closed']) {
						continue;
					}
					if(!$thread['special']) {
						$allowmove = 1;
					} else {
						if($thread['special'] != 127) {
							$allowmove = $toforum['allowpostspecial'] ? $toforumallowspecial[$thread['special']] : 0;
						} else {
							if($toforumallowspecial[127]) {
								$posttable = getposttablebytid($thread['tid']);
								$message = table_forum_post::t()->fetch_threadpost_by_tid_invisible($thread['tid']);
								$message = $message['message'];
								$sppos = strrpos($message, chr(0).chr(0).chr(0));
								$specialextra = substr($message, $sppos + 3);
								$allowmove = in_array($specialextra, $toforumallowspecial[127]);
							} else {
								$allowmove = 0;
							}
						}
					}

					if($allowmove) {
						$moderate[] = $tid;

						$newthread = [
							'fid' => $moveto,
							'readperm' => $thread['readperm'],
							'author' => $thread['author'],
							'authorid' => $thread['authorid'],
							'subject' => $thread['subject'],
							'dateline' => $thread['dateline'],
							'lastpost' => TIMESTAMP,
							'lastposter' => $thread['lastposter'],
							'views' => $thread['views'],
							'replies' => $thread['replies'],
							'displayorder' => 0,
							'digest' => $thread['digest'],
							'closed' => $thread['tid'],
							'special' => $thread['special'],
							'attachment' => $thread['attachment'],
							'isgroup' => $thread['isgroup']
						];
						$newtid = table_forum_thread::t()->insert($newthread, true);
						table_forum_thread::t()->update($thread['tid'], ['closed' => $newtid, 'moderated' => 1], true);
					}
				}
				if(!$moderatetids = implode(',', $moderate)) {
					showmessage('admin_succeed', $_G['referer']);
				}
				$modaction = 'REG';
			}

			if($updatemodlog) {
				updatemodlog($moderatetids, $modaction, $expiration, 0, $reason);
			}

			updatemodworks($modaction, $modpostsnum);
			foreach($threadlist as $thread) {
				modlog($thread, $modaction);
			}

			if($sendreasonpm) {
				$modactioncode = lang('forum/modaction');
				$modtype = $modaction;
				$modaction = $modactioncode[$modaction];
				foreach($threadlist as $thread) {
					if($operation == 'move') {
						sendreasonpm($thread, 'reason_move', ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason, 'tofid' => $toforum['fid'], 'toname' => $toforum['name'], 'from_id' => 0, 'from_idtype' => 'movethread'], 'post');
					} else {
						sendreasonpm($thread, 'reason_moderate', ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason, 'from_id' => 0, 'from_idtype' => 'moderate_'.$modtype], 'post');
					}
				}
			}

			if($stampstatus) {
				set_stamp($stampstatus, $stampaction, $threadlist, $expiration);
			}

			
			$fidarr = [];
			foreach($threadlist as $thread) {
				$fidarr[] = $thread['fid'];
			}
			table_forum_thread::t()->clear_cache($fidarr, 'forumdisplay_');

		}
		showmessage('admin_succeed', $_G['referer']);
	}

}

function checkexpiration($expiration, $operation) {
	global $_G;
	if(!empty($expiration) && in_array($operation, ['recommend', 'stick', 'digest', 'highlight', 'close', 'open', 'bump'])) {
		$expiration = strtotime($expiration) - $_G['setting']['timeoffset'] * 3600 + date('Z');
		if(dgmdate($expiration, 'Ymd') <= dgmdate(TIMESTAMP, 'Ymd') || ($expiration > TIMESTAMP + 86400 * 180)) {
			showmessage('admin_expiration_invalid', '', ['min' => dgmdate(TIMESTAMP, 'Y-m-d'), 'max' => dgmdate(TIMESTAMP + 86400 * 180, 'Y-m-d')]);
		}
	} else {
		$expiration = 0;
	}
	return $expiration;
}

function set_stamp($typeid, $stampaction, &$threadlist, $expiration) {
	global $_G;
	$moderatetids = array_keys($threadlist);
	if(empty($threadlist)) {
		return false;
	}
	if(array_key_exists($typeid, $_G['cache']['stamptypeid'])) {
		if($stampaction == 'SPD') {
			table_forum_thread::t()->update($moderatetids, ['stamp' => -1], true);
		} else {
			table_forum_thread::t()->update($moderatetids, ['stamp' => $_G['cache']['stamptypeid'][$typeid]], true);
		}
		!empty($moderatetids) && updatemodlog($moderatetids, $stampaction, $expiration, 0, '', $_G['cache']['stamptypeid'][$typeid]);
	}
}

function get_expiration($tid, $action) {
	$tid = intval($tid);
	if(empty($tid) || empty($action)) {
		return '';
	}
	$row = table_forum_threadmod::t()->fetch_by_tid_action_status($tid, $action);
	return $row['expiration'] ? date('Y-m-d H:i', $row['expiration']) : '';
}

