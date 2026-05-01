<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$comments = $commentcount = '0';
$sql = $error = '';
$author = trim($author);

if($author != '') {
	$authorids = table_common_member::t()->fetch_all_uid_by_username(array_map('trim', explode(',', $author)));
	$authorid = ($authorid ? $authorid.',' : '').implode(',', $authorids);
}
$authorid = trim($authorid, ', ');

if($starttime != '0') {
	$starttime = strtotime($starttime);
}

if($_G['adminid'] == 1 && $endtime != dgmdate(TIMESTAMP, 'Y-n-j')) {
	if($endtime != '0') {
		$endtime = strtotime($endtime);
	}
} else {
	$endtime = TIMESTAMP;
}


if(($_G['adminid'] == 2 && $endtime - $starttime > 86400 * 16) || ($_G['adminid'] == 3 && $endtime - $starttime > 86400 * 8)) {
	$error = 'comment_mod_range_illegal';
}


if(!$error) {
	if($detail) {
		$commentcount = table_forum_postcomment::t()->count_by_search($searchtid, $searchpid, ($authorid ? explode(',', str_replace(' ', '', $authorid)) : null), $starttime, $endtime, $ip, $message);
		if($commentcount) {
			$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
			$perpage = $_GET['pp'] ? $_GET['pp'] : $_GET['perpage'];

			$comments = '';

			foreach(table_forum_postcomment::t()->fetch_all_by_search($searchtid, $searchpid, ($authorid ? explode(',', str_replace(' ', '', $authorid)) : null), $starttime, $endtime, $ip, $message, (($page - 1) * $perpage), $perpage) as $comment) {
				$comment['dateline'] = dgmdate($comment['dateline']);
				$comments .= showtablerow('', '', [
					"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$comment['id']}\" />",
					str_replace(['[b]', '[/b]', '[/color]'], ['<b>', '</b>', '</font>'], preg_replace('/\[color=([#\w]+?)\]/i', "<font color=\"\\1\">", $comment['comment'])),
					($comment['author'] ? "<a href=\"home.php?mod=space&uid={$comment['authorid']}\" target=\"_blank\">".$comment['author'].'</a>' : cplang('postcomment_guest')),
					$comment['dateline'],
					$comment['useip'],
					"<a href=\"forum.php?mod=redirect&goto=findpost&ptid={$comment['tid']}&pid={$comment['pid']}\" target=\"_blank\">".cplang('postcomment_pid').'</a>'
				], TRUE);
			}

			$multi = multi($commentcount, $perpage, $page, ADMINSCRIPT.'?action=postcomment');
			$multi = preg_replace("/href=\"".ADMINSCRIPT."\?action=postcomment&amp;page=(\d+)\"/", "href=\"javascript:page(\\1)\"", $multi);
			$multi = str_replace("window.location='".ADMINSCRIPT."?action=postcomment&amp;page='+this.value", 'page(this.value)', $multi);
		} else {
			$error = 'postcomment_nonexistence';
		}
	} else {
		$commentcount = 0;
		foreach(table_forum_postcomment::t()->fetch_all_by_search($searchtid, $searchpid, ($authorid ? explode(',', str_replace(' ', '', $authorid)) : null), $starttime, $endtime, $ip, $message) as $row) {
			$cids .= ','.$row['id'];
			$commentcount++;
		}
		$multi = '';
	}
}

showtagheader('div', 'postlist', $searchsubmit || $newlist);
showformheader('postcomment&frame=no', 'target="postcommentframe"');
showhiddenfields(['cids' => authcode($cids, 'ENCODE')]);
if(!$search_tips) {
	showtableheader(cplang('postcomment_new_result').' '.$commentcount, 'fixpadding');
} else {
	showtableheader(cplang('postcomment_result').' '.$commentcount.(empty($newlist) ? ' <a href="###" onclick="$(\'searchposts\').style.display=\'\';$(\'postlist\').style.display=\'none\';$(\'postcommentforum\').pp.value=\'\';$(\'postcommentforum\').page.value=\'\';" class="act lightlink normal">'.cplang('research').'</a>' : ''), 'fixpadding');
}

if($error) {
	echo "<tr><td class=\"lineheight\" colspan=\"15\">$lang[$error]</td></tr>";
} elseif($detail) {
	showsubtitle(['', 'postcomment_content', 'author', 'time', 'ip', '']);
	echo $comments;
}

showsubmit('postcommentsubmit', 'delete', $detail ? 'del' : '', '', $multi);
showtablefooter();
showformfooter();
echo '<iframe name="postcommentframe" style="display:none"></iframe>';
showtagfooter('div');
	