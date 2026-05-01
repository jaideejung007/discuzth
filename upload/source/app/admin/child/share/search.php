<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$uids = $sids = $sharecount = 0;
$sql = $error = '';
$users = trim($users);
$uids = [];

if($users != '') {
	foreach(table_home_share::t()->fetch_all_by_username(explode(',', str_replace(' ', '', $users))) as $arr) {
		$uids[$arr['uid']] = $arr['uid'];
	}
	if(!$uids) {
		$uids = [-1];
	}
	$sql .= " AND s.uid IN ($uids)";
}

if($type != '') {
	$arr = table_home_share::t()->fetch_by_type($type);
	$type = $arr['type'];
}

if($starttime != '') {
	$starttime = strtotime($starttime);
	$sql .= " AND s.dateline>'$starttime'";
}

if($_G['adminid'] == 1 && $endtime != dgmdate(TIMESTAMP, 'Y-n-j')) {
	if($endtime != '') {
		$endtime = strtotime($endtime);
		$sql .= " AND s.dateline<'$endtime'";
	}
} else {
	$endtime = TIMESTAMP;
}

if($sid != '') {
	$sids = [];
	foreach(table_home_share::t()->fetch_all(explode(',', str_replace(' ', '', $sid))) as $fidarr) {
		$sids[] = $fidarr['sid'];
	}
	if(!$sids) {
		$sids = [-1];
	}
	$sql .= " AND  s.sid IN ($sids)";
}

if($uid != '') {
	$uidtmp = [];
	foreach(table_home_share::t()->fetch_all_by_uid(explode(',', str_replace(' ', '', $uid))) as $uidarr) {
		$uidtmp[$uidarr['uid']] = $uidarr['uid'];
	}
	if($uids && $uids[0] != -1) {
		$uids = array_intersect($uids, $uidtmp);
	} else {
		$uids = $uidtmp;
	}
	if(!$uids) {
		$uids = [-1];
	}
}

$sql .= $hot1 ? " AND s.hot >= '$hot1'" : '';
$sql .= $hot2 ? " AND s.hot <= '$hot2'" : '';

if(($_G['adminid'] == 2 && $endtime - $starttime > 86400 * 16) || ($_G['adminid'] == 3 && $endtime - $starttime > 86400 * 8)) {
	$error = 'share_mod_range_illegal';
}

if(!$error) {
	if($detail) {
		$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
		$perpage = $_GET['pp'] ? $_GET['pp'] : $_GET['perpage'];
		$sharecount = table_home_share::t()->count_by_search($sids, $uids, $type, $starttime, $endtime, $hot1, $hot2);
		if($sharecount) {
			$shares = '';
			require_once libfile('function/share');

			$start = ($page - 1) * $perpage;
			foreach(table_home_share::t()->fetch_all_search($sids, $uids, $type, $starttime, $endtime, $hot1, $hot2, $start, $perpage) as $share) {
				$share['dateline'] = dgmdate($share['dateline']);
				$share = mkshare($share);
				$shares .= showtablerow('', ['', 'style="width:80px;"', 'style="width:150px;"', 'style="width:500px;"'], [
					"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$share['sid']}\" />",
					"<a href=\"home.php?mod=space&uid={$share['uid']}\" target=\"_blank\">".$share['username'].'</a>',
					$share['title_template'],
					$share['body_template'],
					$share['dateline']
				], TRUE);
			}
			$multi = multi($sharecount, $perpage, $page, ADMINSCRIPT.'?action=share');
			$multi = preg_replace("/href=\"".ADMINSCRIPT."\?action=share&amp;page=(\d+)\"/", "href=\"javascript:page(\\1)\"", $multi);
			$multi = str_replace("window.location='".ADMINSCRIPT."?action=share&amp;page='+this.value", 'page(this.value)', $multi);
		}
	} else {
		$sharecount = 0;
		foreach(table_home_share::t()->fetch_all_search($sids, $uids, $type, $starttime, $endtime, $hot1, $hot2) as $share) {
			$sids .= ','.$share['sid'];
			$sharecount++;
		}
		$multi = '';
	}

	if(!$sharecount) {
		$error = 'share_post_nonexistence';
	}
}

showtagheader('div', 'postlist', $searchsubmit || $newlist);
showformheader('share&frame=no', 'target="shareframe"');
showhiddenfields(['sids' => authcode($sids, 'ENCODE')]);
showtableheader(cplang('share_result').' '.$sharecount.(empty($newlist) ? ' <a href="###" onclick="$(\'searchposts\').style.display=\'\';$(\'postlist\').style.display=\'none\';$(\'shareforum\').pp.value=\'\';$(\'shareforum\').page.value=\'\';" class="act lightlink normal">'.cplang('research').'</a>' : ''), 'fixpadding');

if($error) {
	echo "<tr><td class=\"lineheight\" colspan=\"15\">$lang[$error]</td></tr>";
} else {
	if($detail) {
		showsubtitle(['', 'author', 'share_title', 'share_body', 'time']);
		echo $shares;
	}
}

showsubmit('sharesubmit', 'delete', $detail ? 'del' : '', '', $multi);
showtablefooter();
showformfooter();
echo '<iframe name="shareframe" style="display:none"></iframe>';
showtagfooter('div');
	