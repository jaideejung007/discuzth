<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$picids = $piccount = '0';
$sql = $error = '';
$users = trim($users);

if($starttime != '') {
	$starttime = strtotime($starttime);
	$sql .= ' AND p.'.DB::field('dateline', $starttime, '>');
}

if($_G['adminid'] == 1 && $endtime != dgmdate(TIMESTAMP, 'Y-n-j')) {
	if($endtime != '') {
		$endtime = strtotime($endtime);
		$sql .= ' AND p.'.DB::field('dateline', $endtime, '<');
	}
} else {
	$endtime = TIMESTAMP;
}

if($picid != '') {
	$picids = '-1';
	$picidsarr = ['-1'];
	$query = table_home_pic::t()->fetch_all(explode(',', str_replace(' ', '', $picid)));
	foreach($query as $arr) {
		$picids .= ",{$arr['picid']}";
		$picidsarr[] = $arr['picid'];
	}
	$sql .= ' AND p.'.DB::field('picid', $picidsarr);
}

if($albumid != '') {
	$albumids = '-1';
	$albumidsarr = ['-1'];
	$query = table_home_album::t()->fetch_all_album(explode(',', $albumid));
	foreach($query as $arr) {
		$albumids .= ",{$arr['albumid']}";
		$albumidsarr[] = $arr['albumid'];
	}
	$sql .= ' AND p.'.DB::field('albumid', $albumidsarr);
}

if($users != '') {
	$uids = '-1';
	$uidsarr = ['-1'];
	$query = table_home_album::t()->fetch_uid_by_username(explode(',', $users));
	foreach($query as $arr) {
		$uids .= ",{$arr['uid']}";
		$uidsarr[] = $arr['uid'];
	}
	$sql .= ' AND p.'.DB::field('uid', $uidsarr);
}

if($postip != '') {
	$sql .= ' AND p.'.DB::field('postip', str_replace('*', '%', $postip), 'like');
}

$sql .= $hot1 ? ' AND p.'.DB::field('hot', $hot1, '>=') : '';
$sql .= $hot2 ? ' AND p.'.DB::field('hot', $hot2, '<=') : '';
$sql .= $title ? ' AND p.'.DB::field('title', '%'.$title.'%', 'like') : '';
$orderby = $orderby ? $orderby : 'dateline';
$ordersc = $ordersc ? "$ordersc" : 'DESC';

if(($_G['adminid'] == 2 && $endtime - $starttime > 86400 * 16) || ($_G['adminid'] == 3 && $endtime - $starttime > 86400 * 8)) {
	$error = 'pic_mod_range_illegal';
}

if(!$error) {
	if($detail) {
		$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
		$perpage = $_GET['pp'] ? $_GET['pp'] : $_GET['perpage'];
		$query = table_home_pic::t()->fetch_all_by_sql('1 '.$sql, 'p.'.DB::order($orderby, $ordersc), (($page - 1) * $perpage), $perpage);
		$pics = '';

		include_once libfile('function/home');
		foreach($query as $pic) {
			$pic['dateline'] = dgmdate($pic['dateline']);
			$pic['pic'] = pic_get($pic['filepath'], 'album', $pic['thumb'], $pic['remote']);
			$pic['albumname'] = empty($pic['albumname']) && empty($pic['albumid']) ? $lang['album_default'] : $pic['albumname'];
			$pic['albumid'] = empty($pic['albumid']) ? -1 : $pic['albumid'];
			$pics .= showtablerow('', '', [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$pic['picid']}\" />",
				"<a href='home.php?mod=space&uid={$pic['uid']}&do=album&picid={$pic['picid']}'  target='_blank'><img src='{$pic['pic']}'/></a>",
				$pic['size'],
				"<a href='home.php?mod=space&uid={$pic['uid']}&do=album&id={$pic['albumid']}'  target='_blank'>{$pic['albumname']}</a>",
				"<a href=\"home.php?mod=space&uid={$pic['uid']}\" target=\"_blank\">".$pic['username'].'</a>',
				$pic['dateline'], "<a href=\"".ADMINSCRIPT."?action=comment&detail=1&searchsubmit=1&idtype=picid&id={$pic['picid']}\">".$lang['pic_comment'].'</a>'
			], TRUE);
		}
		$piccount = table_home_pic::t()->fetch_all_by_sql('1 '.$sql, '', 0, 0, 1);
		$multi = multi($piccount, $perpage, $page, ADMINSCRIPT."?action=pic$muticondition");
	} else {
		$piccount = 0;
		$query = table_home_pic::t()->fetch_all_by_sql('1 '.$sql, '', 0, 0, 0, 0);
		foreach($query as $pic) {
			$picids .= ','.$pic['picid'];
			$piccount++;
		}
		$multi = '';
	}

	if(!$piccount) {
		$error = 'pic_post_nonexistence';
	}
}

showtagheader('div', 'postlist', $searchsubmit || $newlist);
showformheader('pic&frame=no', 'target="picframe"');
showhiddenfields(['picids' => authcode($picids, 'ENCODE')]);
if(!$muticondition) {
	showtableheader(cplang('pic_new_result').' '.$piccount, 'fixpadding');
} else {
	showtableheader(cplang('pic_result').' '.$piccount.(empty($newlist) ? ' <a href="###" onclick="$(\'searchposts\').style.display=\'\';$(\'postlist\').style.display=\'none\';$(\'picforum\').pp.value=\'\';$(\'picforum\').page.value=\'\';" class="act lightlink normal">'.cplang('research').'</a>' : ''), 'fixpadding');
}

if($error) {
	echo "<tr><td class=\"lineheight\" colspan=\"15\">$lang[$error]</td></tr>";
} else {
	if($detail) {
		showsubtitle(['', 'albumpic', 'pic_size', 'albumname', 'author', 'time', 'pic_comment']);
		echo $pics;
	}
}

showsubmit('picsubmit', 'delete', $detail ? 'del' : '', '', $multi);
showtablefooter();
showformfooter();
echo '<iframe name="picframe" style="display:none"></iframe>';
showtagfooter('div');
	