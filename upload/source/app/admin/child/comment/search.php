<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('commentsubmit')) {

	if($fromumanage) {
		$starttime = !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $starttime) ? '' : $starttime;
		$endtime = $_G['adminid'] == 3 || !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $endtime) ? '' : $endtime;
	} else {
		$starttime = !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $starttime) ? dgmdate(TIMESTAMP - 86400 * 7, 'Y-n-j') : $starttime;
		$endtime = $_G['adminid'] == 3 || !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $endtime) ? dgmdate(TIMESTAMP, 'Y-n-j') : $endtime;
	}

	shownav('topic', 'nav_comment');
	showsubmenu('nav_comment', [
		['comment_comment', 'comment', 1],
		['comment_article_comment', 'comment&operation=article', 0],
		['comment_topic_comment', 'comment&operation=topic', 0]
	]);
	/*search={"nav_comment":"action=comment","comment_comment":"action=comment"}*/
	showtips('comment_tips');
	$staticurl = STATICURL;
	echo <<<EOT
	<script type="text/javascript" src="{$staticurl}js/calendar.js"></script>
	<script type="text/JavaScript">
	function page(number) {
		$('commentforum').page.value=number;
		$('commentforum').searchsubmit.click();
	}
	</script>
EOT;
	showtagheader('div', 'searchposts', !$searchsubmit);
	showformheader('comment', '', 'commentforum');
	showhiddenfields(['page' => $page, 'pp' => $_GET['pp'] ? $_GET['pp'] : $_GET['perpage']]);
	showtableheader();
	showsetting('comment_search_detail', 'detail', $detail, 'radio');
	showsetting('comment_search_perpage', '', $_GET['perpage'], "<select name='perpage'><option value='20'>{$lang['perpage_20']}</option><option value='50'>{$lang['perpage_50']}</option><option value='100'>{$lang['perpage_100']}</option></select>");
	showsetting('comment_idtype', ['idtype', [
		['', $lang['all']],
		['uid', $lang['comment_uid']],
		['blogid', $lang['comment_blogid']],
		['picid', $lang['comment_picid']],
		['sid', $lang['comment_sid']],
	]], 'comment_idtype', 'select');
	showsetting('comment_search_id', 'id', $id, 'text');
	showsetting('comment_search_author', 'author', $author, 'text');
	showsetting('comment_search_authorid', 'authorid', $authorid, 'text');
	showsetting('comment_search_uid', 'uid', $uid, 'text');
	showsetting('comment_search_message', 'message', $message, 'text');
	showsetting('comment_search_ip', 'ip', $ip, 'text');
	showsetting('comment_search_time', ['starttime', 'endtime'], [$starttime, $endtime], 'daterange');
	echo '<input type="hidden" name="fromumanage" value="'.$fromumanage.'">';
	showsubmit('searchsubmit');
	showtablefooter();
	showformfooter();
	showtagfooter('div');
	/*search*/

} else {
	$cids = authcode($cids, 'DECODE');
	$cidsadd = $cids ? explode(',', $cids) : $_GET['delete'];
	include_once libfile('function/delete');
	$deletecount = count(deletecomments($cidsadd));
	$cpmsg = cplang('comment_succeed', ['deletecount' => $deletecount]);

	?>
	<script type="text/JavaScript">alert('<?php echo $cpmsg;?>');
		parent.$('commentforum').searchsubmit.click();</script>
	<?php

}

if(submitcheck('searchsubmit', 1)) {

	$comments = $commentcount = '0';
	$sql = $error = '';
	$authorids = [];
	$author = trim($author);

	if($id != '') {
		$id = explode(',', $id);
	}

	if($author != '') {
		$authorids = table_common_member::t()->fetch_all_uid_by_username(array_map('trim', explode(',', $author)));
		if(!$authorids) {
			$authorids = [-1];
		}
	}

	$authorid = trim($authorid, ', ');
	if($authorid != '') {
		if(!$authorids) {
			$authorids = explode(',', $authorid);
		} else {
			$authorids = array_intersect($authorids, explode(',', $authorid));
		}
		if(!$authorids) {
			$authorids = [-1];
		}
	}

	if($_G['adminid'] == 1 && $endtime != dgmdate(TIMESTAMP, 'Y-n-j')) {
		if($endtime != '') {
			$endtime = strtotime($endtime);
		}
	} else {
		$endtime = TIMESTAMP;
	}

	if(($_G['adminid'] == 2 && $endtime - $starttime > 86400 * 16) || ($_G['adminid'] == 3 && $endtime - $starttime > 86400 * 8)) {
		$error = 'comment_mod_range_illegal';
	}

	$uid = trim($uid, ', ');
	if($uid != '') {
		$uid = explode(',', $uid);
	}

	if(!$error) {
		if($commentcount = table_home_comment::t()->fetch_all_search(3, $id, $authorids, $uid, $ip, $message, $idtype, $starttime, $endtime)) {
			if($detail) {
				$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
				$perpage = $_GET['pp'] ? $_GET['pp'] : $_GET['perpage'];
				$query = table_home_comment::t()->fetch_all_search(1, $id, $authorids, $uid, $ip, $message, $idtype, $starttime, $endtime, (($page - 1) * $perpage), $perpage);
				$comments = '';

				foreach($query as $comment) {
					$comment['dateline'] = dgmdate($comment['dateline']);
					switch($comment['idtype']) {
						case 'picid':
							$address = "<a href=\"home.php?mod=space&uid={$comment['uid']}&do=album&picid={$comment['id']}\" target=\"_blank\">{$comment['message']}</a>";
							break;
						case 'uid':
							$address = "<a href=\"home.php?mod=space&uid={$comment['uid']}&do=wall\" target=\"_blank\">{$comment['message']}</a>";
							break;
						case 'sid':
							$address = "<a href=\"home.php?mod=space&uid=1&do=share&id={$comment['id']}\" target=\"_blank\">{$comment['message']}</a>";
							break;
						case 'blogid':
							$address = "<a href=\"home.php?mod=space&uid={$comment['uid']}&do=blog&id={$comment['id']}\" target=\"_blank\">{$comment['message']}</a>";
							break;
					}
					$comments .= showtablerow('', '', [
						"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$comment['cid']}\" />",
						$address,
						"<a href=\"home.php?mod=space&uid={$comment['uid']}\" target=\"_blank\">{$comment['author']}</a>",
						$comment['ip'],
						$comment['idtype'],
						$comment['dateline']
					], TRUE);
				}
				$multi = multi($commentcount, $perpage, $page, ADMINSCRIPT.'?action=comment');
				$multi = preg_replace("/href=\"".ADMINSCRIPT."\?action=comment&amp;page=(\d+)\"/", "href=\"javascript:page(\\1)\"", $multi);
				$multi = str_replace("window.location='".ADMINSCRIPT."?action=comment&amp;page='+this.value", 'page(this.value)', $multi);
			} else {
				$query = table_home_comment::t()->fetch_all_search(2, $id, $authorids, $uid, $ip, $message, $idtype, $starttime, $endtime);
				foreach($query as $comment) {
					$cids .= ','.$comment['cid'];
				}
			}
		} else
			$error = 'comment_post_nonexistence';
	}

	showtagheader('div', 'postlist', $searchsubmit);
	showformheader('comment&frame=no', 'target="commentframe"');
	showhiddenfields(['cids' => authcode($cids, 'ENCODE')]);
	showtableheader(cplang('comment_result').' '.$commentcount.' <a href="###" onclick="$(\'searchposts\').style.display=\'\';$(\'postlist\').style.display=\'none\';$(\'commentforum\').pp.value=\'\';$(\'commentforum\').page.value=\'\';" class="act lightlink normal">'.cplang('research').'</a>', 'fixpadding');

	if($error) {
		echo "<tr><td class=\"lineheight\" colspan=\"15\">$lang[$error]</td></tr>";
	} else {
		if($detail) {
			showsubtitle(['', 'message', 'author', 'ip', 'comment_idtype', 'time']);
			echo $comments;
		}
	}

	showsubmit('commentsubmit', 'delete', $detail ? 'del' : '', '', $multi);
	showtablefooter();
	showformfooter();
	echo '<iframe name="commentframe" style="display:none"></iframe>';
	showtagfooter('div');

}
	