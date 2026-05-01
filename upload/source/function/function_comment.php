<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function add_comment($message, $id, $idtype, $cid = 0) {
	global $_G, $bbcode;

	$allowcomment = false;
	switch($idtype) {
		case 'uid':
			$allowcomment = helper_access::check_module('wall');
			break;
		case 'picid':
			$allowcomment = helper_access::check_module('album');
			break;
		case 'blogid':
			$allowcomment = helper_access::check_module('blog');
			break;
		case 'sid':
			$allowcomment = helper_access::check_module('share');
			break;
	}
	if(!$allowcomment) {
		showmessage('quickclear_noperm');
	}
	$summay = getstr($message, 150, 0, 0, 0, -1);


	$comment = [];
	if($cid) {
		$comment = table_home_comment::t()->fetch_by_id_idtype($id, $idtype, $cid);
		if($comment && $comment['authorid'] != $_G['uid']) {
			$comment['message'] = preg_replace("/\<div class=\"quote\"\>\<blockquote\>.*?\<\/blockquote\>\<\/div\>/is", '', $comment['message']);
			$comment['message'] = $bbcode->html2bbcode($comment['message']);
			$message = ("<div class=\"quote\"><blockquote><b>".$comment['author']. '</b>: ' .getstr($comment['message'], 150, 0, 0, 2, 1).'</blockquote></div>').$message;
			if($comment['idtype'] == 'uid') {
				$id = $comment['authorid'];
			}
		} else {
			$comment = [];
		}
	}

	$hotarr = [];
	$stattype = '';
	$tospace = $pic = $blog = $album = $share = $poll = [];

	switch($idtype) {
		case 'uid':
			$tospace = getuserbyuid($id);
			$stattype = 'wall';
			break;
		case 'picid':
			$pic = table_home_pic::t()->fetch($id);
			if(empty($pic)) {
				showmessage('view_images_do_not_exist');
			}
			$picfield = table_home_picfield::t()->fetch($id);
			$pic['hotuser'] = $picfield['hotuser'];
			$tospace = getuserbyuid($pic['uid']);

			$album = [];
			if($pic['albumid']) {
				$album = table_home_album::t()->fetch_album($pic['albumid']);
				if(!$album['albumid']) {
					table_home_pic::t()->update_for_albumid($pic['albumid'], ['albumid' => 0]);
				}
			}

			if(!ckfriend($album['uid'], $album['friend'], $album['target_ids'])) {
				showmessage('no_privilege_ckfriend_pic');
			} elseif(!$tospace['self'] && $album['friend'] == 4) {
				$cookiename = "view_pwd_album_{$album['albumid']}";
				$cookievalue = empty($_G['cookie'][$cookiename]) ? '' : $_G['cookie'][$cookiename];
				if($cookievalue != md5(md5($album['password']))) {
					showmessage('no_privilege_ckpassword_pic');
				}
			}

			$hotarr = ['picid', $pic['picid'], $pic['hotuser']];
			$stattype = 'piccomment';
			break;
		case 'blogid':
			$blog = array_merge(
				table_home_blog::t()->fetch($id),
				table_home_blogfield::t()->fetch_targetids_by_blogid($id)
			);
			if(empty($blog)) {
				showmessage('view_to_info_did_not_exist');
			}

			$tospace = getuserbyuid($blog['uid']);

			if(!ckfriend($blog['uid'], $blog['friend'], $blog['target_ids'])) {
				showmessage('no_privilege_ckfriend_blog');
			} elseif(!$tospace['self'] && $blog['friend'] == 4) {
				$cookiename = "view_pwd_blog_{$blog['blogid']}";
				$cookievalue = empty($_G['cookie'][$cookiename]) ? '' : $_G['cookie'][$cookiename];
				if($cookievalue != md5(md5($blog['password']))) {
					showmessage('no_privilege_ckpassword_blog');
				}
			}

			if(!empty($blog['noreply'])) {
				showmessage('do_not_accept_comments');
			}
			if($blog['target_ids']) {
				$blog['target_ids'] .= ",{$blog['uid']}";
			}

			$hotarr = ['blogid', $blog['blogid'], $blog['hotuser']];
			$stattype = 'blogcomment';
			break;
		case 'sid':
			$share = table_home_share::t()->fetch($id);
			if(empty($share)) {
				showmessage('sharing_does_not_exist');
			}

			$tospace = getuserbyuid($share['uid']);

			$hotarr = ['sid', $share['sid'], $share['hotuser']];
			$stattype = 'sharecomment';
			break;
		default:
			showmessage('non_normal_operation');
			break;
	}
	if(empty($tospace)) {
		showmessage('space_does_not_exist', '', [], ['return' => true]);
	}

	if(isblacklist($tospace['uid'])) {
		showmessage('is_blacklist');
	}

	if($hotarr && $tospace['uid'] != $_G['uid']) {
		hot_update($hotarr[0], $hotarr[1], $hotarr[2]);
	}

	$fs = [];
	$fs['icon'] = 'comment';
	$fs['target_ids'] = '';
	$fs['friend'] = '';
	$fs['body_template'] = '';
	$fs['body_data'] = [];
	$fs['body_general'] = '';
	$fs['images'] = [];
	$fs['image_links'] = [];

	switch($idtype) {
		case 'uid':
			$fs['icon'] = 'wall';
			$fs['title_template'] = 'feed_comment_space';
			$fs['title_data'] = ['touser' => "<a href=\"home.php?mod=space&uid={$tospace['uid']}\">{$tospace['username']}</a>"];
			break;
		case 'picid':
			$fs['title_template'] = 'feed_comment_image';
			$fs['title_data'] = ['touser' => "<a href=\"home.php?mod=space&uid={$tospace['uid']}\">".$tospace['username']. '</a>'];
			$fs['body_template'] = '{pic_title}';
			$fs['body_data'] = ['pic_title' => $pic['title']];
			$fs['body_general'] = $summay;
			$fs['images'] = [pic_get($pic['filepath'], 'album', $pic['thumb'], $pic['remote'])];
			$fs['image_links'] = ["home.php?mod=space&uid={$tospace['uid']}&do=album&picid={$pic['picid']}"];
			$fs['target_ids'] = $album['target_ids'];
			$fs['friend'] = $album['friend'];
			break;
		case 'blogid':
			table_home_blog::t()->increase($id, 0, ['replynum' => 1]);
			$fs['title_template'] = 'feed_comment_blog';
			$fs['title_data'] = ['touser' => "<a href=\"home.php?mod=space&uid={$tospace['uid']}\">".$tospace['username']. '</a>', 'blog' => "<a href=\"home.php?mod=space&uid={$tospace['uid']}&do=blog&id=$id\">{$blog['subject']}</a>"];
			$fs['target_ids'] = $blog['target_ids'];
			$fs['friend'] = $blog['friend'];
			break;
		case 'sid':
			$fs['title_template'] = 'feed_comment_share';
			$fs['title_data'] = ['touser' => "<a href=\"home.php?mod=space&uid={$tospace['uid']}\">".$tospace['username']. '</a>', 'share' => "<a href=\"home.php?mod=space&uid={$tospace['uid']}&do=share&id=$id\">".str_replace(lang('spacecp', 'share_action'), '', $share['title_template']). '</a>'];
			break;
	}

	$message = censor($message, NULL, FALSE, FALSE);
	if(censormod($message) || $_G['group']['allowcommentmod']) {
		$comment_status = 1;
	} else {
		$comment_status = 0;
	}

	$setarr = [
		'uid' => $tospace['uid'],
		'id' => $id,
		'idtype' => $idtype,
		'authorid' => $_G['uid'],
		'author' => $_G['username'],
		'dateline' => $_G['timestamp'],
		'message' => $message,
		'ip' => $_G['clientip'],
		'port' => $_G['remoteport'],
		'status' => $comment_status,
	];
	$cid = table_home_comment::t()->insert($setarr, true);

	$action = 'comment';
	$becomment = 'getcomment';
	$note = $q_note = '';
	$note_values = $q_values = [];

	switch($idtype) {
		case 'uid':
			$n_url = "home.php?mod=space&uid={$tospace['uid']}&do=wall&cid=$cid";

			$note_type = 'wall';
			$note = 'wall';
			$note_values = ['url' => $n_url];
			$q_note = 'wall_reply';
			$q_values = ['url' => $n_url];

			if($comment) {
				$msg = 'note_wall_reply_success';
				$magvalues = ['username' => $tospace['username']];
				$becomment = '';
			} else {
				$msg = 'do_success';
				$magvalues = [];
				$becomment = 'getguestbook';
			}

			$action = 'guestbook';
			break;
		case 'picid':
			$n_url = "home.php?mod=space&uid={$tospace['uid']}&do=album&picid=$id&cid=$cid";

			$note_type = 'comment';
			$note = 'pic_comment';
			$note_values = ['url' => $n_url];
			$q_note = 'pic_comment_reply';
			$q_values = ['url' => $n_url];

			$msg = 'do_success';
			$magvalues = [];

			break;
		case 'blogid':
			$n_url = "home.php?mod=space&uid={$tospace['uid']}&do=blog&id=$id&cid=$cid";

			$note_type = 'comment';
			$note = 'blog_comment';
			$note_values = ['url' => $n_url, 'subject' => $blog['subject']];
			$q_note = 'blog_comment_reply';
			$q_values = ['url' => $n_url];

			$msg = 'do_success';
			$magvalues = [];

			break;
		case 'sid':
			$n_url = "home.php?mod=space&uid={$tospace['uid']}&do=share&id=$id&cid=$cid";

			$note_type = 'comment';
			$note = 'share_comment';
			$note_values = ['url' => $n_url];
			$q_note = 'share_comment_reply';
			$q_values = ['url' => $n_url];

			$msg = 'do_success';
			$magvalues = [];

			break;
	}

	if(empty($comment)) {

		if($tospace['uid'] != $_G['uid']) {
			if(ckprivacy('comment', 'feed')) {
				require_once libfile('function/feed');
				$fs['title_data']['hash_data'] = "{$idtype}{$id}";
				feed_add($fs['icon'], $fs['title_template'], $fs['title_data'], $fs['body_template'], $fs['body_data'], $fs['body_general'], $fs['images'], $fs['image_links'], $fs['target_ids'], $fs['friend']);
			}

			$note_values['from_id'] = $id;
			$note_values['from_idtype'] = $idtype;
			$note_values['url'] .= "&goto=new#comment_{$cid}_li";

			notification_add($tospace['uid'], $note_type, $note, $note_values);
		}

	} elseif($comment['authorid'] != $_G['uid']) {
		notification_add($comment['authorid'], $note_type, $q_note, $q_values);
	}

	if($comment_status == 1) {
		updatemoderate($idtype.'_cid', $cid);
		manage_addnotify('verifycommontes');
	}
	if($stattype) {
		include_once libfile('function/stat');
		updatestat($stattype);
	}
	if($tospace['uid'] != $_G['uid']) {
		$needle = $id;
		if($idtype != 'uid') {
			$needle = $idtype.$id;
		} else {
			$needle = $tospace['uid'];
		}
		updatecreditbyaction($action, 0, [], $needle);
		if($becomment) {
			if($idtype == 'uid') {
				$needle = $_G['uid'];
			}
			updatecreditbyaction($becomment, $tospace['uid'], [], $needle);
		}
	}

	table_common_member_status::t()->update($_G['uid'], ['lastpost' => $_G['timestamp']], 'UNBUFFERED');
	$magvalues['cid'] = $cid;

	return ['cid' => $cid, 'msg' => $msg, 'magvalues' => $magvalues];
}

