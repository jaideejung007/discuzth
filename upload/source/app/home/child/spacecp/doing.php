<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

use table_common_member;
use table_home_follow;
use table_home_friend;

if(!$_G['setting']['doingstatus']) {
	showmessage('doing_status_off');
}

$doid = empty($_GET['doid']) ? 0 : intval($_GET['doid']);
$docid = empty($_GET['docid']) ? 0 : intval($_GET['docid']);

if($_GET['op'] == 'delete') {
	if(submitcheck('deletesubmit')) {
		if($docid) {
			$allowmanage = checkperm('managedoing');
			if($value = table_home_docomment::t()->fetch($docid)) {
				$home_doing = table_home_doing::t()->fetch($value['doid']);
				$value['duid'] = $home_doing['uid'];
				if($allowmanage || $value['uid'] == $_G['uid'] || $value['duid'] == $_G['uid']) {
					table_home_docomment::t()->update($docid, ['uid' => 0, 'username' => '', 'message' => '']);
					if($value['uid'] != $_G['uid'] && $value['duid'] != $_G['uid']) {
						batchupdatecredit('comment', $value['uid'], [], -1);
					}
					table_home_doing::t()->update_replynum_by_doid(-1, $updo['doid']);
				}
			}
		} else {
			require_once libfile('function/delete');
			deletedoings([$doid]);
		}

		if(defined('IN_RESTFUL')) {
			showmessage('doing_delete_success');
			exit();
		}else{
			dheader('location: '.dreferer());
			exit();
		}
	}
} elseif($_GET['op'] == 'docomment') {
	$doid = intval($_GET['doid']);
	$docid = intval($_GET['docid']);
	$key = empty($_GET['key']) ? random(8) : $_GET['key'];
	$_GET['key'] = $key;

	$updo = [];
	if($docid) {
		$updo = table_home_docomment::t()->fetch($docid);
	} elseif($doid) {
		$updo = table_home_doing::t()->fetch($doid);
	}
	
	if(empty($updo)) {
		showmessage('docomment_error');
	}

	include template('home/spacecp_doing');
	dexit();
} elseif($_GET['op'] == 'getcomment') {
	$key = empty($_GET['key']) ? random(8) : $_GET['key'];
	$_GET['key'] = $key;
	$doid = empty($_GET['doid']) ? 0 : intval($_GET['doid']);
	$doing_info = table_home_doing::t()->fetch($doid);

	$comment_perpage = intval(10);
	$comment_page = empty($_GET['page_c']) ? 0 : intval($_GET['page_c']);
	if($comment_page < 1) $comment_page = 1;
	$comment_start = intval(($comment_page - 1) * $comment_perpage);

	$list = [];
	$highlight = 0;
	$count = 0;
	$has_more = false;

	if(empty($_GET['close'])) {
		
		$top_comment_count = intval(table_home_docomment::t()->count_top_by_doid($doid));
		
		
		$top_comments = table_home_docomment::t()->fetch_all_top_by_doid($doid, $comment_start, $comment_perpage);
		$top_ids = array_column($top_comments, 'id');
		
		
		
		
		
		$is_ajax_list = isset($_GET['inlist']);
		$has_more = $is_ajax_list && $top_comment_count > $comment_perpage;
		
		
		$child_comments = table_home_docomment::t()->fetch_all_child_by_doid($doid);
		
		
		$all_comments = array_merge($top_comments, $child_comments);
		
		
		$comment_map = [];
		$root_comments = [];
		
		foreach($all_comments as $comment) {
			if(!empty($comment['ip'])) {
				$comment['ip'] = ip::to_display($comment['ip']);
				$comment['iplocation'] = $_G['setting']['showiplocation'] ? ip::convert($comment['ip'], true) : '';
			}
			$comment_map[$comment['id']] = $comment;
			
			if($comment['upid'] == 0) {
				$root_comments[] = $comment;
			}
			
			$count++;
		}
		
		
		$comment_tree = [];
		foreach($comment_map as $comment_id => $comment) {
			$parent_id = $comment['upid'];
			if($parent_id == 0) {
				
				$comment_tree[$comment_id] = $comment;
				$comment_tree[$comment_id]['children'] = [];
			} else {
				
				if(isset($comment_map[$parent_id])) {
					$parent_comment = $comment_map[$parent_id];
					
					
					$top_parent_id = $parent_id;
					while($top_parent_id > 0 && isset($comment_map[$top_parent_id]) && $comment_map[$top_parent_id]['upid'] > 0) {
						$top_parent_id = $comment_map[$top_parent_id]['upid'];
					}
					
					
					if(in_array($top_parent_id, $top_ids)) {
						$comment_tree[$top_parent_id]['children'][] = $comment;
					}
				}
			}
		}
		
		
		foreach($comment_tree as $root_id => $root_comment) {
			
			
			$root_comment['layer'] = 0;
			$root_comment['style'] = "padding-left:0em;";
			$root_comment['class'] = '';
			$root_comment['is_child'] = false;
			$root_comment['reply_to_user'] = '';
			$root_comment['reply_uid'] = 0;
			$root_comment['avatar'] = avatar($root_comment['uid'], 'small', true);
			$root_comment['dateline_formatted'] = dgmdate($root_comment['dateline'], 'u');
			
			$root_comment['can_delete'] = ($root_comment['uid'] == $_G['uid'] || $doing_info['uid'] == $_G['uid'] || checkperm('managedoing'));
			
			
			
			
			
			
			$list[] = $root_comment;
			
			
			if(!empty($root_comment['children'])) {
				$child_count = count($root_comment['children']);
				$show_child_count = 3;
				$show_children = array_slice($root_comment['children'], 0, $show_child_count);
				$hide_children = array_slice($root_comment['children'], $show_child_count);
				
				
				foreach($show_children as $child_comment) {
					$child_comment['layer'] = 2;
					$child_comment['style'] = "padding-left:4em;";
					$child_comment['class'] = ' dtll';
					$child_comment['is_child'] = true;
					$child_comment['is_hidden'] = false;
					$child_comment['avatar'] = avatar($child_comment['uid'], 'small', true);
					$child_comment['dateline_formatted'] = dgmdate($child_comment['dateline'], 'u');
					
					$child_comment['can_delete'] = ($child_comment['uid'] == $_G['uid'] || $doing_info['uid'] == $_G['uid'] || checkperm('managedoing'));
					
					
					$child_comment['reply_to_user'] = '';
					$child_comment['reply_uid'] = 0;
					
					if($child_comment['upid'] > 0 && isset($comment_map[$child_comment['upid']])) {
						$parent_comment = $comment_map[$child_comment['upid']];
						
						if($parent_comment['upid'] > 0) {
							$child_comment['reply_to_user'] = $parent_comment['username'];
							$child_comment['reply_uid'] = $parent_comment['uid'];
						}
					}
					
					$list[] = $child_comment;
				}
				
				
				if(!empty($hide_children)) {
					
					$toggle_comment = array(
						'id' => "toggle_{$root_id}",
						'doid' => $root_comment['doid'],
						'layer' => 2,
						'style' => "padding-left:2em;",
						'class' => ' dtll toggle-comment',
						'is_child' => true,
						'is_toggle' => true,
						'hide_count' => count($hide_children),
						'root_id' => $root_id,
						'username' => '',
						'message' => "",
						'reply_to_user' => '',
						'reply_uid' => 0,
						'is_hidden' => false,
						'can_delete' => false
					);
					$list[] = $toggle_comment;
					
					
					foreach($hide_children as $child_comment) {
						$child_comment['layer'] = 2;
						$child_comment['style'] = "padding-left:2em; display: none;";
						$child_comment['class'] = ' dtll hidden-comment';
						$child_comment['is_child'] = true;
						$child_comment['is_hidden'] = true;
						$child_comment['root_id'] = $root_id;
						$child_comment['avatar'] = avatar($child_comment['uid'], 'small', true);
						$child_comment['dateline_formatted'] = dgmdate($child_comment['dateline'], 'u');
						
						$child_comment['can_delete'] = ($child_comment['uid'] == $_G['uid'] || $doing_info['uid'] == $_G['uid'] || checkperm('managedoing'));
						
						
						$child_comment['reply_to_user'] = '';
						$child_comment['reply_uid'] = 0;
						
						if($child_comment['upid'] > 0 && isset($comment_map[$child_comment['upid']])) {
							$parent_comment = $comment_map[$child_comment['upid']];
							
							if($parent_comment['upid'] > 0) {
								$child_comment['reply_to_user'] = $parent_comment['username'];
								$child_comment['reply_uid'] = $parent_comment['uid'];
							}
						}
						
						$list[] = $child_comment;
					}
				}
			}
		}
	}
	
	
	$comment_multi = array();
	if (isset($_GET['page_c'])) {
		if($top_comment_count > $comment_perpage) {
			
			$comment_url = "home.php?mod=spacecp&ac=doing&op=getcomment&doid=$doid&key=$key&page_c={page}";
			$comment_multi[$doid] = multi($top_comment_count, $comment_perpage, $comment_page, $comment_url);
			
			
			
			
			preg_match_all('/<a\s+href="([^"]*?)"[^>]*>/', $comment_multi[$doid], $matches);
			if(!empty($matches[0])) {
				foreach($matches[0] as $index => $full_match) {
					$href = $matches[1][$index];
					
					$page_num = 1; 
					if(preg_match('/page_c=(\d+)/', $href, $page_match)) {
						
						$page_num = $page_match[1];
					} elseif(strpos($href, 'page_c=') !== false) {
						
						$page_num = 1;
					}
					
					$new_link = '<a href="javascript:;" onclick="docomment_get_page('.$doid.', \''.$key.'\', '.$page_num.');" data-page="'.$page_num.'">';
					$comment_multi[$doid] = str_replace($full_match, $new_link, $comment_multi[$doid]);
				}
			}
			
			
			$comment_multi[$doid] = preg_replace('/<span[^>]*>(\d+)<\/span>/', '<span data-page="$1">$1</span>', $comment_multi[$doid]);
		}
	}
	
	
	if (defined('IN_MOBILE')) {
		
		$response = array(
			'list' => $list,
			'count' => $count,
			'has_more' => $has_more,
			'page' => $comment_page,
			'perpage' => $comment_perpage,
			'total_pages' => ceil($top_comment_count / $comment_perpage),
			'total_comments' => $top_comment_count
		);

		
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($response);
		dexit();
	} else {
		
		include template('home/space_doing_comment_li');
		dexit();
	}
} elseif($_GET['op'] == 'recommend') {
	
	$doid = intval($_GET['doid']);
	$uid = $_G['uid'];

	if(!$doid || !$uid) {
		showmessage('to_login', '', array(), array('login' => 1));
	}

	$doing = table_home_doing::t()->fetch($doid);
	if(!$doing) {
		showmessage('doing_not_exists');
	}

	$exists = table_home_doing_recomend_log::t()->fetch_by_doid_uid($doid, $uid);

	if($exists) {
		
		table_home_doing::t()->update_recommendnum_by_doid(-1, $doid);
		table_home_doing_recomend_log::t()->delete_by_doid_uid($doid, $uid);
		$status = 0;
	} else {
		
		table_home_doing::t()->update_recommendnum_by_doid(1, $doid);
		table_home_doing_recomend_log::t()->insert(array(
			'doid' => $doid,
			'uid' => $uid,
			'dateline' => TIMESTAMP
		));
		$status = 1;
	}

	
	$doing = table_home_doing::t()->fetch($doid);
	$recomends = $doing['recomends'];

	if(defined('IN_RESTFUL')) {
		showmessage('doing_recommend_success', '', array(), array('status' => $status,'count' => $recomends));
		exit();
	}else{
		
		header('Content-Type: application/json');
		echo json_encode(array(
			'message' => 'doing_recommend_success',
			'status' => $status,
			'count' => $recomends
		));
		exit;
	}
	exit;
} elseif($_GET['op'] == 'spacenote') {
	space_merge($space, 'field_home');
} elseif($_GET['op'] == 'upload_image') {
	if(!checkperm('allowdoing')) {
		showmessage('no_privilege_doing', '', array(), array('login' => 1));
	}

	if (!empty($_FILES)) {
		$upload = new upload('doing');
		$f = $upload->upload();
		$result = [
			'status' => 'success',
			'images' => []
		];

		$image = null;

		if (!empty($f['Filedata']) && is_array($f['Filedata'])) {
			$value = $f['Filedata'];
			if (is_array($value) && $value['attachment']) {
				if (isset($value['isimage']) && $value['isimage'] == 1) {
					$temp_doid = 0; 
					$aid = table_home_doing_attachment::t()->insert_attachment([
						'doid' => $temp_doid,
						'uid' => $_G['uid'],
						'dateline' => TIMESTAMP,
						'filename' => $value['name'],
						'filesize' => $value['size'],
						'attachment' => $value['attachment'],
						'remote' => $value['remote'] ?? 0,
						'isimage' => $value['isimage'],
						'width' => $value['imageinfo'][0] ?? 0,
						'height' => $value['imageinfo'][1] ?? 0,
						'displayorder' => 0
					], true);
					$image_url = getglobal('setting/attachurl').'doing/'.$value['attachment'];
					if($value['remote']) {
						$image_url = getglobal('setting/ftp/attachurl').'doing/'.$value['attachment'];
					}
					
					$image = [
						'aid' => $aid,
						'filename' => $value['name'],
						'url' => $image_url,
						'width' => $value['imageinfo'][0] ?? 0,
						'height' => $value['imageinfo'][1] ?? 0,
						'filesize' => $value['size'],
						'extension' => $value['ext'] ?? '',
						'mime_type' => $value['type'] ?? ''
					];
				}
			}
		}

		
		if ($image) {
			$result = [
				'status' => 'success',
				'image' => $image
			];
		} else {
			$result = [
				'status' => 'error',
				'message' => 'Upload failed or no valid image found',
				'debug_info' => $f 
			];
			
			if(defined('IN_RESTFUL')) {
				showmessage('upload_failed', '', array(), array('result' => $result));
			} else {
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			}
		}

		if(defined('IN_RESTFUL')) {
			echo $result;
		} else {
			header('Content-Type: application/json');
			echo json_encode($result);
			exit;
		}
	} else {
		$result = [
			'status' => 'error',
			'message' => 'No files uploaded'
		];

		if(defined('IN_RESTFUL')) {
			echo $result;
		} else {
			header('Content-Type: application/json');
			echo json_encode($result);
			exit;
		}
	}
} elseif($_GET['op'] == 'delete_image') {
	$count = 0;

	if(isset($_GET['aids'])) {
		$_GET['aids'] = (array)$_GET['aids'];
		foreach($_GET['aids'] as $aid) {
			$attach = table_home_doing_attachment::t()->fetch_attachment('aid:'.$aid, $aid);
			if($attach && $attach['uid'] == $_G['uid']) {

				table_home_doing_attachment::t()->delete($aid);
				pic_delete($attach['attachment'], 'doing', 0, $attach['remote']);
				if($_G['setting']['ftp']['on'] == 2) {
					ftpcmd('delete', 'doing/'.$attach['attachment']);
					ftpcmd('delete', 'doing/'.getimgthumbname($attach['attachment']));
				}

				$count++;
			}
		}
	}

	if(defined('IN_RESTFUL')) {
		echo $count;
		exit();
	}

	include template('common/header_ajax');
	echo $count;
	include template('common/footer_ajax');
	dexit();
}else{
	$type = empty($_GET['type']) ? '' : $_GET['type'];
	$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
	$note_uid = 0;
	$note_message = '';
	$note_values = [];

	$arr = [];
	$feed_hash_data = '';

	switch($type) {
		case 'space':

			$feed_hash_data = "uid{$id}";

			$tospace = getuserbyuid($id);
			if(empty($tospace)) {
				showmessage('space_does_not_exist');
			}
			if(isblacklist($tospace['uid'])) {
				showmessage('is_blacklist');
			}

			$arr['itemid'] = $id;
			$arr['body_template'] = '<h5><a href="home.php?mod=space&uid={uid}" target="_blank">{username}</a></h5><p><a href="home.php?mod=space&uid={uid}" target="_blank">'.lang('spacecp', 'homepage').'</a></p>';
			$arr['body_data'] = [
				'uid' => $id,
				'username' => $tospace['username'],
				'reside' => $tospace['residecountry'].$tospace['resideprovince'].$tospace['residecity'],
				'spacenote' => $tospace['spacenote']
			];

			loaducenter();
			$isavatar = uc_check_avatar($id);
			$arr['image'] = $arr['body_data']['image'] = $isavatar ? avatar($id, 'middle', true) : $_G['setting']['avatarurl'].'/noavatar.svg';
			$arr['image_link'] = $arr['body_data']['image_link'] = "home.php?mod=space&uid=$id";

			$note_uid = $id;
			$note_message = 'share_space';

			break;
		case 'blog':

			$feed_hash_data = "blogid{$id}";

			$blog = array_merge(
				table_home_blog::t()->fetch($id),
				table_home_blogfield::t()->fetch($id)
			);
			if(!$blog) {
				showmessage('blog_does_not_exist');
			}
			if(in_array($blog['status'], [1, 2])) {
				showmessage('moderate_blog_not_share');
			}
			if($blog['friend']) {
				showmessage('logs_can_not_share');
			}
			if(isblacklist($blog['uid'])) {
				showmessage('is_blacklist');
			}
			$arr['itemid'] = $id;
			$arr['body_template'] = '<h5><a href="{url}" target="_blank">{subject}</a></h5><p><a href="home.php?mod=space&uid={uid}">{username}</a>: {dateline} </p>';
			$arr['body_data'] = [
				'url' => "home.php?mod=space&uid=".$blog['uid']."&do=blog&id=".$blog['blogid'],
				'subject' => $blog['subject'],
				'uid' => $blog['uid'],
				'blog' => $blog['blogid'],
				'username' => $blog['username'],
				'dateline' => dgmdate($blog['dateline']),
				'message' => getstr($blog['message'], 150, 0, 0, 0, -1)
			];
			if($blog['pic']) {
				$arr['image'] = $arr['body_data']['image'] = pic_cover_get($blog['pic'], $blog['picflag']);
				$arr['image_link'] = $arr['body_data']['image_link'] = "home.php?mod=space&uid={$blog['uid']}&do=blog&id={$blog['blogid']}";
			}
			$note_uid = $blog['uid'];
			$note_message = 'share_blog';
			$note_values = ['url' => "home.php?mod=space&uid={$blog['uid']}&do=blog&id={$blog['blogid']}", 'subject' => $blog['subject'], 'from_id' => $id, 'from_idtype' => 'blogid'];

			$hotarr = ['blogid', $blog['blogid'], $blog['hotuser']];

			break;
		case 'album':

			$feed_hash_data = "albumid{$id}";

			if(!$album = table_home_album::t()->fetch_album($id)) {
				showmessage('album_does_not_exist');
			}
			if($album['friend']) {
				showmessage('album_can_not_share');
			}
			if(isblacklist($album['uid'])) {
				showmessage('is_blacklist');
			}

			$arr['itemid'] = $id;
			$arr['body_template'] = '<h5><a href="{url}" target="_blank">{albumname}</a></h5><p><a href="home.php?mod=space&uid={uid}">{username}</a>: {dateline} </p>';
			$arr['body_data'] = [
				'url' => "home.php?mod=space&uid={$album['uid']}&do=album&id={$album['albumid']}",
				'albumname' => $album['albumname'],
				'uid' => $album['uid'],
				'albumid' => $album['albumid'],
				'username' => $album['username'],
				'dateline' => dgmdate($album['dateline'])
			];
			$arr['image'] = $arr['body_data']['image'] = pic_cover_get($album['pic'], $album['picflag']);
			$arr['image_link'] = $arr['body_data']['image_link'] = "home.php?mod=space&uid={$album['uid']}&do=album&id={$album['albumid']}";
			$note_uid = $album['uid'];
			$note_message = 'share_album';
			$note_values = ['url' => "home.php?mod=space&uid={$album['uid']}&do=album&id={$album['albumid']}", 'albumname' => $album['albumname'], 'from_id' => $id, 'from_idtype' => 'albumid'];

			break;
		case 'pic':

			$feed_hash_data = "picid{$id}";
			$pic = table_home_pic::t()->fetch($id);
			if(!$pic) {
				showmessage('image_does_not_exist');
			}
			$picfield = table_home_picfield::t()->fetch($id);
			$album = table_home_album::t()->fetch_album($pic['albumid']);
			$pic = array_merge($pic, $picfield, $album);
			if(in_array($pic['status'], [1, 2])) {
				showmessage('moderate_pic_not_share');
			}
			if($pic['friend']) {
				showmessage('image_can_not_share');
			}
			if(isblacklist($pic['uid'])) {
				showmessage('is_blacklist');
			}
			if(empty($pic['albumid'])) $pic['albumid'] = 0;
			if(empty($pic['albumname'])) $pic['albumname'] = lang('spacecp', 'default_albumname');

			$arr['itemid'] = $id;
			$arr['body_template'] = '<h5>'.lang('spacecp', 'album').'<a href="{url}" target="_blank">{albumname}</a></h5><p><a href="home.php?mod=space&uid={uid}">{username}</a>: {dateline} </p>';
			$arr['body_data'] = [
				'url' => "home.php?mod=space&uid={$pic['uid']}&do=album&picid={$pic['picid']}",
				'albumname' => $pic['albumname'],
				'uid' => $pic['uid'],
				'picid' => $pic['picid'],
				'username' => $pic['username'],
				'dateline' => dgmdate($pic['dateline']),
				'title' => getstr($pic['title'], 100, 0, 0, 0, -1)
			];
			$arr['image'] = $arr['body_data']['image'] = pic_get($pic['filepath'], 'album', $pic['thumb'], $pic['remote']);
			$arr['image_link'] = $arr['body_data']['image_link'] = "home.php?mod=space&uid={$pic['uid']}&do=album&picid={$pic['picid']}";
			$note_uid = $pic['uid'];
			$note_message = 'share_pic';
			$note_values = ['url' => "home.php?mod=space&uid={$pic['uid']}&do=album&picid={$pic['picid']}", 'albumname' => $pic['albumname'], 'from_id' => $id, 'from_idtype' => 'picid'];

			$hotarr = ['picid', $pic['picid'], $pic['hotuser']];

			break;

		case 'thread':
			
			$feed_hash_data = "tid{$id}";

			$thread = table_forum_thread::t()->fetch_thread($id);
			if(in_array($thread['displayorder'], [-2, -3])) {
				showmessage('moderate_thread_not_share');
			}
			require_once libfile('function/post');
			$post = table_forum_post::t()->fetch_threadpost_by_tid_invisible($id);
			$arr['body_template'] = '<h5><a href="{url}" target="_blank">{subject}</a></h5><p><a href="home.php?mod=space&uid={authorid}">{author}</a>: {dateline} </p>';
			$post['message'] = threadmessagecutstr($thread, $post['message']);
			$arr['body_data'] = [
				'url' => "forum.php?mod=viewthread&tid=$id",
				'subject' => $thread['subject'],
				'authorid' => $thread['authorid'],
				'author' => $thread['author'],
				'tid' => $id,
				'dateline' => dgmdate($thread['dateline']),
				'message' => getstr($post['message'], 150, 0, 0, 0, -1)
			];
			$arr['itemid'] = $id;
			$attachment = table_forum_attachment_n::t()->fetch_max_image('tid:'.$id, 'tid', $id);
			if($attachment) {
				$arr['image'] = $arr['body_data']['image'] = pic_get($attachment['attachment'], 'forum', $attachment['thumb'], $attachment['remote'], 1);
				$arr['image_link'] = $arr['body_data']['image_link'] = "forum.php?mod=viewthread&tid=$id";
			}

			$note_uid = $thread['authorid'];
			$note_message = 'share_thread';
			$note_values = ['url' => "forum.php?mod=viewthread&tid=$id", 'subject' => $thread['subject'], 'from_id' => $id, 'from_idtype' => 'tid'];
			break;

		case 'article':

			$feed_hash_data = "articleid{$id}";

			$article = table_portal_article_title::t()->fetch($id);
			if(!$article) {
				showmessage('article_does_not_exist');
			}
			if(in_array($article['status'], [1, 2])) {
				showmessage('moderate_article_not_share');
			}

			require_once libfile('function/portal');
			$article_url = fetch_article_url($article);
			$arr['itemid'] = $id;
			$arr['body_template'] = '<h5><a href="{url}" target="_blank">{title}</a></h5><p><a href="home.php?mod=space&uid={uid}">{username}</a>: {dateline} </p>';
			$arr['body_data'] = [
				'url' => $article_url,
				'title' => $article['title'],
				'uid' => $article['uid'],
				'articleid' => $id,
				'username' => $article['username'],
				'dateline' => dgmdate($article['dateline']),
				'summary' => getstr($article['summary'], 150, 0, 0, 0, -1)
			];
			if($article['pic']) {
				$arr['image'] = $arr['body_data']['image'] = pic_get($article['pic'], 'portal', $article['thumb'], $article['remote'], 1, 1);
				$arr['image_link'] = $arr['body_data']['image_link'] = $article_url;
			}
			$note_uid = $article['uid'];
			$note_message = 'share_article';
			$note_values = ['url' => $article_url, 'subject' => $article['title'], 'from_id' => $id, 'from_idtype' => 'aid'];

			break;
		case 'doing':
			$feed_hash_data = "doingid{$id}";
			$sdoing = table_home_doing::t()->fetch($id);
			if(!$sdoing) {
				showmessage('doing_does_not_exist');
			}
			$arr['itemid'] = $id;
			$arr['body_template'] = '<p><a href="{url}" target="_blank">{message}</a></p><p><a href="home.php?mod=space&uid={uid}">{username}</a> - {dateline} </p>';
			$arr['body_data'] = [
				'url' => "home.php?mod=space&do=doing&doid={$sdoing['doid']}",
				'message' => getstr($sdoing['message'], 150, 0, 0, 0, -1),
				'uid' => $sdoing['uid'],
				'doid' => $sdoing['doid'],
				'username' => $sdoing['username'],
				'dateline' => dgmdate($sdoing['dateline']),
			];
			
			$attachment = table_home_doing_attachment::t()->fetch_max_image(0, 'doid', $id);
			if($attachment) {
				$arr['image'] = $arr['body_data']['image'] = getdiscuzimg('doing', $attachment['aid'], 0, 140, 140);
				$arr['image_link'] = $arr['body_data']['image_link'] = "home.php?mod=space&do=doing&doid={$sdoing['doid']}";
			}
			break;
		default:
			$arr['itemid'] = 0;
			$arr['body_template'] = '';
			break;
	}
	$commentcable = ['blog' => 'blogid', 'pic' => 'picid', 'thread' => 'thread', 'article' => 'article', 'doing' => 'doid'];

	if(submitcheck('addsubmit')) {

		if(!checkperm('allowdoing')) {
			showmessage('no_privilege_doing');
		}

		cknewuser();

		$waittime = interval_check('post');
		if($waittime > 0) {
			showmessage('operating_too_fast', '', ['waittime' => $waittime]);
		}

		$message = getstr($_POST['message'], 200, 0, 0, 1);
		$message = preg_replace('/\<br.*?\>/i', ' ', $message);
		if(strlen($message) < 1) {
			showmessage('should_write_that');
		}

		$message = censor($message, NULL, TRUE, false);
		if(is_array($message) && $message['message']) {
			showmessage($message['message'], dreferer(), ['message' => $message['message']]);
		}

		if(censormod($message) || $_G['group']['allowdoingmod']) {
			$doing_status = 1;
		} else {
			$doing_status = 0;
		}

		
		if($_G['group']['allowat']) {
			$atlist = parse_at_user($message);
			if($atlist) {
				
				$atsearch = [];
				$atreplace = [];
				foreach($atlist as $atuid => $atusername) {
					$atsearch[] = '/@'.preg_quote($atusername, '/').' /i';
					$atreplace[] = '<a href="home.php?mod=space&uid='.$atuid.'" class="atuser" target="_blank" c="1">@'.$atusername.'</a> ';

					
					$atsearch[] = '/@\['.preg_quote($atusername, '/').'\]/i';
					$atreplace[] = '<a href="home.php?mod=space&uid='.$atuid.'" class="atuser" target="_blank" c="1">@'.$atusername.'</a> ';
				}
				$updated_message = preg_replace($atsearch, $atreplace, $message.' ', 1);
				$updated_message = substr($updated_message, 0, strlen($updated_message) - 1);
				
				
				$updatefields['at'] = $atlist;
				$message = $updated_message;
			}
		}

		$arr['body_data'] = serialize($arr['body_data']);
		$setarr = [
			'itemid' => $arr['itemid'],
			'type' => $type,
			'uid' => $_G['uid'],
			'username' => $_G['username'],
			'dateline' => $_G['timestamp'],
			'body_template' => $arr['body_template'],
			'body_data' => $arr['body_data'],
			'message' => $message,
			'ip' => $_G['clientip'],
			'port' => $_G['remoteport'],
			'recomends' => 0,
			'status' => $doing_status,
		];
		$newdoid = table_home_doing::t()->insert($setarr, 1);

		
		$class_tag = new tag();
		$tags = '';
		
		preg_match_all('/#([^#]+)#/', $message, $matches);
		if (!empty($matches[1])) {
			$tags = implode(',', $matches[1]) . ',';
			
			if($tags) {
				$tagsarr = $class_tag->add_tag($tags, $newdoid, 'doid',true);
			}
		}
		if($tagsarr) {
			$updatefields['tags'] = $tagsarr;
		}
		if($atlist) {
			
			foreach($atlist as $atuid => $atusername) {
				notification_add($atuid, 'at', 'at_doing', array('from_id' => $newdoid, 'from_idtype' => 'at', 'buyerid' => $_G['uid'], 'buyer' => $_G['username'], 'doid' => $newdoid));
			}
		}
		$updatearr['fields'] = !empty($updatefields) ? json_encode($updatefields) : '{}';
		table_home_doing::t()->update($newdoid, $updatearr);

		$setarr = ['recentnote' => $message, 'spacenote' => $message];
		$credit = $experience = 0;
		$extrasql = ['doings' => 1];

		updatecreditbyaction('doing', 0, $extrasql);

		table_common_member_field_home::t()->update($_G['uid'], $setarr);
		if (!empty($_POST['imageaids'])) {
			$imageaids = explode(',', $_POST['imageaids']);
			$imageaids = array_map('intval', $imageaids);
			$imageaids = array_filter($imageaids); 
			
			if (!empty($imageaids)) {
				table_home_doing_attachment::t()->update_by_aid($imageaids, ['doid' => $newdoid]);
			}
		}
		if (!empty($_FILES)) {
			$upload = new upload('doing');
			$f = $upload->upload();

			
			$photos = [];
			if (!empty($f['photos'])) {
				
				if (isset($f['photos'][0])) {
					
					$photos = $f['photos'];
				} else {
					
					$photos[] = $f['photos'];
				}
			}

			
			if (!empty($photos)) {
				foreach ($photos as $key => $value) {
					if (!$value['attachment']) continue;

					
					table_home_doing_attachment::t()->insert_attachment([
						'doid' => $newdoid,
						'uid' => $_G['uid'],
						'dateline' => TIMESTAMP,
						'filename' => $value['name'],
						'filesize' => $value['size'],
						'attachment' => $value['attachment'],
						'remote' => $value['remote'],
						'isimage' => $value['isimage'],
						'width' => $value['imageinfo'][0],
						'height' => $value['imageinfo'][1],
						'displayorder' => $key
					], true);
				}
			}
		}
		if($_GET['iscomment'] && $message && $commentcable[$type] && $id) {

			$message = censor($message);

			$currenttype = $commentcable[$type];
			$currentid = $id;

			if($currenttype == 'article') {
				$article = table_portal_article_title::t()->fetch($currentid);
				include_once libfile('function/portal');
				loadcache('portalcategory');
				$cat = $_G['cache']['portalcategory'][$article['catid']];
				$article['allowcomment'] = !empty($cat['allowcomment']) && !empty($article['allowcomment']) ? 1 : 0;
				if(!$article['allowcomment']) {
					showmessage('no_privilege_commentadd', '', [], ['return' => true]);
				}
				if($article['idtype'] == 'blogid') {
					$currentid = $article['id'];
					$currenttype = 'blogid';
				} elseif($article['idtype'] == 'tid') {
					$currentid = $article['id'];
					$currenttype = 'thread';
				}
			}

			if($currenttype == 'thread') {
				if($commentcable[$type] == 'article') {
					$_POST['portal_referer'] = $article_url ? $article_url : 'portal.php?mod=view&aid='.$id;
				}

				$_G['setting']['seccodestatus'] = 0;
				$_G['setting']['secqaa']['status'] = 0;

				$_POST['replysubmit'] = true;
				$_GET['tid'] = $currentid;
				$_GET['action'] = 'reply';
				$_GET['message'] = $message;
				include_once libfile('function/forum');
				require_once libfile('function/post');
				loadforum();

				$inspacecpshare = 1;
				include_once appfile('module/post', 'forum');

				if($_POST['portal_referer']) {
					$redirecturl = $_POST['portal_referer'];
				} else {
					if($modnewreplies) {
						$redirecturl = 'forum.php?mod=viewthread&tid='.$currentid;
					} else {
						$redirecturl = 'forum.php?mod=viewthread&tid='.$currentid.'&pid='.$modpost->pid.'&page='.$modpost->param('page').'&extra='.$extra.'#pid'.$modpost->pid;
					}
				}
				$showmessagecontent = ($modnewreplies && $commentcable[$type] != 'article') ? 'do_success_thread_share_mod' : '';

			} elseif($currenttype == 'article') {

				if(!checkperm('allowcommentarticle')) {
					showmessage('group_nopermission', NULL, ['grouptitle' => $_G['group']['grouptitle']], ['login' => 1]);
				}

				include_once libfile('function/spacecp');
				include_once libfile('function/portalcp');

				cknewuser();

				$waittime = interval_check('post');
				if($waittime > 0) {
					showmessage('operating_too_fast', '', ['waittime' => $waittime], ['return' => true]);
				}

				$aid = intval($currentid);

				$retmessage = addportalarticlecomment($aid, $message);
				if($retmessage != 'do_success') {
					showmessage($retmessage);
				}

			} elseif($currenttype == 'picid' || $currenttype == 'blogid') {

				if(!checkperm('allowcomment')) {
					showmessage('no_privilege_comment', '', [], ['return' => true]);
				}
				cknewuser();
				$waittime = interval_check('post');
				if($waittime > 0) {
					showmessage('operating_too_fast', '', ['waittime' => $waittime], ['return' => true]);
				}
				$message = getstr($message, 0, 0, 0, 2);
				if(strlen($message) < 2) {
					showmessage('content_is_too_short', '', [], ['return' => true]);
				}
				include_once libfile('class/bbcode');
				$bbcode = &bbcode::instance();

				require_once libfile('function/comment');
				$cidarr = add_comment($message, $currentid, $currenttype, 0);
				if($cidarr['cid']) {
					$magvalues['cid'] = $cidarr['cid'];
					$magvalues['id'] = $currentid;
				}
			} elseif($currenttype == 'doid') {
				$commentdo = table_home_doing::t()->fetch($currentid);
				$message = getstr($message, 200, 0, 0, 1);
				$message = preg_replace('/\<br.*?\>/i', ' ', $message);

				$setarr = [
					'doid' => $currentid,
					'upid' => 0,
					'uid' => $_G['uid'],
					'username' => $_G['username'],
					'dateline' => $_G['timestamp'],
					'message' => $message,
					'ip' => $_G['clientip'],
					'port' => $_G['remoteport'],
					'grade' => 1
				];

				$newid = table_home_docomment::t()->insert($setarr, true);

				table_home_doing::t()->update_replynum_by_doid(1, $currentid);

				if($commentdo['uid'] != $_G['uid']) {
					notification_add($commentdo['uid'], 'comment', 'doing_reply', [
						'url' => "home.php?mod=space&uid={$commentdo['uid']}&do=doing&view=me&doid={$commentdo['doid']}&highlight=$newid",
						'from_id' => $commentdo['doid'],
						'from_idtype' => 'doid'
					]);
					updatecreditbyaction('comment', 0, [], 'doing'.$commentdo['doid']);
				}

				include_once libfile('function/stat');
				updatestat('docomment');
			}
			$magvalues['type'] = $commentcable[$type];
		}
		
		if(helper_access::check_module('feed') && ckprivacy('doing', 'feed') && $doing_status == '0') {
			$feedarr = [
				'icon' => 'doing',
				'uid' => $_G['uid'],
				'username' => $_G['username'],
				'dateline' => $_G['timestamp'],
				'title_template' => lang('feed', 'feed_doing_title'),
				'title_data' => serialize(['message' => $message]),
				'body_template' => $arr['body_template'],
				'body_data' => $arr['body_data'],
				'id' => $newdoid,
				'idtype' => 'doid'
			];
			table_home_feed::t()->insert($feedarr);
		}
		if($doing_status == '1') {
			updatemoderate('doid', $newdoid);
			manage_addnotify('verifydoing');
		}
		require_once libfile('function/stat');
		updatestat('doing');
		table_common_member_status::t()->update($_G['uid'], ['lastpost' => TIMESTAMP], 'UNBUFFERED');
		if($type) {
			switch($type) {
				case 'space':
					table_common_member_status::t()->increase($id, ['sharetimes' => 1]);
					break;
				case 'blog':
					table_home_blog::t()->increase($id, null, ['sharetimes' => 1]);
					break;
				case 'album':
					table_home_album::t()->update_num_by_albumid($id, 1, 'sharetimes');
					break;
				case 'pic':
					table_home_pic::t()->update_sharetimes($id);
					break;
				case 'thread':
					table_forum_thread::t()->increase($id, ['sharetimes' => 1]);
					require_once libfile('function/forum');
					update_threadpartake($id);
					break;
				case 'article':
					table_portal_article_count::t()->increase($id, ['sharetimes' => 1]);
					break;
				case 'doing':
					table_home_doing::t()->increase($id, ['sharetimes' => 1]);
					break;
			}
			updatestat('share');
			if($note_uid && $note_uid != $_G['uid']) {
				notification_add($note_uid, 'sharenotice', $note_message, $note_values);
			}
		}
		if(!empty($_GET['fromcard'])) {
			showmessage($message.lang('spacecp', 'card_update_doing'));
		} else {
			showmessage('do_success', dreferer(), ['doid' => $newdoid], $_GET['spacenote'] ? ['showmsg' => false] : ['header' => true]);
		}

	} elseif(submitcheck('commentsubmit')) {

		if(!checkperm('allowdoing')) {
			showmessage('no_privilege_doing_comment');
		}
		cknewuser();

		$waittime = interval_check('post');
		if($waittime > 0) {
			showmessage('operating_too_fast', '', ['waittime' => $waittime]);
		}

		$message = getstr($_POST['message'], 200, 0, 0, 1);
		$message = preg_replace('/\<br.*?\>/i', ' ', $message);
		if(strlen($message) < 1) {
			showmessage('should_write_that');
		}
		$message = censor($message);


		$updo = [];
		if($docid) {
			$updo = table_home_docomment::t()->fetch($docid);
		}
		if(empty($updo) && $doid) {
			$updo = table_home_doing::t()->fetch($doid);
		}
		if(empty($updo)) {
			showmessage('docomment_error');
		} else {
			if(isblacklist($updo['uid'])) {
				showmessage('is_blacklist');
			}
		}

		$updo['id'] = intval($updo['id']);
		$updo['grade'] = intval($updo['grade']);

		$replyUid = $updo['uid'];
		$replyUsername = $updo['username'];
		
		$setarr = [
			'doid' => $updo['doid'],
			'upid' => $updo['id'],
			'uid' => $_G['uid'],
			'username' => $_G['username'],
			'dateline' => $_G['timestamp'],
			'message' => $message,
			'ip' => $_G['clientip'],
			'port' => $_G['remoteport'],
			'grade' => $updo['upid'] == 0 ? 2 : 2
		];

		$newid = table_home_docomment::t()->insert($setarr, true);

		table_home_doing::t()->update_replynum_by_doid(1, $updo['doid']);

		$notifyUid = $updo['uid'];
		$notifyMessage = getstr($updo['message'], 120, 0, 0, 0);

		if($notifyUid != $_G['uid']) {
			notification_add($notifyUid, 'comment', 'doing_reply', [
				'url' => "home.php?mod=space&uid={$notifyUid}&do=doing&view=me&doid={$updo['doid']}&highlight=$newid",
				'summery' => $notifyMessage,
				'from_id' => $updo['doid'],
				'from_idtype' => 'doid',
			]);
			updatecreditbyaction('comment', 0, [], 'doing'.$updo['doid']);
		}

		include_once libfile('function/stat');
		updatestat('docomment');
		table_common_member_status::t()->update($_G['uid'], ['lastpost' => TIMESTAMP], 'UNBUFFERED');
		showmessage('do_success', dreferer(), ['doid' => $updo['doid']]);
	}
}
require_once libfile('function/share');
$arr = mkshare($arr);
$arr['dateline'] = $_G['timestamp'];
$share_count = table_home_doing::t()->count_by_uid_itemid_type(null, $id ? $id : '', $type ? $type : '');
include template('home/spacecp_doing');