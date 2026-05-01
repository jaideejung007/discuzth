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

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class extend_thread_follow extends extend_thread_base {

	public function after_newthread() {
		$tid = $this->tid;
		$pid = $this->pid;
		$uid = $this->member['uid'];
		if($this->param['displayorder'] >= 0 && helper_access::check_module('follow') && !$this->param['isanonymous']) {
			$values = [];
			require_once libfile('function/discuzcode');
			require_once libfile('function/followcode');
			$feedcontent = [
				'tid' => $tid,
				'content' => followcode($this->param['message'], $tid, $pid, 1000),
			];
			table_forum_threadpreview::t()->insert($feedcontent);
			table_forum_thread::t()->update_status_by_tid($tid, '512');
			$followfeed = [
				'uid' => $uid,
				'username' => $this->member['username'],
				'tid' => $tid,
				'note' => '',
				'dateline' => TIMESTAMP
			];
			$values['feedid'] = table_home_follow_feed::t()->insert($followfeed, true);
			table_common_member_count::t()->increase($uid, ['feeds' => 1]);

			$this->param['values'] = array_merge((array)$this->param['values'], $values);
		}

	}

	public function after_newreply() {
		$feedid = 0;
		if(helper_access::check_module('follow') && !$this->param['isanonymous']) {
			require_once libfile('function/discuzcode');
			require_once libfile('function/followcode');
			$feedcontent = table_forum_threadpreview::t()->count_by_tid($this->thread['tid']);
			$firstpost = table_forum_post::t()->fetch_threadpost_by_tid_invisible($this->thread['tid']);

			if(empty($feedcontent)) {
				$feedcontent = [
					'tid' => $this->thread['tid'],
					'content' => followcode($firstpost['message'], $this->thread['tid'], $this->pid, 1000),
				];
				table_forum_threadpreview::t()->insert($feedcontent);
				table_forum_thread::t()->update_status_by_tid($this->thread['tid'], '512');
			} else {
				table_forum_threadpreview::t()->update_relay_by_tid($this->thread['tid'], 1);
			}
			$notemsg = cutstr(followcode($this->param['message'], $this->thread['tid'], $this->pid, 0, false), 140);
			$followfeed = [
				'uid' => $this->member['uid'],
				'username' => $this->member['username'],
				'tid' => $this->thread['tid'],
				'note' => $notemsg,
				'dateline' => TIMESTAMP
			];
			$feedid = table_home_follow_feed::t()->insert($followfeed, true);
			table_common_member_count::t()->increase($this->member['uid'], ['feeds' => 1]);
		}
		if($feedid) {
			$this->param['showmsgparam'] = array_merge((array)$this->param['showmsgparam'], ['feedid' => $feedid]);
		}
	}

	public function after_editpost() {
		$isfirstpost = $this->post['first'] ? 1 : 0;
		if($isfirstpost) {
			require_once libfile('function/discuzcode');
			require_once libfile('function/followcode');
			$feed = table_forum_threadpreview::t()->fetch($this->thread['tid']);
			if($feed) {
				table_forum_threadpreview::t()->update($this->thread['tid'], ['content' => followcode($this->param['message'], $this->thread['tid'], $this->post['pid'], 1000)]);
			}
		}
	}
}

