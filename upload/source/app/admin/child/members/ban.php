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
$membergroup = table_common_usergroup::t()->fetch($member['groupid']);
$membergroupfield = table_common_usergroup_field::t()->fetch($member['groupid']);
$member = array_merge($member, $membermf, $membergroup, $membergroupfield);

if(($member['type'] == 'system' && in_array($member['groupid'], [1, 2, 3, 6, 7, 8])) || $member['type'] == 'special') {
	cpmsg('members_edit_illegal', '', 'error', ['grouptitle' => $member['grouptitle'], 'uid' => $member['uid']]);
}

if($member['allowadmincp']) {
	cpmsg('members_edit_illegal_portal', '', 'error', ['uid' => $member['uid']]);
}

$member['groupterms'] = dunserialize($member['groupterms']);
$member['banexpiry'] = !empty($member['groupterms']['main']['time']) && ($member['groupid'] == 4 || $member['groupid'] == 5) ? dgmdate($member['groupterms']['main']['time'], 'Y-n-j') : '';

if(!submitcheck('bansubmit')) {

	echo '<script src="'.STATICURL.'js/calendar.js" type="text/javascript"></script>';
	shownav('user', 'members_ban_user');
	showchildmenu([['nav_members', 'members&operation=list'],
		[$member['username'].' ', 'members&operation=edit&uid='.$member['uid']]], cplang('members_ban_user'));

	showtips('members_ban_tips');
	showformheader('members&operation=ban');
	showtableheader();
	showsetting('members_ban_username', 'username', $member['username'], 'text', null, null, '<input type="button" id="crimebtn" class="btn" style="margin-top:-1px;display:none;" onclick="getcrimerecord();" value="'.$lang['crime_checkrecord'].'" />', 'onkeyup="showcrimebtn(this);" id="banusername"');
	if($member) {

		showtagheader('tbody', 'member_status', 1);
		showtablerow('', 'class="td27" colspan="2"', cplang('members_edit_current_status').'<span class="normal">: '.($member['groupid'] == 4 ? $lang['members_ban_post'] : ($member['groupid'] == 5 ? $lang['members_ban_visit'] : ($member['status'] == -1 ? $lang['members_ban_status'] : $lang['members_ban_none']))).'</span>');

		include_once libfile('function/member');
		$clist = crime('getactionlist', $member['uid']);

		if($clist) {
			echo '<tr><td class="td27" colspan="2">'.$lang['members_ban_crime_record'].':</td></tr>';
			echo '<tr><td colspan="2" style="padding:0 !important;border-top:none;"><table style="width:100%;">';
			showtablerow('class="partition"', ['width="15%"', 'width="10%"', 'width="20%"', '', 'width="15%"'], [$lang['crime_user'], $lang['crime_action'], $lang['crime_dateline'], $lang['crime_reason'], $lang['crime_operator']]);
			foreach($clist as $crime) {
				showtablerow('', '', ['<a href="home.php?mod=space&uid='.$member['uid'].'">'.$member['username'], $lang[$crime['action']], date('Y-m-d H:i:s', $crime['dateline']), $crime['reason'], '<a href="home.php?mod=space&uid='.$crime['operatorid'].'" target="_blank">'.$crime['operator'].'</a>']);
			}
			echo '</table></td></tr>';
		}
		showtagfooter('tbody');
	}
	showsetting('members_ban_type', ['bannew', [
		['', $lang['members_ban_none'], ['validity' => 'none']],
		['post', $lang['members_ban_post'], ['validity' => '']],
		['visit', $lang['members_ban_visit'], ['validity' => '']],
		['status', $lang['members_ban_status'], ['validity' => 'none']]
	]], '', 'mradio');
	showtagheader('tbody', 'validity', false, 'sub');
	showsetting('members_ban_validity', '', '', selectday('banexpirynew', [0, 1, 3, 5, 7, 14, 30, 60, 90, 180, 365]));
	showtagfooter('tbody');
	print <<<EOF
			<tr>
				<td class="td27" colspan="2">{$lang['members_ban_clear_content']}:</td>
			</tr>
			<tr>
				<td colspan="2">
					<ul class="dblist" onmouseover="altStyle(this);">
						<li style="width: 100%;"><input type="checkbox" name="chkall" onclick="checkAll('prefix', this.form, 'clear', 'chkall', true)" class="checkbox">&nbsp;{$lang['select_all']}</li>
						<li style="width: 8%;"><input type="checkbox" value="post" name="clear[post]" class="checkbox">&nbsp;{$lang['members_ban_delpost']}</li>
						<li style="width: 8%;"><input type="checkbox" value="follow" name="clear[follow]" class="checkbox">&nbsp;{$lang['members_ban_delfollow']}</li>
						<li style="width: 8%;"><input type="checkbox" value="postcomment" name="clear[postcomment]" class="checkbox">&nbsp;{$lang['members_ban_postcomment']}</li>
						<li style="width: 8%;"><input type="checkbox" value="doing" name="clear[doing]" class="checkbox">&nbsp;{$lang['members_ban_deldoing']}</li>
						<li style="width: 8%;"><input type="checkbox" value="blog" name="clear[blog]" class="checkbox">&nbsp;{$lang['members_ban_delblog']}</li>
						<li style="width: 8%;"><input type="checkbox" value="album" name="clear[album]" class="checkbox">&nbsp;{$lang['members_ban_delalbum']}</li>
						<li style="width: 8%;"><input type="checkbox" value="share" name="clear[share]" class="checkbox">&nbsp;{$lang['members_ban_delshare']}</li>
						<li style="width: 8%;"><input type="checkbox" value="avatar" name="clear[avatar]" class="checkbox">&nbsp;{$lang['members_ban_delavatar']}</li>
						<li style="width: 8%;"><input type="checkbox" value="comment" name="clear[comment]" class="checkbox">&nbsp;{$lang['members_ban_delcomment']}</li>
						<li style="width: 8%;"><input type="checkbox" value="others" name="clear[others]" class="checkbox">&nbsp;{$lang['members_ban_delothers']}</li>
						<li style="width: 8%;"><input type="checkbox" value="profile" name="clear[profile]" class="checkbox">&nbsp;{$lang['members_ban_delprofile']}</li>
					</ul>
				</td>
			</tr>
EOF;

	showsetting('members_ban_reason', 'reason', '', 'textarea');
	showsubmit('bansubmit');
	showtablefooter();
	showformfooter();
	$basescript = ADMINSCRIPT;
	print <<<EOF
			<script type="text/javascript">
				var oldbanusername = '{$member['username']}';
				function showcrimebtn(obj) {
					if(oldbanusername == obj.value) {
						return;
					}
					oldbanusername = obj.value;
					$('crimebtn').style.display = '';
					if($('member_status')) {
						$('member_status').style.display = 'none';
					}
				}
				function getcrimerecord() {
					if($('banusername').value) {
						window.location.href = '$basescript?action=members&operation=ban&username=' + $('banusername').value;
					}
				}
			</script>
EOF;

} else {

	if(empty($member)) {
		cpmsg('members_edit_nonexistence');
	}

	$setarr = [];
	$reason = trim($_GET['reason']);
	if(!$reason && ($_G['group']['reasonpm'] == 1 || $_G['group']['reasonpm'] == 3)) {
		cpmsg('members_edit_reason_invalid', '', 'error');
	}
	$my_data = [];
	$mylogtype = '';
	if(in_array($_GET['bannew'], ['post', 'visit', 'status'])) {
		$my_data = ['uid' => $member['uid']];
		if($_GET['delpost']) {
			$my_data['otherid'] = 1;
		}
		$mylogtype = 'banuser';
	} elseif($member['groupid'] == 4 || $member['groupid'] == 5 || $member['status'] == '-1') {
		$my_data = ['uid' => $member['uid']];
		$mylogtype = 'unbanuser';
	}
	if($_GET['bannew'] == 'post' || $_GET['bannew'] == 'visit') {
		$groupidnew = $_GET['bannew'] == 'post' ? 4 : 5;
		$_GET['banexpirynew'] = !empty($_GET['banexpirynew']) ? TIMESTAMP + $_GET['banexpirynew'] * 86400 : 0;
		$_GET['banexpirynew'] = $_GET['banexpirynew'] > TIMESTAMP ? $_GET['banexpirynew'] : 0;
		if($_GET['banexpirynew']) {
			if($member['groupid'] == 4 || $member['groupid'] == 5) {
				$member['groupterms']['main']['time'] = $_GET['banexpirynew'];
				if(empty($member['groupterms']['main']['groupid'])) {
					$groupnew = table_common_usergroup::t()->fetch_by_credits($member['credits']);
					$member['groupterms']['main']['groupid'] = $groupnew['groupid'];
				}
				if(!isset($member['groupterms']['main']['adminid'])) {
					$member['groupterms']['main']['adminid'] = $member['adminid'];
				}
			} else {
				$member['groupterms']['main'] = ['time' => $_GET['banexpirynew'], 'adminid' => $member['adminid'], 'groupid' => $member['groupid']];
			}
			$member['groupterms']['ext'][$groupidnew] = $_GET['banexpirynew'];
			$setarr['groupexpiry'] = groupexpiry($member['groupterms']);
		} else {
			$setarr['groupexpiry'] = 0;
		}
		$adminidnew = -1;
		$my_data['expiry'] = groupexpiry($member['groupterms']);
		$postcomment_cache_pid = [];
		foreach(table_forum_postcomment::t()->fetch_all_by_authorid($member['uid']) as $postcomment) {
			$postcomment_cache_pid[$postcomment['pid']] = $postcomment['pid'];
		}
		table_forum_postcomment::t()->delete_by_authorid($member['uid'], false, true);
		if($postcomment_cache_pid) {
			table_forum_postcache::t()->delete($postcomment_cache_pid);
		}
		if(in_array($member['adminid'], [0, -1])) {
			$member_status = table_common_member_status::t()->fetch($member['uid']);
		}
	} elseif($member['groupid'] == 4 || $member['groupid'] == 5) {
		if(!empty($member['groupterms']['main']['groupid'])) {
			$groupidnew = $member['groupterms']['main']['groupid'];
			$adminidnew = $member['groupterms']['main']['adminid'];
			unset($member['groupterms']['main']);
			unset($member['groupterms']['ext'][$member['groupid']]);
			$setarr['groupexpiry'] = groupexpiry($member['groupterms']);
		}
		$groupnew = table_common_usergroup::t()->fetch_by_credits($member['credits']);
		$groupidnew = $groupnew['groupid'];
		$adminidnew = 0;
	} else {
		$update = false;
		$groupidnew = $member['groupid'];
		$adminidnew = $member['adminid'];
		if(is_array($_GET['clear']) && in_array('avatar', $_GET['clear'])) {
			$setarr['avatarstatus'] = 0;
			loaducenter();
			uc_user_deleteavatar($member['uid']);
		}
	}

	$setarr['adminid'] = $adminidnew;
	$setarr['groupid'] = $groupidnew;
	$setarr['status'] = $_GET['bannew'] == 'status' ? -1 : 0;
	C::t('common_member'.$tableext)->update($member['uid'], $setarr);

	if($_G['group']['allowbanuser'] && (DB::affected_rows())) {
		banlog($member['username'], $member['groupid'], $groupidnew, $_GET['banexpirynew'], $reason, $_GET['bannew'] == 'status' ? -1 : 0);
	}

	C::t('common_member_field_forum'.$tableext)->update($member['uid'], ['groupterms' => ($member['groupterms'] ? serialize($member['groupterms']) : '')]);

	$crimeaction = $noticekey = '';
	include_once libfile('function/member');
	if($_GET['bannew'] == 'post') {
		$crimeaction = 'crime_banspeak';
		$noticekey = 'member_ban_speak';
		$from_idtype = 'banspeak';
	} elseif($_GET['bannew'] == 'visit') {
		$crimeaction = 'crime_banvisit';
		$noticekey = 'member_ban_visit';
		$from_idtype = 'banvisit';
	} elseif($_GET['bannew'] == 'status') {
		$crimeaction = 'crime_banstatus';
		$noticekey = 'member_ban_status';
		$from_idtype = 'banstatus';
	} else {
		$crimeaction = 'members_ban_none';
	}
	if($crimeaction) {
		crime('recordaction', $member['uid'], $crimeaction, lang('forum/misc', 'crime_reason', ['reason' => $reason]));
	}
	if($noticekey) {
		$notearr = [
			'user' => "<a href=\"home.php?mod=space&uid={$_G['uid']}\">{$_G['username']}</a>",
			'day' => intval($_POST['banexpirynew']),
			'reason' => $reason,
			'from_id' => 0,
			'from_idtype' => $from_idtype
		];
		notification_add($member['uid'], 'system', $noticekey, $notearr, 1);
	}

	if($_G['adminid'] == 1 && !empty($_GET['clear']) && is_array($_GET['clear'])) {
		require_once libfile('function/delete');
		$membercount = [];
		if(in_array('post', $_GET['clear'])) {
			if($member['uid']) {
				require_once libfile('function/post');

				$tidsdelete = [];
				loadcache('posttableids');
				$posttables = empty($_G['cache']['posttableids']) ? [0] : $_G['cache']['posttableids'];
				foreach($posttables as $posttableid) {
					$pidsthread = $pidsdelete = [];
					$postlist = table_forum_post::t()->fetch_all_by_authorid($posttableid, $member['uid'], false);
					if($postlist) {
						foreach($postlist as $post) {
							$prune['forums'][] = $post['fid'];
							$prune['thread'][$post['tid']]++;
							if($post['first']) {
								$tidsdelete[] = $post['tid'];
							}
							$pidsdelete[] = $post['pid'];
							$pidsthread[$post['pid']] = $post['tid'];
						}
						foreach($pidsdelete as $key => $pid) {
							if(in_array($pidsthread[$pid], $tidsdelete)) {
								unset($pidsdelete[$key]);
								unset($prune['thread'][$pidsthread[$pid]]);
								updatemodlog($pidsthread[$pid], 'DEL');
							} else {
								updatemodlog($pidsthread[$pid], 'DLP');
							}
						}
					}
					deletepost($pidsdelete, 'pid', false, $posttableid, true);
				}
				unset($postlist);
				if($tidsdelete) {
					deletethread($tidsdelete, true, true, true);
				}
				if(!empty($prune)) {
					foreach($prune['thread'] as $tid => $decrease) {
						updatethreadcount($tid);
					}
				}

				if($_G['setting']['globalstick']) {
					updatecache('globalstick');
				}
			}
			$membercount['posts'] = 0;
			$membercount['threads'] = 0;
		}
		if(in_array('follow', $_GET['clear'])) {
			table_home_follow_feed::t()->delete_by_uid($member['uid']);
			$membercount['feeds'] = 0;
		}
		if(in_array('blog', $_GET['clear'])) {
			$blogids = [];
			$query = table_home_blog::t()->fetch_blogid_by_uid($member['uid']);
			foreach($query as $value) {
				$blogids[] = $value['blogid'];
			}
			if(!empty($blogids)) {
				table_common_moderate::t()->delete_moderate($blogids, 'blogid');
			}
			table_home_blog::t()->delete_by_uid($member['uid']);
			table_home_blogfield::t()->delete_by_uid($member['uid']);
			table_home_feed::t()->delete_by_uid_idtype($member['uid'], 'blogid');

			$membercount['blogs'] = 0;
		}
		if(in_array('album', $_GET['clear'])) {
			table_home_album::t()->delete_by_uid($member['uid']);
			$picids = [];
			$query = table_home_pic::t()->fetch_all_by_uid($member['uid']);
			foreach($query as $value) {
				$picids[] = $value['picid'];
				deletepicfiles($value);
			}
			if(!empty($picids)) {
				table_common_moderate::t()->delete_moderate($picids, 'picid');
			}
			table_home_pic::t()->delete_by_uid($member['uid']);
			table_home_feed::t()->delete_by_uid_idtype($member['uid'], 'albumid');

			$membercount['albums'] = 0;
		}
		if(in_array('share', $_GET['clear'])) {
			$shareids = [];
			foreach(table_home_share::t()->fetch_all_by_uid($member['uid']) as $value) {
				$shareids[] = $value['sid'];
			}
			if(!empty($shareids)) {
				table_common_moderate::t()->delete_moderate($shareids, 'sid');
			}
			table_home_share::t()->delete_by_uid($member['uid']);
			table_home_feed::t()->delete_by_uid_idtype($member['uid'], 'sid');

			$membercount['sharings'] = 0;
		}

		if(in_array('doing', $_GET['clear'])) {
			$doids = [];
			$query = table_home_doing::t()->fetch_all_by_uid_doid([$member['uid']]);
			foreach($query as $value) {
				$doids[$value['doid']] = $value['doid'];
			}
			if(!empty($doids)) {
				table_common_moderate::t()->delete_moderate($doids, 'doid');
			}
			table_home_doing::t()->delete_by_uid($member['uid']);
			table_common_member_field_home::t()->update($member['uid'], ['recentnote' => '', 'spacenote' => '']);

			table_home_docomment::t()->delete_by_doid_uid(($doids ? $doids : null), $member['uid']);
			table_home_feed::t()->delete_by_uid_idtype($member['uid'], 'doid');

			$membercount['doings'] = 0;
		}
		if(in_array('comment', $_GET['clear'])) {
			$delcids = [];
			$query = table_home_comment::t()->fetch_all_by_uid($member['uid'], 0, 1);
			foreach($query as $value) {
				$key = $value['idtype'].'_cid';
				$delcids[$key] = $value['cid'];
			}
			if(!empty($delcids)) {
				foreach($delcids as $key => $ids) {
					table_common_moderate::t()->delete_moderate($ids, $key);
				}
			}
			table_home_comment::t()->delete_by_uid_idtype($member['uid']);
		}
		if(in_array('postcomment', $_GET['clear'])) {
			$postcomment_cache_pid = [];
			foreach(table_forum_postcomment::t()->fetch_all_by_authorid($member['uid']) as $postcomment) {
				$postcomment_cache_pid[$postcomment['pid']] = $postcomment['pid'];
			}
			table_forum_postcomment::t()->delete_by_authorid($member['uid']);
			if($postcomment_cache_pid) {
				table_forum_postcache::t()->delete($postcomment_cache_pid);
			}
		}

		if(in_array('profile', $_GET['clear'])) {
			C::t('common_member_profile'.$tableext)->delete($member['uid']);
			C::t('common_member_profile'.$tableext)->insert(['uid' => $member['uid']]);
			C::t('common_member_field_forum'.$tableext)->update($member['uid'], ['customstatus' => '', 'sightml' => '']);
			C::t('common_member_field_home'.$tableext)->update($member['uid'], ['spacename' => '', 'spacedescription' => '']);
		}

		if(in_array('others', $_GET['clear'])) {
			// 家园访客记录清理
			table_home_clickuser::t()->delete_by_uid($member['uid']);
			table_home_visitor::t()->delete_by_uid_or_vuid($member['uid']);
			// 家园关注关系清理
			table_home_follow::t()->delete_by_uid($member['uid']);
			table_home_follow::t()->delete_by_followuid($member['uid']);
			// 好友关系以及好友请求清理
			table_home_friend::t()->delete_by_uid_fuid($member['uid']);
			table_home_friend_request::t()->delete_by_uid_or_fuid($member['uid']);
			// 动态清理
			table_home_feed::t()->delete_by_uid($member['uid']);
			// 通知清理
			table_home_notification::t()->delete_by_uid($member['uid']);
			// 打招呼清理
			table_home_poke::t()->delete_by_uid_or_fromuid($member['uid']);
			table_home_pokearchive::t()->delete_by_uid_or_fromuid($member['uid']);
			// 论坛推广清理
			table_forum_promotion::t()->delete_by_uid($member['uid']);
		}

		if(in_array('avatar', $_GET['clear'])) {
			loaducenter();
			C::t('common_member'.$tableext)->update($member['uid'], array('avatarstatus'=>0));
			uc_user_deleteavatar($member['uid']);
		}

		if($membercount) {
			DB::update('common_member_count'.$tableext, $membercount, "uid='{$member['uid']}'");
		}

	}

	cpmsg('members_edit_succeed', 'action=members&operation=ban&uid='.$member['uid'], 'succeed');

}
	