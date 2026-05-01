<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('posttableids');
$posttable = (is_array($_G['cache']['posttableids']) && in_array($_GET['posttableid'], $_G['cache']['posttableids'])) ? $_GET['posttableid'] : 0;

$pids = [];
$postcount = '0';
$sql = $error = '';
$operation == 'group' && $_GET['forums'] = 'isgroup';
$_GET['keywords'] = trim($_GET['keywords']);
$_GET['users'] = trim($_GET['users']);
if(($_GET['starttime'] == '' && $_GET['endtime'] == '' && !$fromumanage) || ($_GET['keywords'] == '' && $_GET['useip'] == '' && $_GET['users'] == '')) {
	$error = 'prune_condition_invalid';
}

if($_G['adminid'] == 1 || $_G['adminid'] == 2) {
	if($_GET['forums'] && $_GET['forums'] != 'isgroup') {
		$fid = $_GET['forums'];
	}
	if($_GET['forums'] == 'isgroup') {
		$isgroup = 1;
	} else {
		$isgroup = 0;
	}
} else {
	$forums = [];
	foreach(table_forum_moderator::t()->fetch_all_by_uid($_G['uid']) as $forum) {
		$forums[] = $forum['fid'];
	}
	$fid = $forums;
}

if($_GET['users'] != '') {
	$uids = table_common_member::t()->fetch_all_uid_by_username(array_map('trim', explode(',', $_GET['users'])));
	$authorid = $uids;
}
if($_GET['useip'] != '') {
	$useip = str_replace('*', '%', $_GET['useip']);
}
if($_GET['keywords'] != '') {
	$keywords = $_GET['keywords'];
}

if($_GET['lengthlimit'] != '') {
	$lengthlimit = intval($_GET['lengthlimit']);
	$len_message = $lengthlimit;
}

if(!empty($_GET['starttime'])) {
	$starttime = strtotime($_GET['starttime']);
}

if($_G['adminid'] == 1 && !empty($_GET['endtime']) && $_GET['endtime'] != dgmdate(TIMESTAMP, 'Y-n-j')) {
	$endtime = strtotime($_GET['endtime']);
} else {
	$endtime = TIMESTAMP;
}
if(($_G['adminid'] == 2 && $endtime - $starttime > 86400 * 16) || ($_G['adminid'] == 3 && $endtime - $starttime > 86400 * 8)) {
	$error = 'prune_mod_range_illegal';
}

if(!$error) {
	if($_GET['detail']) {
		$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
		$perpage = $_GET['pp'] ? $_GET['pp'] : $_GET['perpage'];
		$posts = '';
		$groupsname = $groupsfid = $postlist = [];
		$postlist = table_forum_post::t()->fetch_all_prune_by_search($posttable, $isgroup, $keywords, $len_message, $fid, $authorid, $starttime, $endtime, $useip, true, ($page - 1) * $perpage, $perpage);
		require_once libfile('function/post');
		foreach($postlist as $key => $post) {
			$postfids[$post['fid']] = $post['fid'];
			$post['dateline'] = dgmdate($post['dateline']);
			$post['subject'] = empty($post['subject']) ? cplang('prune_nosubject') : cutstr($post['subject'], 30);
			$post['message'] = dhtmlspecialchars(messagecutstr($post['message'], 50));
			$postlist[$key] = $post;
		}
		if($postfids) {
			$query = table_forum_forum::t()->fetch_all_by_fid($postfids);
			foreach($query as $row) {
				$forumnames[$row['fid']] = $row['name'];
			}
		}
		if($postlist) {
			foreach($postlist as $post) {
				$posts .= showtablerow('', '', [
					"<input class=\"checkbox\" type=\"checkbox\" name=\"pidarray[]\" value=\"{$post['pid']}\" checked />",
					"<a href=\"forum.php?mod=redirect&goto=findpost&pid={$post['pid']}&ptid={$post['tid']}\" target=\"_blank\">{$post['subject']}</a>",
					$post['message'],
					"<a href=\"forum.php?mod=forumdisplay&fid={$post['fid']}\" target=\"_blank\">".$forumnames[$post['fid']].'</a>',
					"<a href=\"home.php?mod=space&uid={$post['authorid']}\" target=\"_blank\">{$post['author']}</a>",
					$post['dateline']
				], TRUE);
			}
		}
		$postcount = table_forum_post::t()->count_prune_by_search($posttable, $isgroup, $keywords, $len_message, $fid, $authorid, $starttime, $endtime, $useip);
		$multi = multi($postcount, $perpage, $page, ADMINSCRIPT.'?action=prune');
		$multi = preg_replace("/href=\"".ADMINSCRIPT."\?action=prune&amp;page=(\d+)\"/", "href=\"javascript:page(\\1)\"", $multi);
		$multi = str_replace("window.location='".ADMINSCRIPT."?action=prune&amp;page='+this.value", 'page(this.value)', $multi);
	} else {
		$postcount = 0;
		foreach(table_forum_post::t()->fetch_all_prune_by_search($posttable, $isgroup, $keywords, $len_message, $fid, $authorid, $starttime, $endtime, $useip, false) as $post) {
			$pids[] = $post['pid'];
			$postcount++;
		}
		$multi = '';
	}

	if(!$postcount) {
		$error = 'prune_post_nonexistence';
	}
}

showtagheader('div', 'postlist', $searchsubmit);
showformheader('prune&frame=no'.($operation ? '&operation='.$operation : ''), 'target="pruneframe"');
showhiddenfields(['pids' => authcode(implode(',', $pids), 'ENCODE'), 'posttableid' => $posttable]);
showtableheader(cplang('prune_result').' '.$postcount.' <a href="###" onclick="$(\'searchposts\').style.display=\'\';$(\'postlist\').style.display=\'none\';$(\'pruneforum\').pp.value=\'\';$(\'pruneforum\').page.value=\'\';" class="act lightlink normal">'.cplang('research').'</a>', 'fixpadding');

if($error) {
	cpmsg($error);
} else {
	if($_GET['detail']) {
		showsubtitle(['', 'subject', 'message', 'forum', 'author', 'time']);
		echo $posts;
	}
}

showsubmit('prunesubmit', 'submit', $_GET['detail'] ? '<input type="checkbox" name="chkall" id="chkall" class="checkbox" checked onclick="checkAll(\'prefix\', this.form, \'pidarray\')" /><label for="chkall">'.cplang('del').'</label>' : '',
	'<input class="checkbox" type="checkbox" name="donotupdatemember" id="donotupdatemember" value="1" checked="checked" /><label for="donotupdatemember"> '.cplang('prune_no_update_member').'</label>', $multi);
showtablefooter();
showformfooter();
echo '<iframe name="pruneframe" style="display:none"></iframe>';
showtagfooter('div');
	