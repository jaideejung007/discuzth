<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$albumids = $albumcount = '0';
$sql = $error = '';
$users = trim($users);

if($users != '') {
	$uids = [-1];
	$query = table_home_album::t()->fetch_uid_by_username(explode(',', $users));
	$uids = array_keys($query) + $uids;
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

if($albumid != '') {
	$albumids = explode(',', $albumid);
}

if($uid != '') {
	$query = table_home_album::t()->fetch_uid_by_uid($uid);
	if(!$uids) {
		$uids = array_keys($query);
	} else {
		$uids = array_intersect(array_keys($query), $uids);
	}
	if(!$uids) {
		$uids = [-1];
	}
}

$orderby = $orderby ? $orderby : 'updatetime';
$ordersc = $ordersc ? $ordersc : 'DESC';

if(($_G['adminid'] == 2 && $endtime - $starttime > 86400 * 16) || ($_G['adminid'] == 3 && $endtime - $starttime > 86400 * 8)) {
	$error = 'album_mod_range_illegal';
}

if(!$error) {
	if($detail) {
		$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
		$perpage = $_GET['pp'] ? $_GET['pp'] : $_GET['perpage'];
		$query = table_home_album::t()->fetch_all_by_search(1, $uids, $albumname, false, '', $starttime, $endtime, $albumids, $friend, $orderby, $ordersc, (($page - 1) * $perpage), $perpage);
		$albums = '';

		include_once libfile('function/home');
		foreach($query as $album) {
			if($album['friend'] != 4 && ckfriend($album['uid'], $album['friend'], $album['target_ids'])) {
				$album['pic'] = pic_cover_get($album['pic'], $album['picflag']);
			} else {
				$album['pic'] = STATICURL.'image/common/nopublish.svg';
			}
			$album['updatetime'] = dgmdate($album['updatetime']);
			$privacy_name = match ($album['friend']) {
				'1' => $lang['setting_home_privacy_friend'],
				'2' => $lang['setting_home_privacy_specified_friend'],
				'3' => $lang['setting_home_privacy_self'],
				'4' => $lang['setting_home_privacy_password'],
				default => $lang['setting_home_privacy_alluser'],
			};
			$album['friend'] = $album['friend'] ? " <a href=\"".ADMINSCRIPT."?action=album&friend={$album['friend']}\">$privacy_name</a>" : $privacy_name;
			$albums .= showtablerow('', '', [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"ids[]\" value=\"{$album['albumid']}\" />",
				"<a href=\"home.php?mod=space&uid={$album['uid']}&do=album&id={$album['albumid']}\" target=\"_blank\"><img src='{$album['pic']}' /></a>",
				"<a href=\"home.php?mod=space&uid={$album['uid']}&do=album&id={$album['albumid']}\" target=\"_blank\">{$album['albumname']}</a>",
				"<a href=\"home.php?mod=space&uid={$album['uid']}\" target=\"_blank\">".$album['username'].'</a>',
				$album['updatetime'], "<a href=\"".ADMINSCRIPT."?action=pic&albumid={$album['albumid']}\">".$album['picnum'].'</a>',
				$album['friend']
			], TRUE);
		}
		$albumcount = table_home_album::t()->fetch_all_by_search(3, $uids, $albumname, false, '', $starttime, $endtime, $albumids, $friend);
		$multi = multi($albumcount, $perpage, $page, ADMINSCRIPT."?action=album$muticondition");
	} else {
		$albumcount = 0;
		$query = table_home_album::t()->fetch_all_by_search(2, $uids, $albumname, false, '', $starttime, $endtime, $albumids, $friend);
		foreach($query as $album) {
			$albumids .= ','.$album['albumid'];
			$albumcount++;
		}
		$multi = '';
	}

	if(!$albumcount) {
		$error = 'album_post_nonexistence';
	}
}

showtagheader('div', 'postlist', $searchsubmit || $newlist);
showformheader('album&frame=no', 'target="albumframe"');
if(!$muticondition) {
	showtableheader(cplang('album_new_result').' '.$albumcount, 'fixpadding');
} else {
	showtableheader(cplang('album_result').' '.$albumcount.(empty($newlist) ? ' <a href="###" onclick="$(\'searchposts\').style.display=\'\';$(\'postlist\').style.display=\'none\';$(\'albumforum\').pp.value=\'\';$(\'albumforum\').page.value=\'\';" class="act lightlink normal">'.cplang('research').'</a>' : ''), 'fixpadding');
}

if($error) {
	echo "<tr><td class=\"lineheight\" colspan=\"15\">$lang[$error]</td></tr>";
} else {
	if($detail) {
		showsubtitle(['', 'albumpic', 'albumname', 'author', 'updatetime', 'pic_num', 'privacy']);
		echo $albums;
		$optypehtml = ''
			.'<input type="radio" name="optype" id="optype_delete" value="delete" class="radio" /><label for="optype_delete">'.cplang('delete').'</label>&nbsp;&nbsp;';
		$optypehtml .= '<input type="radio" name="optype" id="optype_move" value="move" class="radio" /><label for="optype_move">'.cplang('article_opmove').'</label> '
			.category_showselect('album', 'tocatid', false)
			.'&nbsp;&nbsp;';
		showsubmit('', '', '', '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'ids\')" /><label for="chkall">'.cplang('select_all').'</label>&nbsp;&nbsp;'.$optypehtml.'<input type="submit" class="btn" name="albumsubmit" value="'.cplang('submit').'" />', $multi);
	} else {
		showhiddenfields(['albumids' => authcode($albumids, 'ENCODE')]);
		showsubmit('albumsubmit', 'delete', $detail ? 'del' : '', '', $multi);
	}
}

showtablefooter();
showformfooter();
echo '<iframe name="albumframe" style="display:none;"></iframe>';
showtagfooter('div');
	