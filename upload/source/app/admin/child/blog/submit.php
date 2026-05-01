<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($_GET['blogids']) {
	$blogids = authcode($_GET['blogids'], 'DECODE');
	$blogidsadd = $blogids ? explode(',', $blogids) : $_GET['delete'];
	include_once libfile('function/delete');
	$deletecount = count(deleteblogs($blogidsadd));
	$cpmsg = cplang('blog_succeed', ['deletecount' => $deletecount]);
} else {
	$blogs = $catids = [];
	$selectblogids = !empty($_GET['ids']) && is_array($_GET['ids']) ? $_GET['ids'] : [];
	if($selectblogids) {
		$query = table_home_blog::t()->fetch_all_blog($selectblogids);
		foreach($query as $value) {
			$blogs[$value['blogid']] = $value;
			$catids[] = intval($value['catid']);
		}
	}
	if($blogs) {
		$selectblogids = array_keys($blogs);
		if($_POST['optype'] == 'delete') {
			include_once libfile('function/delete');
			$deletecount = count(deleteblogs($selectblogids));
			$cpmsg = cplang('blog_succeed', ['deletecount' => $deletecount]);
		} elseif($_POST['optype'] == 'move') {
			$tocatid = intval($_POST['tocatid']);
			$catids[] = $tocatid;
			$catids = array_merge($catids);

			table_home_blog::t()->update($selectblogids, ['catid' => $tocatid]);
			foreach($catids as $catid) {
				$catid = intval($catid);
				$cnt = table_home_blog::t()->count_by_catid($catid);
				table_home_blog_category::t()->update($catid, ['num' => $cnt]);
			}
			$cpmsg = cplang('blog_move_succeed');
		} else {
			$cpmsg = cplang('blog_choose_at_least_one_operation');
		}
	} else {
		$cpmsg = cplang('blog_choose_at_least_one_blog');
	}
}

echo '<script type="text/JavaScript">alert(\''.$cpmsg.'\');parent.$(\'blogforum\').searchsubmit.click();</script>';
	