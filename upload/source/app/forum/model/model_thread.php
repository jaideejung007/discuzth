<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace forum;

use discuz_model;
use table_common_member_field_home;
use table_forum_attachment_n;
use table_forum_forum;
use table_forum_forumfield;
use table_forum_groupuser;
use table_forum_newthread;
use table_forum_post_location;
use table_forum_sofa;
use table_forum_thread;
use tag;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class model_thread extends discuz_model {

	public $forum;

	public $thread;

	public $post;

	public $tid;

	public $pid;

	public $feed = [];

	public function __construct($fid = null) {
		parent::__construct();
		if($fid) {
			include_once libfile('function/forum');
			loadforum($fid);
		}
		$this->forum = &$this->app->var['forum'];
	}

	public function newthread($parameters) {

		require_once libfile('function/post');
		$this->tid = $this->pid = 0;
		$this->_init_parameters($parameters);

		if(!empty($this->param['save']) && !$this->group['allowsave']) {
			return $this->showmessage('post_not_allow_save');
		}

		if(trim($this->param['subject']) == '') {
			return $this->showmessage('post_sm_isnull');
		}

		if(!$this->param['sortid'] && (!$this->setting['json_independence'] && !$this->param['special']) && ((in_array($this->param['contentType'], ['text', '']) && empty(trim($this->param['message']))) || (!empty($this->param['contentType']) && $this->param['contentType'] != 'text' && empty(trim($this->param['content']))))) {
			return $this->showmessage('post_sm_isnull');
		}
		list($this->param['modnewthreads'], $this->param['modnewreplies']) = threadmodstatus($this->param['subject']."\t".$this->param['message']."\t".$this->param['content'].$this->param['extramessage']);

		if(($post_invalid = checkpost($this->param['subject'], $this->param['message'], ($this->param['special'] || $this->param['sortid']), $this->param['contentType'] == 'json' && !empty(trim($this->param['content']))))) {
			return $this->showmessage($post_invalid, '', ['minpostsize' => $this->setting['minpostsize'], 'maxpostsize' => $this->setting['maxpostsize']]);
		}

		if(checkflood()) {
			return $this->showmessage('post_flood_ctrl', '', ['floodctrl' => $this->setting['floodctrl']]);
		} elseif(checkmaxperhour('tid')) {
			return $this->showmessage('thread_flood_ctrl_threads_per_hour', '', ['threads_per_hour' => $this->group['maxthreadsperhour']]);
		}
		$this->param['save'] = $this->member['uid'] ? $this->param['save'] : 0;

		$this->param['typeid'] = isset($this->param['typeid']) && isset($this->forum['threadtypes']['types'][$this->param['typeid']]) && (!$this->forum['threadtypes']['moderators'][$this->param['typeid']] || $this->forum['ismoderator']) ? $this->param['typeid'] : 0;

		$this->param['displayorder'] = $this->param['modnewthreads'] ? -2 : (($this->forum['ismoderator'] && $this->group['allowstickthread'] && !empty($this->param['sticktopic'])) ? 1 : (empty($this->param['save']) ? 0 : -4));
		if($this->param['displayorder'] == -2) {
			table_forum_forum::t()->update($this->forum['fid'], ['modworks' => '1']);
		} elseif($this->param['displayorder'] == -4 && !empty($this->group['allowsavenum']) && table_forum_thread::t()->count_by_authorid_displayorder($this->member['uid'], -4) >= intval($this->group['allowsavenum'])) {
			return $this->showmessage('post_max_save');
		}

		$this->param['digest'] = $this->forum['ismoderator'] && $this->group['allowdigestthread'] && !empty($this->param['digest']) ? 1 : 0;
		$this->param['readperm'] = $this->group['allowsetreadperm'] ? $this->param['readperm'] : 0;
		$this->param['isanonymous'] = $this->group['allowanonymous'] && $this->param['isanonymous'] ? 1 : 0;
		$this->param['price'] = intval($this->param['price']);
		if(!$this->param['special']) {
			$this->param['price'] = $this->group['maxprice'] ? ($this->param['price'] <= $this->group['maxprice'] ? $this->param['price'] : $this->group['maxprice']) : 0;
		}

		if(!$this->param['typeid'] && !empty($this->forum['threadtypes']['required']) && !$this->param['special']) {
			return $this->showmessage('post_type_isnull');
		}

		if(!$this->param['sortid'] && !empty($this->forum['threadsorts']['required']) && !$this->param['special']) {
			return $this->showmessage('post_sort_isnull');
		}

		if(!$this->param['special'] && $this->param['price'] > 0 && floor($this->param['price'] * (1 - $this->setting['creditstax'])) == 0) {
			return $this->showmessage('post_net_price_iszero');
		}


		$this->param['sortid'] = $this->param['special'] || !$this->forum['threadsorts']['types'][$this->param['sortid']] ? 0 : $this->param['sortid'];
		$this->param['typeexpiration'] = intval($this->param['typeexpiration']);

		if(!empty($this->forum['threadsorts']['expiration'][$this->param['typeid']]) && !$this->param['typeexpiration']) {
			return $this->showmessage('threadtype_expiration_invalid');
		}

		$author = !$this->param['isanonymous'] ? $this->member['username'] : '';

		$this->param['moderated'] = $this->param['digest'] || $this->param['displayorder'] > 0 ? 1 : 0;


		$this->param['ordertype'] && $this->param['tstatus'] = setstatus(4, 1, $this->param['tstatus']);

		$this->param['imgcontent'] && $this->param['tstatus'] = setstatus(15, $this->param['imgcontent'], $this->param['tstatus']);

		$this->param['hiddenreplies'] && $this->param['tstatus'] = setstatus(2, 1, $this->param['tstatus']);


		$this->param['allownoticeauthor'] && $this->param['tstatus'] = setstatus(6, 1, $this->param['tstatus']);
		$this->param['isgroup'] = $this->forum['status'] == 3 ? 1 : 0;

		$this->param['publishdate'] = !$this->param['modnewthreads'] ? $this->param['publishdate'] : TIMESTAMP;

		
		$summary = '';
		if($this->param['contentType'] == 'json' && $this->param['contentEditor'] == 'jsonEditor'
			&& is_valid_non_empty_json($this->param['content'], true)) {
			$ctmp = json_decode($this->param['content'], true);
			foreach($ctmp['blocks'] as $ckey => $cvalue) {
				if($cvalue['type'] == 'paragraph') {
					$summary .= $cvalue['data']['text'].';';
				}
			}
			$summary = str_replace(["\r", "\n"], '', messagecutstr(strip_tags($summary), 200));
			$sppos = strpos($this->param['message'], chr(0).chr(0).chr(0));
			$specialextra = substr($this->param['message'], $sppos + 3);
			$this->param['message'] = $summary;
			if($sppos !== false) {
				$this->param['message'] .= chr(0).chr(0).chr(0).$specialextra;
			}
		} elseif(!empty($this->param['message'])) {
			$summary = str_replace(["\r", "\n"], '', messagecutstr(strip_tags(preg_replace('/(\[(.+?)\](.+?)\[\/(.+?)\])/is', '$1', $this->param['message'])), 200));
		} else {
			$summary = '';
		}

		$newthread = [
			'fid' => $this->forum['fid'],
			'posttableid' => 0,
			'readperm' => $this->param['readperm'],
			'price' => $this->param['price'],
			'typeid' => $this->param['typeid'],
			'sortid' => $this->param['sortid'],
			'author' => $author,
			'authorid' => $this->member['uid'],
			'subject' => $this->param['subject'],
			'summary' => $summary,
			'dateline' => $this->param['publishdate'],
			'lastpost' => $this->param['publishdate'],
			'lastposter' => $author,
			'displayorder' => $this->param['displayorder'],
			'digest' => $this->param['digest'],
			'special' => $this->param['special'],
			'attachment' => 0,
			'moderated' => $this->param['moderated'],
			'status' => $this->param['tstatus'] ?? 0,
			'isgroup' => $this->param['isgroup'],
			'replycredit' => $this->param['replycredit'] ?? 0,
			'closed' => $this->param['closed'] ? 1 : 0
		];
		$this->tid = table_forum_thread::t()->insert($newthread, true);
		table_forum_newthread::t()->insert([
			'tid' => $this->tid,
			'fid' => $this->forum['fid'],
			'dateline' => $this->param['publishdate'],
		]);
		table_forum_sofa::t()->insert(['tid' => $this->tid, 'fid' => $this->forum['fid']]);
		useractionlog($this->member['uid'], 'tid');

		if(!getuserprofile('threads') && $this->setting['newbie']) {
			table_forum_thread::t()->update($this->tid, ['icon' => $this->setting['newbie']]);
		}
		if($this->param['publishdate'] != TIMESTAMP) {
			$cron_publish_ids = $this->cache('cronpublish');
			$cron_publish_ids[$this->tid] = $this->tid;
			savecache('cronpublish', $cron_publish_ids);
		}

		if(!$this->param['isanonymous']) {
			table_common_member_field_home::t()->update($this->member['uid'], ['recentnote' => $this->param['subject']]);
		}

		if($this->param['moderated']) {
			if($this->param['displayorder'] > 0) {
				updatemodlog($this->tid, 'STK');
				updatemodworks('STK', 1);
			}
			if($this->param['digest']) {
				updatemodlog($this->tid, 'DIG');
				updatemodworks('DIG', 1);
			}
		}

		$this->param['bbcodeoff'] = checkbbcodes($this->param['message'], !empty($this->param['bbcodeoff']));
		$this->param['smileyoff'] = checksmilies($this->param['message'], !empty($this->param['smileyoff']));
		$this->param['parseurloff'] = !empty($this->param['parseurloff']);
		$this->param['htmlon'] = $this->group['allowhtml'] && !empty($this->param['htmlon']) ? 1 : 0;
		$this->param['usesig'] = !empty($this->param['usesig']) && $this->group['maxsigsize'] ? 1 : 0;
		$class_tag = new tag();
		$this->param['tagstr'] = $class_tag->add_tag($this->param['tags'], $this->tid, 'tid');


		$this->param['pinvisible'] = $this->param['modnewthreads'] ? -2 : (empty($this->param['save']) ? 0 : -3);
		$this->param['message'] = preg_replace('/\[attachimg\](\d+)\[\/attachimg\]/is', '[attach]\1[/attach]', $this->param['message']);

		$this->param['pstatus'] = intval($this->param['pstatus']);
		defined('IN_MOBILE') && $this->param['pstatus'] = setstatus(4, 1, $this->param['pstatus']);

		if($this->param['imgcontent']) {
			stringtopic($this->param['message'], $this->tid, true, $this->param['imgcontentwidth']);
		}

		$contentType = $this->param['contentType'] ?? 'text';
		$contentEditor = $this->param['contentEditor'] ?? 'default';
		$content = generate_content_json($contentType, $contentEditor, (!empty($this->param['content']) ? $this->param['content'] : '{}'));

		$this->pid = insertpost([
			'fid' => $this->forum['fid'],
			'tid' => $this->tid,
			'first' => '1',
			'author' => $this->member['username'],
			'authorid' => $this->member['uid'],
			'subject' => $this->param['subject'],
			'original' => !empty($this->param['original']) ? $this->param['original'] : 0,
			'dateline' => $this->param['publishdate'],
			'message' => $this->param['message'],
			'content' => $content,
			'source' => !empty($this->param['source']) ? $this->param['source'] : '{}',
			'useip' => $this->param['clientip'] ? $this->param['clientip'] : getglobal('clientip'),
			'port' => $this->param['remoteport'] ? $this->param['remoteport'] : getglobal('remoteport'),
			'invisible' => $this->param['pinvisible'],
			'anonymous' => $this->param['isanonymous'],
			'usesig' => $this->param['usesig'],
			'htmlon' => $this->param['htmlon'],
			'bbcodeoff' => $this->param['bbcodeoff'],
			'smileyoff' => $this->param['smileyoff'],
			'parseurloff' => $this->param['parseurloff'],
			'attachment' => '0',
			'tags' => !empty($this->param['tagstr']) ? $this->param['tagstr'] : '',
			'replycredit' => 0,
			'status' => $this->param['pstatus']
		]);

		$statarr = [0 => 'thread', 1 => 'poll', 2 => 'trade', 3 => 'reward', 4 => 'activity', 5 => 'debate', 127 => 'thread'];
		include_once libfile('function/stat');
		updatestat($this->param['isgroup'] ? 'groupthread' : $statarr[$this->param['special']]);


		if($this->param['geoloc'] && defined('IN_MOBILE') && constant('IN_MOBILE') == 2) {
			list($mapx, $mapy, $location) = explode('|', $this->param['geoloc']);
			if($mapx && $mapy && $location) {
				table_forum_post_location::t()->insert([
					'pid' => $this->pid,
					'tid' => $this->tid,
					'uid' => $this->member['uid'],
					'mapx' => $mapx,
					'mapy' => $mapy,
					'location' => $location,
				]);
			}
		}

		if($this->param['modnewthreads']) {
			updatemoderate('tid', $this->tid);
			table_forum_forum::t()->update_forum_counter($this->forum['fid'], 0, 0, 1);
			manage_addnotify('verifythread');
			return 'post_newthread_mod_succeed';
		} else {

			if($this->param['displayorder'] != -4) {
				if($this->param['digest']) {
					updatepostcredits('+', $this->member['uid'], 'digest', $this->forum['fid']);
				}
				updatepostcredits('+', $this->member['uid'], 'post', $this->forum['fid']);
				if($this->param['isgroup']) {
					table_forum_groupuser::t()->update_counter_for_user($this->member['uid'], $this->forum['fid'], 1);
				}

				$subject = str_replace("\t", ' ', $this->param['subject']);
				$subject = cutstr($subject, 80);
				$lastpost = "$this->tid\t".$subject."\t".TIMESTAMP."\t$author";
				table_forum_forum::t()->update($this->forum['fid'], ['lastpost' => $lastpost]);
				table_forum_forum::t()->update_forum_counter($this->forum['fid'], 1, 1, 1);
				if($this->forum['type'] == 'sub') {
					table_forum_forum::t()->update($this->forum['fup'], ['lastpost' => $lastpost]);
				}
			}

			if($this->param['isgroup']) {
				table_forum_forumfield::t()->update($this->forum['fid'], ['lastupdate' => TIMESTAMP]);
				require_once libfile('function/grouplog');
				updategroupcreditlog($this->forum['fid'], $this->member['uid']);
			}

			return 'post_newthread_succeed';

		}

	}

	public function feed() {
		if($this->forum('allowfeed') && !$this->param['isanonymous']) {
			if(empty($this->feed)) {
				$this->feed = [
					'icon' => '',
					'title_template' => '',
					'title_data' => [],
					'body_template' => '',
					'body_data' => [],
					'title_data' => [],
					'images' => []
				];

				$message = !$this->param['price'] && !$this->param['readperm'] ? $this->param['message'] : '';
				$message = messagesafeclear($message);
				$this->feed['icon'] = 'thread';
				$this->feed['title_template'] = 'feed_thread_title';
				$this->feed['body_template'] = 'feed_thread_message';
				$this->feed['body_data'] = [
					'subject' => "<a href=\"forum.php?mod=viewthread&tid={$this->tid}\">{$this->param['subject']}</a>",
					'message' => threadmessagecutstr($this->param, $message, 150)
				];
				if(getglobal('forum_attachexist')) {
					$imgattach = table_forum_attachment_n::t()->fetch_max_image('tid:'.$this->tid, 'pid', $this->pid);
					$firstaid = $imgattach['aid'];
					unset($imgattach);
					if($firstaid) {
						$this->feed['images'] = [getforumimg($firstaid)];
						$this->feed['image_links'] = ["forum.php?mod=viewthread&do=tradeinfo&tid={$this->tid}&pid={$this->pid}"];
					}
				}

			}


			$this->feed['title_data']['hash_data'] = 'tid'.$this->tid;
			$this->feed['id'] = $this->tid;
			$this->feed['idtype'] = 'tid';
			if($this->feed['icon']) {
				postfeed($this->feed);
			}
		}
	}

	protected function _init_parameters($parameters) {

		$varname = [
			'member', 'group', 'forum', 'extramessage',
			'subject', 'sticktopic', 'save', 'ordertype', 'hiddenreplies',
			'allownoticeauthor', 'readperm', 'price', 'typeid', 'sortid',
			'publishdate', 'digest', 'moderated', 'tstatus', 'isgroup', 'imgcontent', 'imgcontentwidth',
			'replycredit', 'closed', 'special', 'tags',
			'message', 'content', 'clientip', 'invisible', 'isanonymous', 'usesig',
			'htmlon', 'bbcodeoff', 'smileyoff', 'parseurloff', 'pstatus', 'geoloc', 'original', 'source', 'contentType', 'contentEditor'
		];
		foreach($varname as $name) {
			if(!isset($this->param[$name])) {
				$this->param[$name] = $parameters[$name] ?? null;
			}
		}

	}


	public function forum($name = null, $val = null) {
		if(isset($val)) {
			return $this->setvar($this->forum, $name, $val);
		} else {
			return $this->getvar($this->forum, $name);
		}
	}


}

