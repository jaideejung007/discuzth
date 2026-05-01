<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$doids = $doingcount = '0';
$sql = $error = '';
$keywords = trim($keywords);
$users = trim($users);

if($users != '') {
	$uids = table_common_member::t()->fetch_all_uid_by_username(array_map('trim', explode(',', $users)));
	if(!$uids) {
		$uids = [-1];
	}
}

if(($_G['adminid'] == 2 && $endtime - $starttime > 86400 * 16) || ($_G['adminid'] == 3 && $endtime - $starttime > 86400 * 8)) {
	$error = 'prune_mod_range_illegal';
}

if(!($_G['adminid'] == 1 && $endtime != dgmdate(TIMESTAMP, 'Y-n-j'))) {
	$endtime = TIMESTAMP;
}

if(!$error) {
	if($detail) {
		$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
		$perpage = $_GET['pp'] ? $_GET['pp'] : $_GET['perpage'];
		$query = table_home_doing::t()->fetch_all_search((($page - 1) * $perpage), $perpage, 1, $uids, $userip, $keywords, $lengthlimit, $starttime, $endtime);
		$doings = '';

		foreach($query as $doing) {
			$doing['dateline'] = dgmdate($doing['dateline']);
			$doings .= showtablerow('', '', [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$doing['doid']}\"  />",
				"<a href=\"home.php?mod=space&uid={$doing['uid']}\" target=\"_blank\">{$doing['username']}</a>",
				$doing['message'],
				$doing['ip'],
				$doing['dateline']
			], TRUE);
		}
		$doingcount = table_home_doing::t()->fetch_all_search((($page - 1) * $perpage), $perpage, 3, $uids, $userip, $keywords, $lengthlimit, $starttime, $endtime);
		$multi = multi($doingcount, $perpage, $page, ADMINSCRIPT.'?action=doing');
		$multi = preg_replace("/href=\"".ADMINSCRIPT."\?action=doing&amp;page=(\d+)\"/", "href=\"javascript:page(\\1)\"", $multi);
		$multi = str_replace("window.location='".ADMINSCRIPT."?action=doing&amp;page='+this.value", 'page(this.value)', $multi);

	} else {
		$doingcount = 0;
		$query = table_home_doing::t()->fetch_all_search((($page - 1) * $perpage), $perpage, 2, $uids, $userip, $keywords, $lengthlimit, $starttime, $endtime);
		foreach($query as $doing) {
			$doids .= ','.$doing['doid'];
			$doingcount++;
		}
		$multi = '';
	}

	if(!$doingcount) {
		$error = 'doing_post_nonexistence';
	}
}

showtagheader('div', 'postlist', $searchsubmit || $newlist);
showformheader('doing&frame=no', 'target="doingframe"');
showhiddenfields(['doids' => authcode($doids, 'ENCODE')]);
if(!$search_tips) {
	showtableheader(cplang('doing_new_result').' '.$doingcount, 'fixpadding');
} else {
	showtableheader(cplang('doing_result').' '.$doingcount.(empty($newlist) ? ' <a href="###" onclick="$(\'searchposts\').style.display=\'\';$(\'postlist\').style.display=\'none\';$(\'doingforum\').pp.value=\'\';$(\'doingforum\').page.value=\'\';" class="act lightlink normal">'.cplang('research').'</a>' : ''), 'fixpadding');
}

if($error) {
	echo "<tr><td class=\"lineheight\" colspan=\"15\">$lang[$error]</td></tr>";
} else {
	if($detail) {
		showsubtitle(['', 'author', 'message', 'ip', 'time']);
		echo $doings;
	}
}

showsubmit('doingsubmit', 'delete', $detail ? 'del' : '', '', $multi);
showtablefooter();
showformfooter();
echo '<iframe name="doingframe" style="display:none"></iframe>';
showtagfooter('div');
