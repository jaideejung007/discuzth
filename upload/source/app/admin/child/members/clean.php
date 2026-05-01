<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('submit', 1) && !submitcheck('deletesubmit', 1)) {

	shownav('user', 'nav_members');
	showsubmenu('nav_members', [
		['search', 'members&operation=search', 0],
		['clean', 'members&operation=clean', 1],
		['nav_repeat', 'members&operation=repeat', 0],
		['add', 'members&operation=add', 0],
	]);

	showsearchform('clean');

} else {

	if((!$tmpsearch_condition && empty($_GET['uidarray'])) || (submitcheck('deletesubmit', 1) && empty($_GET['uidarray']))) {
		cpmsg('members_no_find_deluser', '', 'error');
	}
	if(!empty($_GET['deleteall'])) {
		unset($search_condition['uidarray']);
		$_GET['uidarray'] = '';
	}
	$uids = 0;
	$extra = '';
	$delmemberlimit = 300;
	$deletestart = intval($_GET['deletestart']);

	if(!empty($_GET['uidarray'])) {
		$uids = [];
		$allmember = table_common_member::t()->fetch_all($_GET['uidarray']);

		$membernum = 0;
		foreach($allmember as $uid => $member) {
			if(!is_protect_member($member)) {
				if($membernum < 2000) {
					$extra .= '<input type="hidden" name="uidarray[]" value="'.$member['uid'].'" />';
				}
				$uids[] = $member['uid'];
				$membernum++;
			}
		}
	} elseif($tmpsearch_condition) {
		$membernum = countmembers($search_condition, $urladd);
		$uids = searchmembers($search_condition, $delmemberlimit, 0);

		if($uids) {
			$protect_uids = table_common_member::t()->fetch_all_protect_member();
			$uids = array_diff($uids, $protect_uids);
			$membernum = count($uids);
		}
	}
	$allnum = intval($_GET['allnum']);

	if((empty($membernum) || empty($uids))) {
		if($deletestart) {
			cpmsg('members_delete_succeed', '', 'succeed', ['numdeleted' => $allnum]);
		}
		cpmsg('members_no_find_deluser', '', 'error');
	}
	if(!submitcheck('confirmed')) {

		loaducenter();
		$deluc = '';
		if(UC_STANDALONE) {
			$deluc = '<input type="hidden" name="includeuc" value="1" />';
		} else {
			$deluc = $isfounder ? '&nbsp;<label><input type="checkbox" name="includeuc" value="1" class="checkbox" />'.$lang['members_delete_ucdata'].'</label>' : '';
		}

		cpmsg('members_delete_confirm', 'action=members&operation=clean&submit=yes&confirmed=yes'.$urladd, 'form', ['membernum' => $membernum], $extra.'<br />'.'<label><input type="checkbox" name="includepost" value="1" class="checkbox" />'.$lang['members_delete_all'].'</label>'.$deluc, '');

	} else {

		if(!submitcheck('includepost')) {

			require_once libfile('function/delete');
			$numdeleted = deletemember($uids, 0);

			if($isfounder && !empty($_GET['includeuc'])) {
				loaducenter();
				uc_user_delete($uids);
				$_GET['includeuc'] = 1;
			} else {
				$_GET['includeuc'] = 0;
			}
			if($_GET['uidarray']) {
				cpmsg('members_delete_succeed', '', 'succeed', ['numdeleted' => $numdeleted]);
			} else {
				$allnum += $membernum < $delmemberlimit ? $membernum : $delmemberlimit;
				$nextlink = 'action=members&operation=clean&confirmed=yes&submit=yes'.(!empty($_GET['includeuc']) ? '&includeuc=yes' : '')."&allnum=$allnum&deletestart=".($deletestart + $delmemberlimit).$urladd;
				cpmsg(cplang('members_delete_user_processing_next', ['deletestart' => $deletestart, 'nextdeletestart' => $deletestart + $delmemberlimit]), $nextlink, 'loadingform', []);
			}

		} else {


			$pertask = 1000;
			$current = $_GET['current'] ? intval($_GET['current']) : 0;
			$deleteitem = $_GET['deleteitem'] ? trim($_GET['deleteitem']) : 'post';
			$nextdeleteitem = $deleteitem;

			$next = $current + $pertask;

			if($deleteitem == 'post') {
				$threads = $fids = $threadsarray = [];
				foreach(table_forum_thread::t()->fetch_all_by_authorid($uids, $pertask) as $thread) {
					$threads[$thread['fid']][] = $thread['tid'];
				}

				if($threads) {
					require_once libfile('function/post');
					foreach($threads as $fid => $tids) {
						deletethread($tids);
					}
					if($_G['setting']['globalstick']) {
						require_once libfile('function/cache');
						updatecache('globalstick');
					}
				} else {
					$next = 0;
					$nextdeleteitem = 'blog';
				}
			}

			if($deleteitem == 'blog') {
				$blogs = [];
				$query = table_home_blog::t()->fetch_blogid_by_uid($uids, 0, $pertask);
				foreach($query as $blog) {
					$blogs[] = $blog['blogid'];
				}

				if($blogs) {
					deleteblogs($blogs);
				} else {
					$next = 0;
					$nextdeleteitem = 'pic';
				}
			}

			if($deleteitem == 'pic') {
				$pics = [];
				$query = table_home_pic::t()->fetch_all_by_uid($uids, 0, $pertask);
				foreach($query as $pic) {
					$pics[] = $pic['picid'];
				}

				if($pics) {
					deletepics($pics);
				} else {
					$next = 0;
					$nextdeleteitem = 'doing';
				}
			}

			if($deleteitem == 'doing') {
				$doings = [];
				$query = table_home_doing::t()->fetch_all_by_uid_doid($uids, '', '', 0, $pertask);
				foreach($query as $doing) {
					$doings[] = $doing['doid'];
				}

				if($doings) {
					deletedoings($doings);
				} else {
					$next = 0;
					$nextdeleteitem = 'share';
				}
			}

			if($deleteitem == 'share') {
				$shares = [];
				foreach(table_home_share::t()->fetch_all_by_uid($uids, $pertask) as $share) {
					$shares[] = $share['sid'];
				}

				if($shares) {
					deleteshares($shares);
				} else {
					$next = 0;
					$nextdeleteitem = 'feed';
				}
			}

			if($deleteitem == 'feed') {
				table_home_follow_feed::t()->delete_by_uid($uids);
				$nextdeleteitem = 'comment';
			}

			if($deleteitem == 'comment') {
				$comments = [];
				$query = table_home_comment::t()->fetch_all_by_uid($uids, 0, $pertask);
				foreach($query as $comment) {
					$comments[] = $comment['cid'];
				}

				if($comments) {
					deletecomments($comments);
				} else {
					$next = 0;
					$nextdeleteitem = 'allitem';
				}
			}

			if($deleteitem == 'allitem') {
				require_once libfile('function/delete');
				$numdeleted = deletemember($uids);

				if($isfounder && !empty($_GET['includeuc'])) {
					loaducenter();
					uc_user_delete($uids);
				}
				if(!empty($_GET['uidarray'])) {
					cpmsg('members_delete_succeed', '', 'succeed', ['numdeleted' => $numdeleted]);
				} else {
					$allnum += $membernum < $delmemberlimit ? $membernum : $delmemberlimit;
					$nextlink = 'action=members&operation=clean&confirmed=yes&submit=yes&includepost=yes'.(!empty($_GET['includeuc']) ? '&includeuc=yes' : '')."&allnum=$allnum&deletestart=".($deletestart + $delmemberlimit).$urladd;
					cpmsg(cplang('members_delete_user_processing_next', ['deletestart' => $deletestart, 'nextdeletestart' => $deletestart + $delmemberlimit]), $nextlink, 'loadingform', []);
				}
			}
			$nextlink = 'action=members&operation=clean&confirmed=yes&submit=yes&includepost=yes'.(!empty($_GET['includeuc']) ? '&includeuc=yes' : '')."&current=$next&pertask=$pertask&lastprocess=$processed&allnum=$allnum&deletestart=$deletestart".$urladd;
			if(empty($_GET['uidarray'])) {
				$deladdmsg = cplang('members_delete_user_processing', ['deletestart' => $deletestart, 'nextdeletestart' => $deletestart + $delmemberlimit]).'<br>';
			} else {
				$deladdmsg = '';
			}
			if($nextdeleteitem != $deleteitem) {
				$nextlink .= "&deleteitem=$nextdeleteitem";
				cpmsg(cplang('members_delete_processing_next', ['deladdmsg' => $deladdmsg, 'item' => cplang('members_delete_'.$deleteitem), 'nextitem' => cplang('members_delete_'.$nextdeleteitem)]), $nextlink, 'loadingform', [], $extra);
			} else {
				$nextlink .= "&deleteitem=$deleteitem";
				cpmsg(cplang('members_delete_processing', ['deladdmsg' => $deladdmsg, 'item' => cplang('members_delete_'.$deleteitem), 'current' => $current, 'next' => $next]), $nextlink, 'loadingform', [], $extra);
			}
		}
	}
}