<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['favoritestatus']) {
	showmessage('favorite_status_off');
}

$_GET['type'] = in_array($_GET['type'], ['thread', 'forum', 'group', 'blog', 'album', 'article', 'all']) ? $_GET['type'] : 'all';
if($_GET['op'] == 'delete') {

	if($_GET['checkall']) {
		if($_GET['favorite']) {
			$deletecounter = [];
			$data = table_home_favorite::t()->fetch_all($_GET['favorite']);
			foreach($data as $dataone) {
				$deletecounter[$dataone['idtype']]['idtype'] = $dataone['idtype'];
				$deletecounter[$dataone['idtype']]['id'][] = $dataone['id'];
			}
			foreach($deletecounter as $thevalue) {
				deletefavorite($thevalue);
			}
			table_home_favorite::t()->delete($_GET['favorite'], false, $_G['uid']);
		}
		showmessage('favorite_delete_succeed', 'home.php?mod=space&uid='.$_G['uid'].'&do=favorite&view=me&type='.$_GET['type'].'&quickforward=1');
	} else {
		$type = empty($_GET['type']) ? '' : $_GET['type'];
		$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
		if($type && $id) {
			switch($type) {
				case 'thread':
					$idtype = 'tid';
					break;
				case 'forum':
					$idtype = 'fid';
					break;
				case 'blog':
					$idtype = 'blogid';
					break;
				case 'group':
					$idtype = 'gid';
					break;
				case 'album':
					$idtype = 'albumid';
					break;
				case 'space':
					$idtype = 'uid';
					break;
				case 'article':
					$idtype = 'aid';
					break;
			}
			$thevalue = table_home_favorite::t()->fetch_by_id_idtype($id, $idtype, $_G['uid']);
			$favid = $thevalue['favid'];
		} else {
			$favid = intval($_GET['favid']);
			$thevalue = table_home_favorite::t()->fetch($favid);
		}
		if(empty($thevalue) || $thevalue['uid'] != $_G['uid']) {
			showmessage('favorite_does_not_exist');
		}

		if(submitcheck('deletesubmit')) {
			deletefavorite($thevalue);
			table_home_favorite::t()->delete($favid);
			showmessage('do_success', 'home.php?mod=space&uid='.$_G['uid'].'&do=favorite&view=me&type='.$_GET['type'].'&quickforward=1', ['favid' => $favid, 'id' => $thevalue['id']], ['showdialog' => 1, 'showmsg' => true, 'closetime' => true, 'locationtime' => 3]);
		}
	}

} else {


	cknewuser();

	$type = empty($_GET['type']) ? '' : $_GET['type'];
	$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
	$spaceuid = empty($_GET['spaceuid']) ? 0 : intval($_GET['spaceuid']);
	$idtype = $title = $icon = '';
	switch($type) {
		case 'thread':
			$idtype = 'tid';
			$thread = table_forum_thread::t()->fetch_thread($id);
			$title = $thread['subject'];
			$icon = '<img src="'.STATICURL.'image/feed/thread.gif" alt="thread" class="vm" /> ';
			break;
		case 'forum':
			$idtype = 'fid';
			$foruminfo = table_forum_forum::t()->fetch($id);
			loadcache('forums');
			$forum = $_G['cache']['forums'][$id];
			if(!$forum['viewperm'] || ($forum['viewperm'] && forumperm($forum['viewperm'])) || strstr($forum['users'], "\t{$_G['uid']}\t")) {
				$title = $foruminfo['status'] != 3 ? $foruminfo['name'] : '';
				$icon = '<img src="'.STATICURL.'image/feed/discuz.gif" alt="forum" class="vm" /> ';
			}
			break;
		case 'blog':
			$idtype = 'blogid';
			$bloginfo = table_home_blog::t()->fetch($id);
			$title = ($bloginfo['uid'] == $spaceuid) ? $bloginfo['subject'] : '';
			$icon = '<img src="'.STATICURL.'image/feed/blog.gif" alt="blog" class="vm" /> ';
			break;
		case 'group':
			$idtype = 'gid';
			$foruminfo = table_forum_forum::t()->fetch($id);
			$title = $foruminfo['status'] == 3 ? $foruminfo['name'] : '';
			$icon = '<img src="'.STATICURL.'image/feed/group.gif" alt="group" class="vm" /> ';
			break;
		case 'album':
			$idtype = 'albumid';
			$result = table_home_album::t()->fetch_album($id, $spaceuid);
			$title = $result['albumname'];
			$icon = '<img src="'.STATICURL.'image/feed/album.gif" alt="album" class="vm" /> ';
			break;
		case 'space':
			$idtype = 'uid';
			$_member = getuserbyuid($id);
			$title = $_member['username'];
			$unset($_member);
			$icon = '<img src="'.STATICURL.'image/feed/profile.gif" alt="space" class="vm" /> ';
			break;
		case 'article':
			$idtype = 'aid';
			$article = table_portal_article_title::t()->fetch($id);
			$title = $article['title'];
			$icon = '<img src="'.STATICURL.'image/feed/article.gif" alt="article" class="vm" /> ';
			break;
	}
	if(empty($idtype) || empty($title)) {
		showmessage('favorite_cannot_favorite');
	}

	$fav = table_home_favorite::t()->fetch_by_id_idtype($id, $idtype, $_G['uid']);
	if($fav) {
		showmessage('favorite_repeat');
	}
	$description = $extrajs = '';
	$description_show = nl2br($description);

	$fav_count = table_home_favorite::t()->count_by_id_idtype($id, $idtype);
	if(submitcheck('favoritesubmit') || ($type == 'forum' || $type == 'group' || $type == 'thread') && $_GET['formhash'] == FORMHASH) {
		$arr = [
			'uid' => intval($_G['uid']),
			'idtype' => $idtype,
			'id' => $id,
			'spaceuid' => $spaceuid,
			'title' => getstr($title, 255),
			'description' => getstr($_POST['description'], '', 0, 0, 1),
			'dateline' => TIMESTAMP
		];
		$favid = table_home_favorite::t()->insert($arr, true);

		switch($type) {
			case 'thread':
				table_forum_thread::t()->increase($id, ['favtimes' => 1]);
				require_once libfile('function/forum');
				update_threadpartake($id);
				break;
			case 'forum':
				table_forum_forum::t()->update_forum_counter($id, 0, 0, 0, 0, 1);
				$extrajs = '<script type="text/javascript">$("number_favorite_num").innerHTML = parseInt($("number_favorite_num").innerHTML)+1;$("number_favorite").style.display="";</script>';
				dsetcookie('nofavfid', '', -1);
				break;
			case 'blog':
				table_home_blog::t()->increase($id, $spaceuid, ['favtimes' => 1]);
				break;
			case 'group':
				table_forum_forum::t()->update_forum_counter($id, 0, 0, 0, 0, 1);
				break;
			case 'album':
				table_home_album::t()->update_num_by_albumid($id, 1, 'favtimes', $spaceuid);
				break;
			case 'space':
				table_common_member_status::t()->increase($id, ['favtimes' => 1]);
				break;
			case 'article':
				table_portal_article_count::t()->increase($id, ['favtimes' => 1]);
				break;
		}
		showmessage('favorite_do_success', dreferer(), ['id' => $id, 'favid' => $favid], ['showdialog' => true, 'closetime' => true, 'extrajs' => $extrajs]);
	}
}

include template('home/spacecp_favorite');

function deletefavorite($thevalue = []) {
	switch($thevalue['idtype']) {
		case 'tid':
			table_forum_thread::t()->increase($thevalue['id'], ['favtimes' => -1]);
			break;
		case 'gid':
		case 'fid':
			table_forum_forum::t()->update_forum_counter($thevalue['id'], 0, 0, 0, 0, -1);
			break;
		case 'blogid':
			table_home_blog::t()->increase($thevalue['id'], 0, ['favtimes' => -1]);
			break;
		case 'albumid':
			table_home_album::t()->update_num_by_albumid($thevalue['id'], -1, 'favtimes', 0);
			break;
		case 'uid':
			table_common_member_status::t()->increase($thevalue['id'], ['favtimes' => -1]);
			break;
		case 'aid':
			table_portal_article_count::t()->increase($thevalue['id'], ['favtimes' => -1]);
			break;
	}
}

