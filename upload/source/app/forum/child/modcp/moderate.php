<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_MODCP')) {
	exit('Access Denied');
}

if(!$_G['setting']['forumstatus'] && $op != 'members') {
	showmessage('forum_status_off');
}

$modact = empty($_GET['modact']) || !in_array($_GET['modact'], ['delete', 'ignore', 'validate']) ? 'ignore' : $_GET['modact'];

if($op == 'members') {

	$filter = isset($_GET['filter']) ? intval($_GET['filter']) : 0;
	$filtercheck = ['', '', ''];
	$filtercheck[$filter] = 'selected';

	if(submitcheck('dosubmit', 1) || submitcheck('modsubmit')) {

		if(empty($modact) || !in_array($modact, ['ignore', 'validate', 'delete'])) {
			showmessage('modcp_noaction');
		}

		$list = [];
		if($_GET['moderate'] && is_array($_GET['moderate'])) {
			foreach($_GET['moderate'] as $val) {
				if(is_numeric($val) && $val) {
					$list[] = $val;
				}
			}
		}

		if(submitcheck('dosubmit', 1)) {

			$_GET['handlekey'] = 'mods';
			include template('forum/modcp_moderate_float');
			dexit();

		} elseif($uids = $list) {

			$members = $uidarray = [];


			$member_validate = table_common_member_validate::t()->fetch_all($uids);
			foreach(table_common_member::t()->fetch_all($uids, false, 0) as $uid => $member) {
				if(($member['groupid'] == 8 || (in_array($member['freeze'], [-1, 2]) && $modact != 'delete')) && $member['status'] == $filter) {
					$members[$uid] = array_merge((array)$member_validate[$uid], $member);
				}
			}
			if(($uids = array_keys($members))) {

				$reason = dhtmlspecialchars(trim($_GET['reason']));

				if($_GET['modact'] == 'delete') {
					table_common_member::t()->delete_no_validate($uids);
				}

				if($_GET['modact'] == 'validate') {
					table_common_member::t()->update($uids, ['adminid' => '0', 'groupid' => $_G['setting']['newusergroupid'], 'freeze' => 0]);
					table_common_member_validate::t()->delete($uids);
				}

				if($_GET['modact'] == 'ignore') {
					table_common_member_validate::t()->update($uids, ['moddate' => $_G['timestamp'], 'admin' => $_G['username'], 'status' => '1', 'remark' => $reason]);
				}

				$sendemail = $_GET['sendemail'] ?? 0;
				if($sendemail) {
					if(!function_exists('sendmail')) {
						include libfile('function/mail');
					}
					foreach($members as $uid => $member) {
						$member['regdate'] = dgmdate($member['regdate']);
						$member['submitdate'] = dgmdate($member['submitdate']);
						$member['moddate'] = dgmdate(TIMESTAMP);
						$member['operation'] = $_GET['modact'];
						$member['remark'] = $reason ? $reason : 'N/A';
						$moderate_member_message = [
							'tpl' => 'moderate_member',
							'var' => [
								'username' => $member['username'],
								'bbname' => $_G['setting']['bbname'],
								'regdate' => $member['regdate'],
								'submitdate' => $member['submitdate'],
								'submittimes' => $member['submittimes'],
								'message' => $member['message'],
								'modresult' => lang('email/template', 'moderate_member_'.$member['operation']),
								'moddate' => $member['moddate'],
								'adminusername' => $_G['member']['username'],
								'remark' => $member['remark'],
								'siteurl' => $_G['siteurl'],
							]
						];
						if(!sendmail("{$member['username']} <{$member['email']}>", $moderate_member_message)) {
							runlog('sendmail', "{$member['email']} sendmail failed.");
						}
					}
				}
			} else {
				showmessage('modcp_moduser_invalid');
			}

			showmessage('modcp_mod_succeed', "{$cpscript}?mod=modcp&action={$_GET['action']}&op=$op&filter=$filter");

		} else {
			showmessage('modcp_moduser_invalid');
		}

	} else {
		$count = table_common_member_validate::t()->fetch_all_status_by_count();

		$page = max(1, intval($_G['page']));
		$_G['setting']['memberperpage'] = 20;
		$start_limit = ($page - 1) * $_G['setting']['memberperpage'];

		$multipage = multi($count[$filter], $_G['setting']['memberperpage'], $page, "{$cpscript}?mod=modcp&action={$_GET['action']}&op=$op&fid={$_G['fid']}&filter=$filter");

		$vuids = [];
		$memberlist = $member_validate = $common_member = $member_status = [];
		if(($member_validate = table_common_member_validate::t()->fetch_all_by_status($filter, $start_limit, $_G['setting']['memberperpage']))) {
			$uids = array_keys($member_validate);
			$common_member = table_common_member::t()->fetch_all($uids, false, 0);
			$member_status = table_common_member_status::t()->fetch_all($uids, false, 0);
		}
		foreach($member_validate as $uid => $member) {
			$member = array_merge($member, (array)$common_member[$uid], (array)$member_status[$uid]);
			if($member['groupid'] != 8 && !in_array($member['freeze'], [-1, 2])) {
				$vuids[] = $member['uid'];
				continue;
			}
			$member['regdate'] = dgmdate($member['regdate']);
			$member['submitdate'] = dgmdate($member['submitdate']);
			$member['moddate'] = $member['moddate'] ? dgmdate($member['moddate']) : $lang['none'];
			$member['message'] = dhtmlspecialchars($member['message']);
			$member['admin'] = $member['admin'] ? "<a href=\"home.php?mod=space&username=".rawurlencode($member['admin'])."\" target=\"_blank\">{$member['admin']}</a>" : $lang['none'];
			$memberlist[] = $member;
		}
		if($vuids) {
			table_common_member_validate::t()->delete($vuids, 'UNBUFFERED');
		}

		return true;
	}
}

if(empty($modforums['fids'])) {
	return false;
} elseif($_G['fid'] && ($_G['forum']['type'] == 'group' || !$_G['forum']['ismoderator'])) {
	return false;
} else {
	$modfids = '';
	if($_G['fid']) {
		$modfids = $_G['fid'];
		$modfidsadd = "fid='{$_G['fid']}'";
	} elseif($_G['adminid'] == 1) {
		$modfidsadd = '';
	} else {
		$modfids = $modforums['fids'];
		$modfidsadd = "fid in ({$modforums['fids']})";
	}
}

$updatestat = false;

$op = !in_array($op, ['replies', 'threads']) ? 'threads' : $op;

$filter = !empty($_GET['filter']) ? -3 : 0;
$filtercheck = [0 => '', '-3' => ''];
$filtercheck[$filter] = 'selected="selected"';

$pstat = $filter == -3 ? -3 : -2;
$moderatestatus = $filter == -3 ? 1 : 0;

$tpp = 10;
$page = max(1, intval($_G['page']));
$start_limit = ($page - 1) * $tpp;

$postlist = [];
$posttableselect = '';

$modpost = ['validate' => 0, 'delete' => 0, 'ignore' => 0];
$moderation = ['validate' => [], 'delete' => [], 'ignore' => []];

require_once libfile('function/post');

if(submitcheck('dosubmit', 1) || submitcheck('modsubmit')) {

	$list = [];
	if($_GET['moderate'] && is_array($_GET['moderate'])) {
		foreach($_GET['moderate'] as $val) {
			if(is_numeric($val) && $val) {
				$moderation[$modact][] = $val;
			}
		}
	}

	if(submitcheck('modsubmit')) {

		$updatestat = $op == 'replies' ? 1 : 2;
		$modpost = [
			'ignore' => count($moderation['ignore']),
			'delete' => count($moderation['delete']),
			'validate' => count($moderation['validate'])
		];
	} elseif(submitcheck('dosubmit', 1)) {
		$_GET['handlekey'] = 'mods';
		$list = $moderation[$modact];
		include template('forum/modcp_moderate_float');
		dexit();

	}
}

if($op == 'replies') {
	$posttableid = intval($_GET['posttableid']);
	$posttable = getposttable($posttableid);

	$posttableselect = getposttableselect();

	if(submitcheck('modsubmit')) {

		$pmlist = [];
		if($ignorepids = dimplode($moderation['ignore'])) {
			table_forum_post::t()->update_post($posttableid, $moderation['ignore'], ['invisible' => -3], true, false, 0, -2, ($modfids ? explode(',', $modfids) : null));
			updatemoderate('pid', $moderation['ignore'], 1);
		}

		if($deletepids = dimplode($moderation['delete'])) {
			$deleteauthorids = [];
			$recyclebinpids = [];
			$pids = [];
			foreach(table_forum_post::t()->fetch_all_post($posttableid, $moderation['delete']) as $post) {
				if($post['invisible'] != $pstat || $post['first'] != 0 || ($modfids ? !in_array($post['fid'], explode(',', $modfids)) : 0)) {
					continue;
				}
				if($modforums['recyclebins'][$post['fid']]) {
					$recyclebinpids[] = $post['pid'];
				} else {
					$pids[] = $post['pid'];
				}
				if($post['authorid'] && $post['authorid'] != $_G['uid']) {
					$pmlist[] = [
						'act' => $_GET['reason'] ? 'modreplies_delete_reason' : 'modreplies_delete',
						'notevar' => ['modusername' => ($_G['setting']['moduser_public'] ? $_G['username'] : ''), 'reason' => dhtmlspecialchars($_GET['reason']), 'post' => messagecutstr($post['message'], 30)],
						'authorid' => $post['authorid'],
					];
				}
				if($_GET['crimerecord']) {
					require_once libfile('function/member');
					crime('recordaction', $post['authorid'], 'crime_delpost', lang('forum/misc', 'crime_postreason', ['reason' => dhtmlspecialchars($_GET['reason']), 'tid' => $post['tid'], 'pid' => $post['pid']]));
				}
				$deleteauthorids[$post['authorid']] = $post['authorid'];
			}

			if($recyclebinpids) {
				table_forum_post::t()->update_post($posttableid, $recyclebinpids, ['invisible' => '-5'], true);
			}

			if($pids) {
				require_once libfile('function/delete');
				deletepost($pids, 'pid', false, $posttableid);
			}

			if($_G['group']['allowbanuser'] && ($_GET['banuser'] || $_GET['userdelpost']) && $deleteauthorids) {
				$members = table_common_member::t()->fetch_all($deleteauthorids);
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
						require_once libfile('function/delete');
						deletememberpost($banuins);
					}
				}
			}

			updatemodworks('DLP', count($moderation['delete']));
			updatemoderate('pid', $moderation['delete'], 2);
		}

		$repliesmod = 0;
		if($validatepids = dimplode($moderation['validate'])) {

			$threads = $lastpost = $attachments = $pidarray = [];
			$postlist = $tids = [];
			foreach(table_forum_post::t()->fetch_all_post($posttableid, $moderation['validate']) as $post) {
				if($post['invisible'] != $pstat || $post['first'] != '0' || ($modfids ? !in_array($post['fid'], explode(',', $modfids)) : 0)) {
					continue;
				}
				$tids[$post['tid']] = $post['tid'];
				$postlist[] = $post;
			}
			$threadlist = table_forum_thread::t()->fetch_all($tids);
			foreach($postlist as $post) {
				$post['lastpost'] = $threadlist[$post['tid']]['lastpost'];
				$repliesmod++;
				$pidarray[] = $post['pid'];
				if(getstatus($post['status'], 3) == 0) {
					updatepostcredits('+', $post['authorid'], 'reply', $post['fid']);
					$attachcount = table_forum_attachment_n::t()->count_by_id('tid:'.$post['tid'], 'pid', $post['pid']);
					updatecreditbyaction('postattach', $post['authorid'], [], '', $attachcount, 1, $post['fid']);
				}

				$threads[$post['tid']]['posts']++;

				if($post['dateline'] > $post['lastpost'] && $post['dateline'] > $lastpost[$post['tid']]) {
					$threads[$post['tid']]['lastpost'] = $post['dateline'];
					$threads[$post['tid']]['lastposter'] = $post['anonymous'] && $post['dateline'] != $post['lastpost'] ? '' : addslashes($post['author']);
				}
				if($threads[$post['tid']]['attachadd'] || $post['attachment']) {
					$threads[$post['tid']]['attachment'] = 1;
				}

				$pm = 'pm_'.$post['pid'];
				if($post['authorid'] && $post['authorid'] != $_G['uid']) {
					$pmlist[] = [
						'act' => 'modreplies_validate',
						'notevar' => ['modusername' => ($_G['setting']['moduser_public'] ? $_G['username'] : ''), 'reason' => dhtmlspecialchars($_GET['reason']), 'pid' => $post['pid'], 'tid' => $post['tid'], 'post' => messagecutstr($post['message'], 30), 'from_id' => 0, 'from_idtype' => 'modreplies'],
						'authorid' => $post['authorid'],
					];
				}
				delay_task('run', 'replyNotice_'.$post['pid']);
			}
			unset($postlist, $tids, $threadlist);
			foreach($threads as $tid => $thread) {
				$updatedata = ['replies' => $thread['posts']];
				if(isset($thread['lastpost'])) {
					$updatedata['lastpost'] = [$thread['lastpost']];
					$updatedata['lastposter'] = [$thread['lastposter']];
				}
				if(isset($thread['attachment'])) {
					$updatedata['attachment'] = $thread['attachment'];
				}
				table_forum_thread::t()->increase($tid, $updatedata);
			}
			if($_G['fid']) {
				updateforumcount($_G['fid']);
			} else {
				$fids = array_keys($modforums['list']);
				foreach($fids as $f) {
					updateforumcount($f);
				}
			}

			if(!empty($pidarray)) {
				$pidarray[] = 0;
				$repliesmod = table_forum_post::t()->update_post($posttableid, $pidarray, ['invisible' => '0'], true);
				updatemodworks('MOD', $repliesmod);
				updatemoderate('pid', $pidarray, 2);
			} else {
				updatemodworks('MOD', 1);
			}
		}

		if($pmlist) {
			foreach($pmlist as $pm) {
				$post = $pm['post'];
				$_G['tid'] = intval($pm['tid']);
				notification_add($pm['authorid'], 'system', $pm['act'], $pm['notevar'], 1);
			}
		}

		showmessage('modcp_mod_succeed', "{$cpscript}?mod=modcp&action={$_GET['action']}&op=$op&filter=$filter&fid={$_G['fid']}");
	}

	$attachlist = [];

	require_once libfile('function/discuzcode');
	require_once libfile('function/attachment');

	$ppp = 10;
	$page = max(1, intval($_G['page']));
	$start_limit = ($page - 1) * $ppp;

	$modcount = table_common_moderate::t()->count_by_search_for_post($posttable, $moderatestatus, 0, ($modfids ? explode(',', $modfids) : null));
	$multipage = multi($modcount, $ppp, $page, "{$cpscript}?mod=modcp&action={$_GET['action']}&op=$op&filter=$filter&fid={$_G['fid']}&showcensor={$_GET['showcensor']}");

	if($modcount) {

		$attachtablearr = [];
		$_fids = [];
		foreach(table_common_moderate::t()->fetch_all_by_search_for_post($posttable, $moderatestatus, 0, ($modfids ? explode(',', $modfids) : null), null, null, null, $start_limit, $ppp) as $post) {
			$_fids[$post['fid']] = $post['fid'];
			$_tids[$post['tid']] = $post['tid'];
			$post['id'] = $post['pid'];
			$post['dateline'] = dgmdate($post['dateline']);
			$post['subject'] = $post['subject'] ? '<b>'.$post['subject'].'</b>' : '';
			$post['message'] = nl2br(dhtmlspecialchars($post['message']));

			if(!empty($_GET['showcensor'])) {
				$censor = &discuz_censor::instance();
				$censor->highlight = '#FF0000';
				$censor->check($post['subject']);
				$censor->check($post['message']);
				$censor_words = $censor->words_found;
				if(count($censor_words) > 3) {
					$censor_words = array_slice($censor_words, 0, 3);
				}
				$post['censorwords'] = implode(', ', $censor_words);
			}

			if($post['attachment']) {
				$attachtable = getattachtableid($post['tid']);
				$attachtablearr[$attachtable][$post['pid']] = $post['pid'];
			}
			$postlist[$post['pid']] = $post;
		}
		$_threads = $_forums = [];
		if($_fids) {
			$_forums = table_forum_forum::t()->fetch_all($_fids);
			foreach($postlist as &$_post) {
				$_forum = $_forums[$_post['fid']];
				$_arr = [
					'forumname' => $_forum['name'],
					'allowsmilies' => $_forum['allowsmilies'],
					'allowhtml' => $_forum['allowhtml'],
					'allowbbcode' => $_forum['allowbbcode'],
					'allowimgcode' => $_forum['allowimgcode'],
				];
				$_post = array_merge($_post, $_arr);
			}
		}
		if($_tids) {
			$_threads = table_forum_thread::t()->fetch_all($_tids);
			foreach($postlist as &$_post) {
				$_post['tsubject'] = $_threads[$_post['tid']]['subject'];
			}
		}

		if(!empty($attachtablearr)) {
			foreach($attachtablearr as $attachtable => $pids) {
				foreach(table_forum_attachment_n::t()->fetch_all_by_id($attachtable, 'pid', $pids) as $attach) {
					$_G['setting']['attachurl'] = $attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl'];
					$attach['url'] = $attach['isimage']
						? " {$attach['filename']} (".sizecount($attach['filesize']).")<br /><br /><img src=\"{$_G['setting']['attachurl']}forum/{$attach['attachment']}\" onload=\"if(this.width > 100) {this.resized=true; this.width=100;}\">"
						: "<a href=\"".$_G['setting']['attachurl']."forum/{$attach['attachment']}\" target=\"_blank\">{$attach['filename']}</a> (".sizecount($attach['filesize']).')';
					$postlist[$attach['pid']]['message'] .= '<br /><br />File: '.attachtype(fileext($attach['filename'])."\t").$attach['url'];
				}
			}
		}
	}


} else {

	if(submitcheck('modsubmit')) {
		if(!empty($moderation['ignore'])) {
			table_forum_thread::t()->update_by_tid_displayorder($moderation['ignore'], -2, ['displayorder' => -3], ($modfids ? explode(',', $modfids) : null));
			updatemoderate('tid', $moderation['ignore'], 1);
		}
		$threadsmod = 0;
		$pmlist = [];
		$reason = trim($_GET['reason']);

		if(!empty($moderation['delete'])) {
			$deleteauthorids = [];
			$deletetids = [];
			$recyclebintids = '0';
			foreach(table_forum_thread::t()->fetch_all_by_tid_displayorder($moderation['delete'], $pstat, '=', ($modfids ? explode(',', $modfids) : null)) as $thread) {
				if($modforums['recyclebins'][$thread['fid']]) {
					$recyclebintids .= ','.$thread['tid'];
				} else {
					$deletetids[] = $thread['tid'];
				}

				if($thread['authorid'] && $thread['authorid'] != $_G['uid']) {
					$pmlist[] = [
						'act' => $_GET['reason'] ? 'modthreads_delete_reason' : 'modthreads_delete',
						'notevar' => ['reason' => dhtmlspecialchars($_GET['reason']), 'threadsubject' => $thread['subject'], 'modusername' => ($_G['setting']['moduser_public'] ? $_G['username'] : '')],
						'authorid' => $thread['authorid'],
					];
				}

				if($_GET['crimerecord']) {
					require_once libfile('function/member');
					crime('recordaction', $thread['authorid'], 'crime_delpost', lang('forum/misc', 'crime_postreason', ['reason' => dhtmlspecialchars($_GET['reason']), 'tid' => $thread['tid'], 'pid' => $thread['pid']]));
				}

				$deleteauthorids[$thread['authorid']] = $thread['authorid'];

			}

			if($recyclebintids) {
				$rows = table_forum_thread::t()->update(explode(',', $recyclebintids), ['displayorder' => -1, 'moderated' => 1]);
				updatemodworks('MOD', $rows);

				table_forum_post::t()->update_by_tid(0, explode(',', $recyclebintids), ['invisible' => -1], true);
				updatemodlog($recyclebintids, 'DEL');
			}

			require_once libfile('function/delete');
			deletethread($deletetids);

			if($_G['group']['allowbanuser'] && ($_GET['banuser'] || $_GET['userdelpost']) && $deleteauthorids) {
				$members = table_common_member::t()->fetch_all($deleteauthorids);
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

			updatemoderate('tid', $moderation['delete'], 2);
		}

		if($validatetids = dimplode($moderation['validate'])) {

			$tids = $moderatedthread = [];
			foreach(table_forum_thread::t()->fetch_all_by_tid_displayorder($moderation['validate'], $pstat, '=', ($modfids ? explode(',', $modfids) : null)) as $thread) {
				$tids[] = $thread['tid'];
				$poststatus = table_forum_post::t()->fetch_threadpost_by_tid_invisible($thread['tid']);
				$poststatus = $poststatus['status'];
				if(getstatus($poststatus, 3) == 0) {
					updatepostcredits('+', $thread['authorid'], 'post', $thread['fid']);
					$attachcount = table_forum_attachment_n::t()->count_by_id('tid:'.$thread['tid'], 'tid', $thread['tid']);
					updatecreditbyaction('postattach', $thread['authorid'], [], '', $attachcount, 1, $thread['fid']);
				}
				$validatedthreads[] = $thread;

				if($thread['authorid'] && $thread['authorid'] != $_G['uid']) {
					$pmlist[] = [
						'act' => 'modthreads_validate',
						'notevar' => ['reason' => dhtmlspecialchars($_GET['reason']), 'tid' => $thread['tid'], 'threadsubject' => $thread['subject'], 'from_id' => 0, 'from_idtype' => 'modthreads', 'modusername' => ($_G['setting']['moduser_public'] ? $_G['username'] : '')],
						'authorid' => $thread['authorid'],
					];
				}
			}

			if($tids) {

				$tidstr = dimplode($tids);
				table_forum_post::t()->update_by_tid(0, $tids, ['invisible' => 0], true, false, 1);
				table_forum_thread::t()->update($tids, ['displayorder' => 0, 'moderated' => 1]);
				$threadsmod = DB::affected_rows();

				if($_G['fid']) {
					updateforumcount($_G['fid']);
				} else {
					$fids = array_keys($modforums['list']);
					foreach($fids as $f) {
						updateforumcount($f);
					}
				}
				updatemodworks('MOD', $threadsmod);
				updatemodlog($tidstr, 'MOD');
				updatemoderate('tid', $tids, 2);

			}
		}

		if($pmlist) {
			foreach($pmlist as $pm) {
				$threadsubject = $pm['thread'];
				$_G['tid'] = intval($pm['tid']);
				notification_add($pm['authorid'], 'system', $pm['act'], $pm['notevar'], 1);
			}
		}

		showmessage('modcp_mod_succeed', "{$cpscript}?mod=modcp&action={$_GET['action']}&op=$op&filter=$filter&fid={$_G['fid']}");

	}

	$modcount = table_common_moderate::t()->count_by_seach_for_thread($moderatestatus, ($modfids ? explode(',', $modfids) : null));
	$multipage = multi($modcount, $_G['tpp'], $page, "{$cpscript}?mod=modcp&action={$_GET['action']}&op=$op&filter=$filter&fid={$_G['fid']}&showcensor={$_GET['showcensor']}");

	if($modcount) {
		$posttablearr = [];

		foreach(table_common_moderate::t()->fetch_all_by_search_for_thread($moderatestatus, ($modfids ? explode(',', $modfids) : null), $start_limit, $_G['tpp']) as $thread) {

			$thread['id'] = $thread['tid'];

			if($thread['authorid'] && $thread['author'] != '') {
				$thread['author'] = "<a href=\"home.php?mod=space&uid={$thread['authorid']}\" target=\"_blank\">{$thread['author']}</a>";
			} elseif($thread['authorid']) {
				$thread['author'] = "<a href=\"home.php?mod=space&uid={$thread['authorid']}\" target=\"_blank\">UID {$thread['uid']}</a>";
			} else {
				$thread['author'] = 'guest';
			}

			$thread['dateline'] = dgmdate($thread['dateline']);
			$posttable = $thread['posttableid'] ? (string)$thread['posttableid'] : '0';
			$posttablearr[$posttable][$thread['tid']] = $thread['tid'];
			$postlist[$thread['tid']] = $thread;
		}

		$attachtablearr = [];

		foreach($posttablearr as $posttable => $tids) {
			foreach(table_forum_post::t()->fetch_all_by_tid($posttable, $tids, true, '', 0, 0, 1) as $post) {
				$thread = $postlist[$post['tid']] + $post;
				$thread['message'] = nl2br(dhtmlspecialchars($thread['message']));

				if(!empty($_GET['showcensor'])) {
					$censor = &discuz_censor::instance();
					$censor->highlight = '#FF0000';
					$censor->check($thread['subject']);
					$censor->check($thread['message']);
					$censor_words = $censor->words_found;
					if(count($censor_words) > 3) {
						$censor_words = array_slice($censor_words, 0, 3);
					}
					$thread['censorwords'] = implode(', ', $censor_words);
				}

				if($thread['attachment']) {
					$attachtable = getattachtableid($thread['tid']);
					$attachtablearr[$attachtable][$thread['tid']] = $thread['tid'];
				} else {
					$thread['attach'] = '';
				}

				if($thread['sortid']) {
					require_once libfile('function/threadsort');
					$threadsortshow = threadsortshow($thread['sortid'], $thread['tid']);

					foreach($threadsortshow['optionlist'] as $option) {
						$thread['sortinfo'] .= $option['title'].' '.$option['value'].'<br />';
					}
				} else {
					$thread['sortinfo'] = '';
				}

				$postlist[$post['tid']] = $thread;
			}
		}

		if(!empty($attachtablearr)) {
			require_once libfile('function/attachment');
			foreach($attachtablearr as $attachtable => $tids) {
				foreach(table_forum_attachment_n::t()->fetch_all_by_id($attachtable, 'tid', $tids) as $attach) {
					$tid = $attach['tid'];
					$_G['setting']['attachurl'] = $attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl'];
					$attach['url'] = $attach['isimage']
						? " {$attach['filename']} (".sizecount($attach['filesize']).")<br /><br /><img src=\"".$_G['setting']['attachurl']."forum/{$attach['attachment']}\" onload=\"if(this.width > 100) {this.resized=true; this.width=100;}\">"
						: "<a href=\"".$_G['setting']['attachurl']."forum/{$attach['attachment']}\" target=\"_blank\">{$attach['filename']}</a> (".sizecount($attach['filesize']).')';
					$postlist[$tid]['attach'] .= "<br /><br />{$lang['attachment']}: ".attachtype(fileext($attach['filename'])."\t").$attach['url'];
				}
			}
		}
	}

}
