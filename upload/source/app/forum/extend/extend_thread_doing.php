<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace forum;

use helper_access;
use table_common_member_count;
use table_forum_post;
use table_forum_thread;
use table_forum_threadpreview;
use table_home_follow_feed;
use table_forum_attachment_n;
use table_home_doing;
use tag;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class extend_thread_doing extends extend_thread_base {

	public function after_newthread() {
		$tid = $this->tid;
		$pid = $this->pid;
		$uid = $this->member['uid'];

		if($this->param['displayorder'] >= 0 && helper_access::check_module('doing') && !$this->param['isanonymous'] && checkperm('allowdoing')) {
			require_once libfile('function/home');
			require_once libfile('function/post');
			$arr['body_template'] = '<h5><a href="{url}" target="_blank">{subject}</a></h5><p><a href="home.php?mod=space&uid={authorid}">{author}</a>: {dateline} </p>';
			$postmessage = threadmessagecutstr($this->param, $this->param['message']);
			$arr['body_data'] = [
				'url' => "forum.php?mod=viewthread&tid=".$tid,
				'subject' => $this->param['subject'],
				'authorid' => $uid,
				'author' => $this->member['username'],
				'tid' => $tid,
				'dateline' => dgmdate(TIMESTAMP),
				'message' => getstr($postmessage, 150, 0, 0, 0, -1)
			];
			$arr['type'] = 'thread';
			$arr['itemid'] = $tid;
			$attachment = table_forum_attachment_n::t()->fetch_max_image('tid:'.$tid, 'tid', $tid);
			if($attachment) {
				$arr['body_data']['image'] = pic_get($attachment['attachment'], 'forum', $attachment['thumb'], $attachment['remote'], 1);
				$arr['body_data']['image_link'] = "forum.php?mod=viewthread&tid=$tid";
			}
			$message = ($this->setting['doing_dynamic_fname'] ? '#'.$this->forum['name'].'# ' : '').lang('spacecp','share_thread').': '.$this->param['subject'];
			$doing_status = 0;
			$arr['body_data'] = serialize($arr['body_data']);
			$setarr = [
				'itemid' => $arr['itemid'],
				'type' => $arr['type'],
				'uid' => $uid,
				'username' => $this->member['username'],
				'dateline' => TIMESTAMP,
				'body_template' => $arr['body_template'],
				'body_data' => $arr['body_data'],
				'message' => $message,
				'ip' => getglobal('clientip'),
				'port' => getglobal('remoteport'),
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
			$updatearr['fields'] = !empty($updatefields) ? json_encode($updatefields) : '{}';
			table_home_doing::t()->update($newdoid, $updatearr);

			$extrasql = ['doings' => 1];

			updatecreditbyaction('doing', 0, $extrasql);
			require_once libfile('function/stat');
			updatestat('doing');
			table_forum_thread::t()->increase($tid, ['sharetimes' => 1]);
			require_once libfile('function/forum');
			update_threadpartake($tid);
			updatestat('share');
		}

	}
}

