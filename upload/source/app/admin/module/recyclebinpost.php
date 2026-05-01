<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once libfile('function/post');
require_once libfile('function/discuzcode');

$posttableid = intval($_GET['posttableid']);

cpheader();

if(submitcheck('rbsubmit')) {
	require_once childfile('recyclebinpost/submit');
}

require_once childfile('recyclebinpost/form');

function recyclebinpostshowpostlist($fid, $authors, $starttime, $endtime, $keywords, $start_limit, $lpp) {
	global $_G, $lang, $posttableid;

	$tids = $fids = [];

	$postlist = table_forum_post::t()->fetch_all_by_search($posttableid, null, $keywords, -5, $fid, null, ($authors ? explode(',', str_replace(' ', '', $authors)) : null), strtotime($starttime), strtotime($endtime), null, null, $start_limit, $lpp);

	if(empty($postlist)) return false;

	foreach($postlist as $key => $post) {
		$tids[$post['tid']] = $post['tid'];
		$fids[$post['fid']] = $post['fid'];
	}
	foreach(table_forum_thread::t()->fetch_all_by_tid($tids) as $thread) {
		$thread['tsubject'] = $thread['subject'];
		$threadlist[$thread['tid']] = $thread;
	}
	$query = table_forum_forum::t()->fetch_all_by_fid($fids);
	foreach($query as $val) {
		$forum = ['fid' => $val['fid'],
			'forumname' => $val['name'],
			'allowsmilies' => $val['allowsmilies'],
			'allowhtml' => $val['allowhtml'],
			'allowbbcode' => $val['allowbbcode'],
			'allowimgcode' => $val['allowimgcode']
		];
		$forumlist[$forum['fid']] = $forum;
	}

	foreach($postlist as $key => $post) {
		$post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], sprintf('%00b', $post['htmlon']), $forumlist[$post['fid']]['allowsmilies'], $forumlist[$post['fid']]['allowbbcode'], $forumlist[$post['fid']]['allowimgcode'], $forumlist[$post['fid']]['allowhtml']);
		$post['dateline'] = dgmdate($post['dateline']);
		if($post['attachment']) {
			require_once libfile('function/attachment');
			foreach(table_forum_attachment_n::t()->fetch_all_by_id('tid:'.$post['tid'], 'pid', $post['pid']) as $attach) {
				$_G['setting']['attachurl'] = $attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl'];
				$attach['url'] = $attach['isimage']
					? " {$attach['filename']} (".sizecount($attach['filesize']).")<br /><br /><img src=\"".$_G['setting']['attachurl']."forum/{$attach['attachment']}\" onload=\"if(this.width > 100) {this.resized=true; this.width=100;}\">"
					: "<a href=\"".$_G['setting']['attachurl']."forum/{$attach['attachment']}\" target=\"_blank\">{$attach['filename']}</a> (".sizecount($attach['filesize']).')';
				$post['message'] .= "<br /><br />{$lang['attachment']}: ".attachtype(fileext($attach['filename'])."\t").$attach['url'];
			}
		}

		showtablerow("id=\"mod_{$post['pid']}_row1\"", ['rowspan="3" class="rowform threadopt" style="width:80px;"', 'class="threadtitle"'], [
			"<ul class=\"nofloat\"><li><input class=\"radio\" type=\"radio\" name=\"moderate[{$post['pid']}]\" id=\"mod_{$post['pid']}_1\" value=\"delete\" checked=\"checked\" /><label for=\"mod_{$post['pid']}_1\">{$lang['delete']}</label></li><li><input class=\"radio\" type=\"radio\" name=\"moderate[{$post['pid']}]\" id=\"mod_{$post['pid']}_2\" value=\"undelete\" /><label for=\"mod_{$post['pid']}_2\">{$lang['undelete']}</label></li><li><input class=\"radio\" type=\"radio\" name=\"moderate[{$post['pid']}]\" id=\"mod_{$post['pid']}_3\" value=\"ignore\" /><label for=\"mod_{$post['pid']}_3\">{$lang['ignore']}</label></li></ul>",
			"<h3><a href=\"forum.php?mod=forumdisplay&fid={$post['fid']}\" target=\"_blank\">".$forumlist[$post['fid']]['forumname']."</a> &raquo; <a href=\"forum.php?mod=viewthread&tid={$post['tid']}\" target=\"_blank\">".$threadlist[$post['tid']]['tsubject'].'</a>'.($post['subject'] ? ' &raquo; '.$post['subject'] : '')."</h3><p><span class=\"bold\">{$lang['author']}:</span> <a href=\"home.php?mod=space&uid={$post['authorid']}\" target=\"_blank\">{$post['author']}</a> &nbsp;&nbsp; <span class=\"bold\">{$lang['time']}:</span> {$post['dateline']} &nbsp;&nbsp; IP: {$post['useip']}</p>"
		]);
		showtablerow("id=\"mod_{$post['pid']}_row2\"", 'colspan="2" style="padding: 10px; line-height: 180%;"', '<div style="overflow: auto; overflow-x: hidden; max-height:120px; height:auto !important; height:120px; word-break: break-all;">'.$post['message'].'</div>');
		showtablerow("id=\"mod_{$post['pid']}_row3\"", 'class="threadopt threadtitle" colspan="2"', "{$lang['isanonymous']}: ".($post['anonymous'] ? $lang['yes'] : $lang['no'])." &nbsp;&nbsp; {$lang['ishtmlon']}: ".($post['htmlon'] ? $lang['yes'] : $lang['no']));
	}
	return true;
}

