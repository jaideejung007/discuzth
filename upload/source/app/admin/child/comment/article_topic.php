<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($operation == 'article' || $operation == 'topic') {

	$aid = $_GET['aid'];
	$subject = $_GET['subject'];
	$idtype = $operation == 'article' ? 'aid' : 'topicid';
	$tablename = $idtype == 'aid' ? 'portal_article_title' : 'portal_topic';

	if(!submitcheck('articlesubmit')) {

		$starttime = !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $starttime) ? dgmdate(TIMESTAMP - 86400 * 7, 'Y-n-j') : $starttime;
		$endtime = $_G['adminid'] == 3 || !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $endtime) ? dgmdate(TIMESTAMP, 'Y-n-j') : $endtime;

		shownav('topic', 'nav_comment');
		showsubmenu('nav_comment', [
			['comment_comment', 'comment', 0],
			['comment_article_comment', 'comment&operation=article', $operation == 'article' ? 1 : 0],
			['comment_topic_comment', 'comment&operation=topic', $operation == 'topic' ? 1 : 0]
		]);
		/*search={"nav_comment":"action=comment","comment_article_comment":"action=comment&operation=article","comment_topic_comment":"action=comment&operation=topic"}*/
		showtips('comment_'.$operation.'_tips');
		$staticurl = STATICURL;
		echo <<<EOT
	<script type="text/javascript" src="{$staticurl}js/calendar.js"></script>
	<script type="text/JavaScript">
	function page(number) {
		$('articleforum').page.value=number;
		$('articleforum').searchsubmit.click();
	}
	</script>
EOT;
		showtagheader('div', 'searchposts', !$searchsubmit);
		showformheader("comment&operation=$operation", '', 'articleforum');
		showhiddenfields(['page' => $page, 'pp' => $_GET['pp'] ? $_GET['pp'] : $_GET['perpage']]);
		showtableheader();
		showsetting('comment_search_perpage', '', $_GET['perpage'], "<select name='perpage'><option value='20'>{$lang['perpage_20']}</option><option value='50'>{$lang['perpage_50']}</option><option value='100'>{$lang['perpage_100']}</option></select>");
		showsetting("comment_{$operation}_subject", 'subject', $subject, 'text');
		showsetting("comment_{$operation}_id", 'aid', $aid, 'text');
		showsetting('comment_search_message', 'message', $message, 'text');
		showsetting('comment_search_author', 'author', $author, 'text');
		showsetting('comment_search_authorid', 'authorid', $authorid, 'text');
		showsetting('comment_search_time', ['starttime', 'endtime'], [$starttime, $endtime], 'daterange');
		showsubmit('searchsubmit');
		showtablefooter();
		showformfooter();
		showtagfooter('div');
		/*search*/

	} else {


		$commentnum = [];
		foreach(table_portal_comment::t()->fetch_all($_GET['delete']) as $value) {
			$commentnum[$value['idtype']][$value['id']] = $value['id'];
		}
		if($commentnum['aid']) {
			table_portal_article_count::t()->increase($commentnum['aid'], ['commentnum' => -1]);
		} elseif($commentnum['topicid']) {
			table_portal_topic::t()->increase($commentnum['topicid'], ['commentnum' => -1]);
		}
		table_portal_comment::t()->delete($_GET['delete']);
		$cpmsg = cplang('comment_article_delete');

		?>
		<script type="text/JavaScript">alert('<?php echo $cpmsg;?>');
			parent.$('articleforum').searchsubmit.click();</script>
		<?php
	}

	if(submitcheck('searchsubmit')) {

		$comments = $commentcount = '0';
		$sql = $error = '';
		$author = trim($author);

		$queryAId = $aid ? [$aid] : [];

		if($subject != '') {

			$ids = [];
			$query = C::t($tablename)->fetch_all_by_title($idtype, $subject);
			foreach($query as $value) {
				$ids[] = intval($value[$idtype]);
			}
			$queryAId = array_merge($queryAId, $ids);
		}


		$queryAuthorIDs = $authorid ? [$authorid] : [];

		if($author != '') {
			$authorids = table_common_member::t()->fetch_all_uid_by_username(array_map('trim', explode(',', $author)));
			$queryAuthorIDs = array_merge($queryAuthorIDs, $authorids);
		}


		if($starttime != '0') {
			$starttime = strtotime($starttime);
		}

		$sqlendtime = '';

		if($_G['adminid'] == 1 && $endtime != dgmdate(TIMESTAMP, 'Y-n-j')) {
			if($endtime != '0') {
				$sqlendtime = $endtime = strtotime($endtime);
			}
		} else {
			$endtime = TIMESTAMP;
		}

		if(($_G['adminid'] == 2 && $endtime - $starttime > 86400 * 16) || ($_G['adminid'] == 3 && $endtime - $starttime > 86400 * 8)) {
			$error = 'comment_mod_range_illegal';
		}


		if(!$error) {

			$commentcount = table_portal_comment::t()->count_all_by_search($queryAId, $queryAuthorIDs, $starttime, $sqlendtime, $idtype, $message);
			if($commentcount) {
				$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
				$perpage = $_GET['pp'] ? $_GET['pp'] : $_GET['perpage'];
				$query = table_portal_comment::t()->fetch_all_by_search($queryAId, $queryAuthorIDs, $starttime, $sqlendtime, $idtype, $message, (($page - 1) * $perpage), $perpage);

				$comments = '';

				$mod = $idtype == 'aid' ? 'view' : 'topic';
				foreach($query as $comment) {
					$comment['dateline'] = dgmdate($comment['dateline']);
					$comments .= showtablerow('', '', [
						"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$comment['cid']}\" />",
						"<a href=\"portal.php?mod=$mod&$idtype={$comment['id']}\" target=\"_blank\">{$comment['title']}</a>",
						$comment['message'],
						"<a href=\"home.php?mod=space&uid={$comment['uid']}\" target=\"_blank\">{$comment['username']}</a>",
						$comment['dateline']
					], TRUE);
				}

				$multi = multi($commentcount, $perpage, $page, ADMINSCRIPT."?action=comment&operation=$operation");
				$multi = preg_replace("/href=\"".ADMINSCRIPT."\?action=comment&operation=$operation&amp;page=(\d+)\"/", "href=\"javascript:page(\\1)\"", $multi);
				$multi = str_replace("window.location='".ADMINSCRIPT."?action=comment&amp;operation=$operation&amp;page='+this.value", 'page(this.value)', $multi);

			} else {
				$error = 'comment_post_nonexistence';
			}
		}

		showtagheader('div', 'postlist', $searchsubmit);
		showformheader('comment&operation='.$operation.'&frame=no', 'target="articleframe"');
		showtableheader(cplang('comment_result').' '.$commentcount.' <a href="###" onclick="$(\'searchposts\').style.display=\'\';$(\'postlist\').style.display=\'none\';$(\'articleforum\').pp.value=\'\';$(\'articleforum\').page.value=\'\';" class="act lightlink normal">'.cplang('research').'</a>', 'fixpadding');

		if($error) {
			echo "<tr><td class=\"lineheight\" colspan=\"15\">$lang[$error]</td></tr>";
		} else {
			showsubtitle(['', 'article_title', 'message', 'author', 'time']);
			echo $comments;
		}

		showsubmit('articlesubmit', 'delete', 'del', '', $multi);
		showtablefooter();
		showformfooter();
		echo '<iframe name="articleframe" style="display:none"></iframe>';
		showtagfooter('div');

	}
}
