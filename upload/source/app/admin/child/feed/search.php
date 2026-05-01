<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$detail = $_GET['detail'];
$uid = $_GET['uid'];
$users = $_GET['users'];
$feedid = $_GET['feedid'];
$icon = $_GET['icon'];
$hot1 = $_GET['hot1'];
$hot2 = $_GET['hot2'];
$starttime = $_GET['starttime'];
$endtime = $_GET['endtime'];
$searchsubmit = $_GET['searchsubmit'];
$feedids = $_GET['feedids'];

$fromumanage = $_GET['fromumanage'] ? 1 : 0;

showtips('feed_tips');
if(!submitcheck('feedsubmit')) {

	if($fromumanage) {
		$starttime = !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $starttime) ? '' : $starttime;
		$endtime = $_G['adminid'] == 3 || !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $endtime) ? '' : $endtime;
	} else {
		$starttime = !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $starttime) ? dgmdate(TIMESTAMP - 86400 * 7, 'Y-n-j') : $starttime;
		$endtime = $_G['adminid'] == 3 || !preg_match('/^(0|\d{4}\-\d{1,2}\-\d{1,2})$/', $endtime) ? dgmdate(TIMESTAMP, 'Y-n-j') : $endtime;
	}

	$staticurl = STATICURL;

	echo <<<EOT
	<script type="text/javascript" src="{$staticurl}js/calendar.js"></script>
	<script type="text/JavaScript">
	function page(number) {
		$('feedforum').page.value=number;
		$('feedforum').searchsubmit.click();
	}
	</script>
EOT;
	showtagheader('div', 'searchposts', !$searchsubmit);
	showformheader('feed', '', 'feedforum');
	showhiddenfields(['page' => $page, 'pp' => $_GET['pp'] ? $_GET['pp'] : $_GET['perpage']]);
	showtableheader();
	showsetting('feed_search_detail', 'detail', $detail, 'radio');
	showsetting('feed_search_perpage', '', $_GET['perpage'], "<select name='perpage'><option value='20'>{$lang['perpage_20']}</option><option value='50'>{$lang['perpage_50']}</option><option value='100'>{$lang['perpage_100']}</option></select>");
	$selected[$icon] = $icon ? 'selected="selected"' : '';
	showsetting('feed_search_icon', '', $icon, "<select name='icon'><option value=''>{$lang['all']}</option><option value='blog' {$selected['blog']}>{$lang['feed_blog']}</option>
			<option value='thread' {$selected['thread']}>{$lang['feed_thread']}</option><option value='album' {$selected['album']}>{$lang['feed_album']}</option><option value='doing' {$selected['doing']}>{$lang['doing']}</option>
			<option value='share' {$selected['share']}>{$lang['shares']}</option><option value='friend' {$selected['friend']}>{$lang['feed_friend']}</option><option value='poll' {$selected['poll']}>{$lang['feed_poll']}</option>
			<option value='comment' {$selected['comment']}>{$lang['feed_comment']}</option><option value='click' {$selected['click']}>{$lang['feed_click']}</option>
			<option value='show' {$selected['show']}>{$lang['feed_show']}</option><option value='profile' {$selected['profile']}>{$lang['feed_profile']}</option><option value='sitefeed' {$selected['sitefeed']}>{$lang['feed_sitefeed']}</option></select>");
	showsetting('feed_search_uid', 'uid', $uid, 'text');
	showsetting('feed_search_user', 'users', $users, 'text');
	showsetting('feed_search_feedid', 'feedid', $feedid, 'text');
	showsetting('feed_search_hot', ['hot1', 'hot2'], ['', ''], 'range');
	showsetting('feed_search_time', ['starttime', 'endtime'], [$starttime, $endtime], 'daterange');
	echo '<input type="hidden" name="fromumanage" value="'.$fromumanage.'">';
	showsubmit('searchsubmit');
	showtablefooter();
	showformfooter();
	showtagfooter('div');

} else {
	$feedids = authcode($feedids, 'DECODE');
	$feedidsadd = $feedids ? explode(',', $feedids) : $_GET['delete'];
	include_once libfile('function/delete');
	$deletecount = count(deletefeeds($feedidsadd));
	$cpmsg = cplang('feed_succeed', ['deletecount' => $deletecount]);

	?>
	<script type="text/JavaScript">alert('<?php echo $cpmsg;?>');
		parent.$('feedforum').searchsubmit.click();</script>
	<?php

}

if(submitcheck('searchsubmit', 1)) {

	$feedids = $feedcount = '0';
	$sql = $error = '';
	$users = trim($users);

	if($users != '') {
		$uids = [-1];
		$query = table_home_feed::t()->fetch_uid_by_username(explode(',', $users));
		$uids = array_keys($query) + $uids;
	}

	if($icon != '') {
		$feedarr = table_home_feed::t()->fetch_icon_by_icon($icon);
		$icon = $feedarr['icon'];
		if($icon == '') {
			$icon = '-1';
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

	if($feedid != '') {
		$feedids = [-1];
		$query = table_home_feed::t()->fetch_feedid_by_feedid(explode(',', $feedid));
		$feedids = array_keys($query) + $feedids;
	}

	if($uid != '') {
		$query = table_home_feed::t()->fetch_uid_by_uid(explode(',', $uid));
		if(!$uids) {
			$uids = array_keys($query);
		} else {
			$uids = array_intersect(array_keys($query), $uids);
		}
		if(!$uids) {
			$uids = [-1];
		}
	}


	if(($_G['adminid'] == 2 && $endtime - $starttime > 86400 * 16) || ($_G['adminid'] == 3 && $endtime - $starttime > 86400 * 8)) {
		$error = 'feed_mod_range_illegal';
	}

	if(!$error) {
		if($detail) {
			$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
			$perpage = $_GET['pp'] ? $_GET['pp'] : $_GET['perpage'];
			$query = table_home_feed::t()->fetch_all_by_search(1, $uids, $icon, $starttime, $endtime, $feedids, $hot1, $hot2, (($page - 1) * $perpage), $perpage);
			$feeds = '';
			include_once libfile('function/feed');
			foreach($query as $feed) {
				$feed['dateline'] = dgmdate($feed['dateline']);

				$feed = mkfeed($feed);

				$feeds .= showtablerow('', ['style="width:20px;"', 'style="width:260px;"', '', 'style="width:120px;"', 'style="width:60px;"'], [
					"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$feed['feedid']}\" />",
					$feed['title_template'],
					$feed['body_template'],
					$feed['dateline'],
					'<a href="'.ADMINSCRIPT.'?action=feed&operation=global&feedid='.$feed['feedid'].'">'.$lang['edit'].'</a>'
				], TRUE);
			}
			$feedcount = table_home_feed::t()->fetch_all_by_search(3, $uids, $icon, $starttime, $endtime, $feedids, $hot1, $hot2);
			$multi = multi($feedcount, $perpage, $page, ADMINSCRIPT.'?action=feed');
			$multi = preg_replace("/href=\"".ADMINSCRIPT."\?action=feed&amp;page=(\d+)\"/", "href=\"javascript:page(\\1)\"", $multi);
			$multi = str_replace("window.location='".ADMINSCRIPT."?action=feed&amp;page='+this.value", 'page(this.value)', $multi);
		} else {
			$feedcount = 0;
			$query = table_home_feed::t()->fetch_all_by_search(2, $uids, $icon, $starttime, $endtime, $feedids, $hot1, $hot2);
			foreach($query as $feed) {
				$feedids .= ','.$feed['feedid'];
				$feedcount++;
			}
			$multi = '';
		}

		if(!$feedcount) {
			$error = 'feed_post_nonexistence';
		}
	}

	showtagheader('div', 'postlist', $searchsubmit);
	showformheader('feed&frame=no', 'target="feedframe"');
	showhiddenfields(['feedids' => authcode($feedids, 'ENCODE')]);
	showtableheader(cplang('feed_result').' '.$feedcount.' <a href="###" onclick="$(\'searchposts\').style.display=\'\';$(\'postlist\').style.display=\'none\';$(\'feedforum\').pp.value=\'\';$(\'feedforum\').page.value=\'\';" class="act lightlink normal">'.cplang('research').'</a>', 'fixpadding');

	if($error) {
		echo "<tr><td class=\"lineheight\" colspan=\"15\">$lang[$error]</td></tr>";
	} else {
		if($detail) {
			showsubtitle(['', 'feed_title', 'feed_body', 'time', '']);
			echo $feeds;
		}
	}

	showsubmit('feedsubmit', 'delete', $detail ? 'del' : '', '', $multi);
	showtablefooter();
	showformfooter();
	echo '<iframe name="feedframe" style="display:none"></iframe>';
	showtagfooter('div');

}
	