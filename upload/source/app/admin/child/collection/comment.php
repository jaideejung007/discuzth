<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$tagarray = [];
if(submitcheck('submit') && !empty($_GET['cidarray']) && is_array($_GET['cidarray']) && count($_GET['cidarray']) && !empty($_GET['operate_type'])) {
	$class_tag = new tag();
	$cidarray = [];
	$operate_type = $_GET['operate_type'];
	$cidarray = $_GET['cidarray'];
	if($operate_type == 'delete') {
		require_once libfile('function/delete');
		$cidlist = table_forum_collectioncomment::t()->fetch_all($cidarray);
		table_forum_collectioncomment::t()->delete_by_cid_ctid($cidarray);
		foreach($cidlist as $uniquecid) {
			$decreasnum[$uniquecid['ctid']]++;
		}
		foreach($decreasnum as $ctid => $num) {
			table_forum_collection::t()->update_by_ctid($ctid, 0, 0, -$num);
		}
	}
	cpmsg('collection_admin_updated', 'action=collection&operation=comment&searchsubmit=yes&perpage='.$_GET['perpage'].'&page='.$_GET['page'], 'succeed');
}
/*search={"collection":"action=collection"}*/
if(!submitcheck('searchsubmit', 1)) {
	showformheader('collection&operation=comment');
	showtableheader();
	showsetting('collection_ctid', 'comment_ctid', $comment_ctid, 'text');
	showsetting('collection_comment_message', 'comment_message', $comment_message, 'text');
	showsetting('collection_comment_cid', 'comment_cid', $comment_cid, 'text');
	showsetting('collection_comment_username', 'comment_username', $comment_username, 'text');
	showsetting('collection_comment_uid', 'comment_uid', $comment_uid, 'text');
	showsetting('collection_comment_rate', 'comment_rate', $comment_rate, 'text');
	showsetting('collection_comment_useip', 'comment_useip', $comment_useip, 'text');
	if(!$fromumanage) {
		empty($_GET['starttime']) && $_GET['starttime'] = date('Y-m-d', time() - 86400 * 30);
	}
	echo '<input type="hidden" name="fromumanage" value="'.$fromumanage.'">';
	showsetting('threads_search_time', ['starttime', 'endtime'], [$_GET['starttime'], $_GET['endtime']], 'daterange');
	showsetting('feed_search_perpage', '', $_GET['perpage'], "<select name='perpage'><option value='20'>{$lang['perpage_20']}</option><option value='50'>{$lang['perpage_50']}</option><option value='100'>{$lang['perpage_100']}</option></select>");
	showsubmit('searchsubmit');
	showtablefooter();
	showformfooter();
	showtagfooter('div');
} else {
	$comment_message = trim($_GET['comment_message']);
	$comment_cid = dintval($_GET['comment_cid']);
	$comment_ctid = dintval($_GET['comment_ctid']);
	$comment_uid = dintval($_GET['comment_uid']);
	$comment_username = trim($_GET['comment_username']);
	$comment_useip = trim($_GET['comment_useip']);
	$comment_rate = dintval($_GET['comment_rate']);
	$starttime = $_GET['starttime'] ? strtotime($_GET['starttime']) : '';
	$endtime = $_GET['endtime'] ? strtotime($_GET['endtime']) : '';

	$ppp = $_GET['perpage'];
	$startlimit = ($page - 1) * $ppp;
	$multipage = '';
	$totalcount = table_forum_collectioncomment::t()->fetch_all_for_search($comment_cid, $comment_ctid, $comment_username, $comment_uid, $comment_useip, $comment_rate, $comment_message, $starttime, $endtime, -1);
	$multipage = multi($totalcount, $ppp, $page, ADMINSCRIPT."?action=collection&operation=comment&searchsubmit=yes&comment_message=$comment_message&comment_cid=$comment_cid&comment_username=$comment_username&comment_uid=$comment_uid&comment_ctid=$comment_ctid&comment_useip=$comment_useip&comment_rate=$comment_rate&starttime=$starttime&endtime=$endtime&perpage=$ppp");
	$collectioncomment = table_forum_collectioncomment::t()->fetch_all_for_search($comment_cid, $comment_ctid, $comment_username, $comment_uid, $comment_useip, $comment_rate, $comment_message, $starttime, $endtime, $startlimit, $ppp);
	showformheader('collection&operation=comment');
	showtableheader(cplang('collection_comment_result').' '.$totalcount.' <a href="###" onclick="location.href=\''.ADMINSCRIPT.'?action=collection&operation=comment\';" class="act lightlink normal">'.cplang('research').'</a>', 'nobottom');
	showhiddenfields(['page' => $_GET['page'], 'tagname' => $tagname, 'status' => $status, 'perpage' => $ppp]);
	showsubtitle(['', 'collection_comment_message', 'collection_comment_cid', 'collection_name', 'collection_comment_username', 'collection_comment_useip', 'collection_comment_ratenum', 'collection_date']);

	$ctidarray = [];
	foreach($collectioncomment as $uniquecomment) {
		$ctidarray[$uniquecomment['ctid']] = 1;
	}
	$ctidarray = array_keys($ctidarray);
	$collectiondata = table_forum_collection::t()->fetch_all($ctidarray);
	foreach($collectioncomment as $uniquecomment) {
		if($uniquecomment['rate'] == 0) $uniquecomment['rate'] = '-';
		showtablerow('', ['class="td25"', 'width=400', ''], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"cidarray[]\" value=\"{$uniquecomment['cid']}\" />",
			$uniquecomment['message'],
			$uniquecomment['cid'],
			"<a href='forum.php?mod=collection&action=view&ctid={$uniquecomment['ctid']}' target='_blank'>{$collectiondata[$uniquecomment['ctid']]['name']}</a>",
			"<a href='home.php?mod=space&uid={$uniquecomment['uid']}' target='_blank'>{$uniquecomment['username']}</a>",
			$uniquecomment['useip'],
			$uniquecomment['rate'],
			dgmdate($uniquecomment['dateline']),
		]);
	}
	showtablerow('', ['class="td25" colspan="3"'], ['<input name="chkall" id="chkall" type="checkbox" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'cidarray\', \'chkall\')" /><label for="chkall">'.cplang('select_all').'</label>']);
	showtablerow('', ['class="td25"', 'colspan="2"'], [
		cplang('operation'),
		'<input class="radio" type="radio" name="operate_type" value="delete"> '.cplang('delete').' '
	]);
	showsubmit('submit', 'submit', '', '', $multipage);
	showtablefooter();
	showformfooter();
}
/*search*/
	