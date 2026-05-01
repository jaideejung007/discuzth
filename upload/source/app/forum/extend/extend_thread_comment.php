<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace forum;

use table_forum_post;
use table_forum_postcache;
use table_forum_postcomment;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class extend_thread_comment extends extend_thread_base {

	private $postcomment;

	public function before_newreply($parameters) {
		global $nauthorid;
		list(, $this->param['modnewreplies']) = threadmodstatus($this->param['subject']."\t".$this->param['message'].$this->param['extramessage']);
		if($this->thread['displayorder'] == -4) {
			$this->param['modnewreplies'] = 0;
		}
		$pinvisible = $parameters['modnewreplies'] ? -2 : ($this->thread['displayorder'] == -4 ? -3 : 0);
		$this->postcomment = is_array($this->setting['allowpostcomment']) && in_array(2, $this->setting['allowpostcomment']) && $this->group['allowcommentreply'] && !$pinvisible && !empty($_GET['reppid']) && ($nauthorid != $this->member['uid'] || $this->setting['commentpostself']) ? messagecutstr($parameters['message'], 200, ' ') : '';
	}

	public function after_newreply() {
		if(!empty($_GET['noticeauthor']) && !$this->param['isanonymous'] && !$this->param['modnewreplies']) {
			if($this->postcomment) {
				$rpid = intval($_GET['reppid']);
				if($rpost = table_forum_post::t()->fetch_post('tid:'.$this->thread['tid'], $rpid)) {
					if(!$rpost['first']) {
						$cid = table_forum_postcomment::t()->insert([
							'tid' => $this->thread['tid'],
							'pid' => $rpid,
							'rpid' => $this->pid,
							'author' => $this->member['username'],
							'authorid' => $this->member['uid'],
							'dateline' => TIMESTAMP,
							'comment' => $this->postcomment,
							'score' => 0,
							'useip' => getglobal('clientip'),
							'port' => getglobal('remoteport')
						], true);

						table_forum_post::t()->update_post('tid:'.$this->thread['tid'], $rpid, ['comment' => 1]);
						table_forum_postcache::t()->delete($rpid);
					}
				}
				unset($this->postcomment);
			}
		}
	}

	public function after_deletepost() {
		table_forum_postcomment::t()->delete_by_rpid($this->post['pid']);
	}
}

