<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('rbsubmit')) {

	$inforum = $_GET['inforum'];
	$authors = $_GET['authors'];
	$keywords = $_GET['keywords'];
	$admins = $_GET['admins'];
	$pstarttime = $_GET['pstarttime'];
	$pendtime = $_GET['pendtime'];
	$mstarttime = $_GET['mstarttime'];
	$mendtime = $_GET['mendtime'];

	$searchsubmit = $_GET['searchsubmit'];

	require_once libfile('function/forumlist');

	$forumselect = '<select name="inforum"><option value="">&nbsp;&nbsp;> '.$lang['select'].'</option>'.
		'<option value="">&nbsp;</option><option value="groupthread">'.$lang['group_thread'].'</option>'.forumselect(FALSE, 0, 0, TRUE).'</select>';

	if($inforum) {
		$forumselect = preg_replace("/(\<option value=\"$inforum\")(\>)/", "\\1 selected=\"selected\" \\2", $forumselect);
	}

	shownav('topic', 'nav_recyclebin');
	showsubmenu('nav_recyclebin', [
		['recyclebin_list', 'recyclebin', 0],
		['search', 'recyclebin&operation=search', 1],
		['clean', 'recyclebin&operation=clean', 0]
	]);
	/*search={"nav_recyclebin":"action=recyclebin","search":"action=recyclebin&operation=search"}*/
	$staticurl = STATICURL;
	echo <<<EOT
<script type="text/javascript" src="{$staticurl}js/calendar.js"></script>
<script type="text/JavaScript">
function page(number) {
	$('rbsearchform').page.value=number;
	$('rbsearchform').searchsubmit.click();
}
</script>
EOT;
	showtagheader('div', 'threadsearch', !$searchsubmit);
	showformheader('recyclebin&operation=search', '', 'rbsearchform');
	showhiddenfields(['page' => $page]);
	showtableheader('recyclebin_search');
	showsetting('recyclebin_search_forum', '', '', $forumselect);
	showsetting('recyclebin_search_author', 'authors', $authors, 'text');
	showsetting('recyclebin_search_keyword', 'keywords', $keywords, 'text');
	showsetting('recyclebin_search_admin', 'admins', $admins, 'text');
	showsetting('recyclebin_search_post_time', ['pstarttime', 'pendtime'], [$pstarttime, $pendtime], 'daterange');
	showsetting('recyclebin_search_mod_time', ['mstarttime', 'mendtime'], [$mstarttime, $mendtime], 'daterange');

	showsubmit('searchsubmit');
	showtablefooter();
	showformfooter();
	showtagfooter('div');
	/*search*/

	if(submitcheck('searchsubmit')) {

		$sql = '';
		$isgroup = $fid = 0;
		if($inforum == 'groupthread') {
			$isgroup = 1;
		} else {
			$fid = $inforum ? $inforum : 0;
		}
		$author = $authors != '' ? explode(' ', $authors) : '';
		$admins = $admins != '' ? explode(' ', $admins) : '';
		$pstarttime = $pstarttime != '' ? strtotime($pstarttime) : '';
		$pendtime = $pendtime != '' ? strtotime($pendtime) : '';
		$mstarttime = $mstarttime != '' ? strtotime($mstarttime) : '';
		$mendtime = $mendtime != '' ? strtotime($mendtime) : '';

		$threadcount = table_forum_thread::t()->count_by_recyclebine($fid, $isgroup, $author, $admins, $pstarttime, $pendtime, $mstarttime, $mendtime, $keywords);

		$pagetmp = $page;

		$multi = multi($threadcount, $_G['ppp'], $page, ADMINSCRIPT.'?action=recyclebin');
		$multi = preg_replace("/href=\"".ADMINSCRIPT."\?action=recyclebin&amp;page=(\d+)\"/", "href=\"javascript:page(\\1)\"", $multi);
		$multi = str_replace("window.location='".ADMINSCRIPT."?action=recyclebin&amp;page='+this.value", 'page(this.value)', $multi);

		echo '<script type="text/JavaScript">var replyreload;function attachimg() {}</script>';
		showtagheader('div', 'threadlist', $searchsubmit);
		showformheader('recyclebin&operation=search&frame=no', 'target="rbframe"', 'rbform');
		showtableheader(cplang('recyclebin_result').' '.$threadcount.' <a href="#" onclick="$(\'threadlist\').style.display=\'none\';$(\'threadsearch\').style.display=\'\';" class="act lightlink normal">'.cplang('research').'</a>', 'fixpadding');

		if($threadcount) {

			$searchresult = table_forum_thread::t()->fetch_all_by_recyclebine($fid, $isgroup, $author, $admins, $pstarttime, $pendtime, $mstarttime, $mendtime, $keywords, ($pagetmp - 1) * $_G['ppp'], $_G['ppp']);

			$issettids = [];
			foreach($searchresult as $thread) {
				$disabledstr = '';
				if(isset($issettids[$thread['tid']])) {
					$disabledstr = 'disabled';
				} else {
					$issettids[$thread['tid']] = $thread['tid'];
				}
				$post = table_forum_post::t()->fetch_threadpost_by_tid_invisible($thread['tid']);
				$thread = array_merge($thread, $post);
				$thread['message'] = discuzcode($thread['message'], $thread['smileyoff'], $thread['bbcodeoff'], sprintf('%00b', $thread['htmlon']), $thread['allowsmilies'], $thread['allowbbcode'], $thread['allowimgcode'], $thread['allowhtml']);
				$thread['moddateline'] = dgmdate($thread['moddateline']);
				$thread['dateline'] = dgmdate($thread['dateline']);
				if($thread['attachment']) {
					require_once libfile('function/attachment');
					foreach(table_forum_attachment_n::t()->fetch_all_by_id('tid:'.$thread['tid'], 'tid', $thread['tid']) as $attach) {
						$_G['setting']['attachurl'] = $attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl'];
						$attach['url'] = $attach['isimage']
							? " {$attach['filename']} (".sizecount($attach['filesize']).")<br /><br /><img src=\"".$_G['setting']['attachurl']."forum/{$attach['attachment']}\" onload=\"if(this.width > 100) {this.resized=true; this.width=100;}\">"
							: "<a href=\"".$_G['setting']['attachurl']."forum/{$attach['attachment']}\" target=\"_blank\">{$attach['filename']}</a> (".sizecount($attach['filesize']).')';
						$thread['message'] .= "<br /><br />{$lang['attachment']}: ".attachtype(fileext($attach['filename'])."\t").$attach['url'];
					}
				}

				showtablerow("id=\"mod_{$thread['tid']}_row1\"", ['rowspan="3" class="rowform threadopt" style="width:80px;"', 'class="threadtitle"'], [
					"<ul class=\"nofloat\"><li><input class=\"radio\" type=\"radio\" name=\"moderate[{$thread['tid']}]\" id=\"mod_{$thread['tid']}_1\" value=\"delete\" ".(empty($disabledstr) ? "checked=\"checked\"" : '')." $disabledstr /><label for=\"mod_{$thread['tid']}_1\">{$lang['delete']}</label></li><li><input class=\"radio\" type=\"radio\" name=\"moderate[{$thread['tid']}]\" id=\"mod_{$thread['tid']}_2\" value=\"undelete\" $disabledstr/><label for=\"mod_{$thread['tid']}_2\">{$lang['undelete']}</label></li><li><input class=\"radio\" type=\"radio\" name=\"moderate[{$thread['tid']}]\" id=\"mod_{$thread['tid']}_3\" value=\"ignore\" $disabledstr/><label for=\"mod_{$thread['tid']}_3\">{$lang['ignore']}</label></li></ul>",
					"<h3><a href=\"forum.php?mod=forumdisplay&fid={$thread['fid']}}\" target=\"_blank\">{$thread['forumname']}</a> &raquo; {$thread['subject']}</h3><p><span class=\"bold\">{$lang['author']}:</span> <a href=\"home.php?mod=space&uid={$thread['authorid']}\" target=\"_blank\">{$thread['author']}</a> &nbsp;&nbsp; <span class=\"bold\">{$lang['time']}:</span> {$thread['dateline']} &nbsp;&nbsp; {$lang['threads_replies']}: {$thread['replies']} {$lang['threads_views']}: {$thread['views']}</p>"
				]);
				showtablerow("id=\"mod_{$thread['tid']}_row2\"", 'colspan="2" style="padding: 10px; line-height: 180%;"', '<div style="overflow: auto; overflow-x: hidden; max-height:120px; height:auto !important; height:120px; word-break: break-all;">'.$thread['message'].'</div>');
				showtablerow("id=\"mod_{$thread['tid']}_row3\"", 'class="threadopt threadtitle" colspan="2"', "{$lang['operator']}: <a href=\"home.php?mod=space&uid={$thread['moduid']}\" target=\"_blank\">{$thread['modusername']}</a> &nbsp;&nbsp; {$lang['recyclebin_delete_time']}: {$thread['moddateline']}&nbsp;&nbsp; {$lang['reason']}: {$thread['reason']}");
			}
		}

		showsubmit('rbsubmit', 'submit', '', '<a href="#rb" onclick="checkAll(\'option\', $(\'rbform\'), \'delete\')">'.cplang('recyclebin_all_delete').'</a> &nbsp;<a href="#rb" onclick="checkAll(\'option\', $(\'rbform\'), \'undelete\')">'.cplang('recyclebin_all_undelete').'</a> &nbsp;<a href="#rb" onclick="checkAll(\'option\', $(\'rbform\'), \'ignore\')">'.cplang('recyclebin_all_ignore').'</a> &nbsp;', $multi);
		showtablefooter();
		showformfooter();
		echo '<iframe name="rbframe" style="display:none"></iframe>';
		showtagfooter('div');

	}

} else {
	$moderate = $_GET['moderate'];
	$moderation = ['delete' => [], 'undelete' => [], 'ignore' => []];
	if(is_array($moderate)) {
		foreach($moderate as $tid => $action) {
			$moderation[$action][] = intval($tid);
		}
	}

	require_once libfile('function/delete');
	$threadsdel = deletethread($moderation['delete']);
	$threadsundel = undeletethreads($moderation['undelete']);
	if($threadsdel || $threadsundel) {
		$cpmsg = cplang('recyclebin_succeed', ['threadsdel' => $threadsdel, 'threadsundel' => $threadsundel]);
	} else {
		$cpmsg = cplang('recyclebin_nothread');
	}

	?>
	<script type="text/JavaScript">alert('<?php echo $cpmsg;?>');
		parent.$('rbsearchform').searchsubmit.click();</script>
	<?php

}
	