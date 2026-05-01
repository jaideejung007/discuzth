<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$blogids = $blogcount = '0';
$sql = $error = '';
$keywords = trim($keywords);
$users = trim($users);
$uids = [];

if($blogid != '') {
	$blogid = explode(',', $blogid);
}

if($users != '') {
	$uids = table_common_member::t()->fetch_all_uid_by_username(array_map('trim', explode(',', $users)));
	if(!$uids) {
		$uids = [-1];
	}
}

$uid = trim($uid, ', ');
if($uid != '') {
	$uid = explode(',', $uid);
	if($uids && $uids[0] != -1) {
		$uids = array_intersect($uids, $uid);
	} else {
		$uids = $uid;
	}
	if(!$uids) {
		$uids = [-1];
	}
}

if($starttime != '') {
	$starttime = strtotime($starttime);
}

if($_G['adminid'] == 1 && $endtime != dgmdate(TIMESTAMP, 'Y-n-j')) {
	if($endtime != '') {
		$endtime = strtotime($endtime);
	}
} else {
	$endtime = TIMESTAMP;
}


if(($_G['adminid'] == 2 && $endtime - $starttime > 86400 * 16) || ($_G['adminid'] == 3 && $endtime - $starttime > 86400 * 8)) {
	$error = 'blog_mod_range_illegal';
}

if(!$error) {
	if($detail) {
		$pagetmp = $page;
		$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
		$perpage = $_GET['pp'] ? $_GET['pp'] : $_GET['perpage'];
		do {
			$query = table_home_blog::t()->fetch_all_by_search(1, $blogid, $uids, $starttime, $endtime, $hot1, $hot2, $viewnum1, $viewnum2, $replynum1, $replynum2, $friend, $ip, $keywords, $lengthlimit, $orderby, $ordersc, (($pagetmp - 1) * $perpage), $perpage, null, null, null, null, false, [0, 1]);
			$pagetmp--;
		} while(!count($query) && $pagetmp);
		$blogs = '';
		foreach($query as $blog) {
			$blog['dateline'] = dgmdate($blog['dateline']);
			$blog['subject'] = cutstr($blog['subject'], 30);
			$privacy_name = match ($blog['friend']) {
				'1' => $lang['setting_home_privacy_friend'],
				'2' => $lang['setting_home_privacy_specified_friend'],
				'3' => $lang['setting_home_privacy_self'],
				'4' => $lang['setting_home_privacy_password'],
				default => $lang['setting_home_privacy_alluser'],
			};
			$blog['friend'] = $blog['friend'] ? " <a href=\"".ADMINSCRIPT."?action=blog&friend={$blog['friend']}\">$privacy_name</a>" : $privacy_name;
			$blogs .= showtablerow('', '', [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"ids[]\" value=\"{$blog['blogid']}\" />",
				$blog['blogid'],
				"<a href=\"home.php?mod=space&uid={$blog['uid']}\" target=\"_blank\">{$blog['username']}</a>",
				"<a href=\"home.php?mod=space&uid={$blog['uid']}&do=blog&id={$blog['blogid']}\" target=\"_blank\">{$blog['subject']}</a>",
				$blog['viewnum'],
				$blog['replynum'],
				$blog['hot'],
				$blog['dateline'],
				$blog['friend']
			], TRUE);
		}
		$blogcount = table_home_blog::t()->count_all_by_search($blogid, $uids, $starttime, $endtime, $hot1, $hot2, $viewnum1, $viewnum2, $replynum1, $replynum2, $friend, $ip, $keywords, $lengthlimit, null, null, null, false, [0, 1]);
		$multi = multi($blogcount, $perpage, $page, ADMINSCRIPT.'?action=blog'.($perpage ? '&perpage='.$perpage : '').$muticondition);
	} else {
		$blogcount = 0;
		$query = table_home_blog::t()->fetch_all_by_search(2, $blogid, $uids, $starttime, $endtime, $hot1, $hot2, $viewnum1, $viewnum2, $replynum1, $replynum2, $friend, $ip, $keywords, $lengthlimit, null, null, 0, 0, null, null, null, null, false, [0, 1]);
		foreach($query as $blog) {
			$blogids .= ','.$blog['blogid'];
			$blogcount++;
		}
		$multi = '';
	}

	if(!$blogcount) {
		$error = 'blog_post_nonexistence';
	}
}

showtagheader('div', 'postlist', $searchsubmit || $newlist);
showformheader('blog&frame=no', 'target="blogframe"');
if(!$muticondition) {
	showtableheader(cplang('blog_new_result').' '.$blogcount, 'fixpadding');
} else {
	showtableheader(cplang('blog_result').' '.$blogcount.(empty($newlist) ? ' <a href="###" onclick="$(\'searchposts\').style.display=\'\';$(\'postlist\').style.display=\'none\';$(\'blogforum\').pp.value=\'\';$(\'blogforum\').page.value=\'\';" class="act lightlink normal">'.cplang('research').'</a>' : ''), 'fixpadding');
}

if($error) {
	echo "<tr><td class=\"lineheight\" colspan=\"15\">$lang[$error]</td></tr>";
} else {
	if($detail) {
		showsubtitle(['', 'blogid', 'author', 'subject', 'view', 'reply', 'hot', 'time', 'privacy']);
		echo $blogs;
		$optypehtml = ''
			.'<input type="radio" name="optype" id="optype_delete" value="delete" class="radio" /><label for="optype_delete">'.cplang('delete').'</label>&nbsp;&nbsp;';
		$optypehtml .= '<input type="radio" name="optype" id="optype_move" value="move" class="radio" /><label for="optype_move">'.cplang('article_opmove').'</label> '
			.category_showselect('blog', 'tocatid', false)
			.'&nbsp;&nbsp;';
		showsubmit('', '', '', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'ids\')" /><label for="chkall">'.cplang('select_all').'</label>&nbsp;&nbsp;'.$optypehtml.'<input type="submit" class="btn" name="blogsubmit" value="'.cplang('submit').'" />', $multi);
	} else {
		showhiddenfields(['blogids' => authcode($blogids, 'ENCODE')]);
		showsubmit('blogsubmit', 'delete', $detail ? 'del' : '', '', $multi);
	}
}

showtablefooter();
showformfooter();
echo '<iframe name="blogframe" style="display:none;"></iframe>';
showtagfooter('div');
	