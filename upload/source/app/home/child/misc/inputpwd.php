<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(submitcheck('pwdsubmit')) {

	$blogid = empty($_POST['blogid']) ? 0 : intval($_POST['blogid']);
	$albumid = empty($_POST['albumid']) ? 0 : intval($_POST['albumid']);

	$itemarr = [];
	if($blogid) {
		if(!$_G['setting']['blogstatus']) {
			showmessage('blog_status_off');
		}
		$itemarr = table_home_blog::t()->fetch($blogid);
		$itemurl = "home.php?mod=space&uid={$itemarr['uid']}&do=blog&id={$itemarr['blogid']}";
		$cookiename = 'view_pwd_blog_'.$blogid;
	} elseif($albumid) {
		if(!$_G['setting']['albumstatus']) {
			showmessage('album_status_off');
		}
		$itemarr = table_home_album::t()->fetch_album($albumid);
		$itemurl = "home.php?mod=space&uid={$itemarr['uid']}&do=album&id={$itemarr['albumid']}";
		$cookiename = 'view_pwd_album_'.$albumid;
	}

	if(empty($itemarr)) {
		showmessage('news_does_not_exist');
	}

	if($itemarr['password'] && $_POST['viewpwd'] == $itemarr['password']) {
		dsetcookie($cookiename, md5(md5($itemarr['password'])));
		showmessage('proved_to_be_successful', $itemurl, ['succeed' => 1], ['showmsg' => 1, 'timeout' => 1]);
	} else {
		showmessage('password_is_not_passed', $itemurl, ['succeed' => 0], ['showmsg' => 1]);
	}
}

