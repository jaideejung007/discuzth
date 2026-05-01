<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/post');

class cells_threadlist {

	public static function process($cellId) {
		global $_G, $threadlist, $forums, $groupnames;

		if(empty($_G['forum_threadlist'])) {
			$_G['forum_colorarray'] = ['', '#EE1B2E', '#EE5023', '#996600', '#3C9D40', '#2897C5', '#2B65B7', '#8F2A90', '#EC1282'];

			$fids = array_column($threadlist, 'fid', 'fid');
			$forums = table_forum_forum::t()->fetch_all_info_by_fids($fids);

			$todaytime = strtotime(dgmdate(TIMESTAMP, 'Ymd'));
			$thide = !empty($_G['cookie']['thide']) ? explode('|', $_G['cookie']['thide']) : [];
			$forumlastvisit = $threadindex = 0;
			$threadids = $rIndex = [];
			if(isset($_G['cookie']['forum_lastvisit']) && strexists($_G['cookie']['forum_lastvisit'], 'D_0')) {
				preg_match('/D\_0\_(\d+)/', $_G['cookie']['forum_lastvisit'], $a);
				$forumlastvisit = $a[1];
				unset($a);
			}
			dsetcookie('forum_lastvisit', preg_replace('/D\_0\_\d+/', '', getcookie('forum_lastvisit')).'D_0_'.TIMESTAMP, 604800);

			foreach($threadlist as $thread) {
				$_G['forum'] = &$forums[$thread['fid']];
				$_G['fid'] = $thread['fid'];
				$_G['forum']['threadtypes'] = dunserialize($_G['forum']['threadtypes']);
				$_G['forum']['threadsorts'] = dunserialize($_G['forum']['threadsorts']);
				if($_G['forum']['autoclose']) {
					$closedby = $_G['forum']['autoclose'] > 0 ? 'dateline' : 'lastpost';
					$_G['forum']['autoclose'] = abs($_G['forum']['autoclose']) * 86400;
				}

				$thread['allreplies'] = $thread['replies'] + $thread['comments'];
				$thread['ordertype'] = getstatus($thread['status'], 4);
				$thread['related_group'] = 0;

				$thread['lastposterenc'] = rawurlencode($thread['lastposter']);
				if($thread['typeid'] && !empty($_G['forum']['threadtypes']['prefix']) && isset($_G['forum']['threadtypes']['types'][$thread['typeid']])) {
					if($_G['forum']['threadtypes']['prefix'] == 1) {
						$thread['typehtml'] = '<em>[<a href="forum.php?mod=forumdisplay&fid='.$_G['fid'].'&amp;filter=typeid&amp;typeid='.$thread['typeid'].'">'.$_G['forum']['threadtypes']['types'][$thread['typeid']].'</a>]</em>';
					} elseif($_G['forum']['threadtypes']['icons'][$thread['typeid']] && $_G['forum']['threadtypes']['prefix'] == 2) {
						$thread['typehtml'] = '<em><a title="'.strip_tags($_G['forum']['threadtypes']['types'][$thread['typeid']]).'" href="forum.php?mod=forumdisplay&fid='.$_G['fid'].'&amp;filter=typeid&amp;typeid='.$thread['typeid'].'">'.'<img style="vertical-align: middle;padding-right:4px;" src="'.$_G['forum']['threadtypes']['icons'][$thread['typeid']].'" alt="'.strip_tags($_G['forum']['threadtypes']['types'][$thread['typeid']]).'" /></a></em>';
					}
					$thread['typename'] = $_G['forum']['threadtypes']['types'][$thread['typeid']];
				} else {
					$thread['typename'] = $thread['typehtml'] = '';
				}

				$thread['sorthtml'] = $thread['sortid'] && !empty($_G['forum']['threadsorts']['prefix']) && isset($_G['forum']['threadsorts']['types'][$thread['sortid']]) ?
					'<em>[<a href="forum.php?mod=forumdisplay&fid='.$_G['fid'].'&amp;filter=sortid&amp;sortid='.$thread['sortid'].'">'.$_G['forum']['threadsorts']['types'][$thread['sortid']].'</a>]</em>' : '';
				$thread['multipage'] = '';
				$topicposts = $thread['special'] ? $thread['replies'] : $thread['replies'] + 1;
				if($topicposts > $_G['ppp']) {
					$pagelinks = '';
					$thread['pages'] = ceil($topicposts / $_G['ppp']);
					$realtid = $_G['forum']['status'] != 3 && $thread['isgroup'] == 1 ? $thread['closed'] : $thread['tid'];
					for($i = 2; $i <= 6 && $i <= $thread['pages']; $i++) {
						$pagelinks .= "<a href=\"forum.php?mod=viewthread&tid=$realtid&amp;page=$i\" onclick=\"atarget(this)\">$i</a>";
					}
					if($thread['pages'] > 6) {
						$pagelinks .= "..<a href=\"forum.php?mod=viewthread&tid=$realtid&amp;page=$thread[pages]\" onclick=\"atarget(this)\">$thread[pages]</a>";
					}
					$thread['multipage'] = '&nbsp;...'.$pagelinks;
				}

				if($thread['highlight']) {
					$string = sprintf('%02d', $thread['highlight']);
					$stylestr = sprintf('%03b', $string[0]);

					$thread['highlight'] = ' style="';
					$thread['highlight'] .= $stylestr[0] ? 'font-weight: bold;' : '';
					$thread['highlight'] .= $stylestr[1] ? 'font-style: italic;' : '';
					$thread['highlight'] .= $stylestr[2] ? 'text-decoration: underline;' : '';
					$thread['highlight'] .= $string[1] ? 'color: '.$_G['forum_colorarray'][$string[1]].';' : '';
					if($thread['bgcolor']) {
						$thread['highlight'] .= "background-color: {$thread['bgcolor']};";
					}
					$thread['highlight'] .= '"';
				} else {
					$thread['highlight'] = '';
				}

				$thread['recommendicon'] = '';
				if(!empty($_G['setting']['recommendthread']['status']) && $thread['recommends']) {
					foreach($_G['setting']['recommendthread']['iconlevels'] as $k => $i) {
						if($thread['recommends'] > $i) {
							$thread['recommendicon'] = $k + 1;
							break;
						}
					}
				}

				$thread['moved'] = $thread['heatlevel'] = $thread['new'] = 0;
				if($_G['forum']['status'] != 3 && ($thread['closed'] || ($_G['forum']['autoclose'] && $thread['fid'] == $_G['fid'] && TIMESTAMP - $thread[$closedby] > $_G['forum']['autoclose']))) {
					if($thread['isgroup'] == 1) {
						$thread['folder'] = 'common';
						$grouptids[$thread['closed']] = $thread['closed'];
					} else {
						if($thread['closed'] > 1) {
							$thread['moved'] = $thread['tid'];
							$thread['allreplies'] = $thread['replies'] = '-';
							$thread['views'] = '-';
						}
						$thread['folder'] = 'lock';
					}
				} elseif($_G['forum']['status'] == 3 && $thread['closed'] == 1) {
					$thread['folder'] = 'lock';
				} else {
					$thread['folder'] = 'common';
					$thread['weeknew'] = TIMESTAMP - 604800 <= $thread['dateline'];
					if($thread['allreplies'] > $thread['views']) {
						$thread['views'] = $thread['allreplies'];
					}
					if($_G['setting']['heatthread']['iconlevels']) {
						foreach($_G['setting']['heatthread']['iconlevels'] as $k => $i) {
							if($thread['heats'] > $i) {
								$thread['heatlevel'] = $k + 1;
								break;
							}
						}
					}
				}
				$thread['icontid'] = $thread['forumstick'] || !$thread['moved'] && $thread['isgroup'] != 1 ? $thread['tid'] : $thread['closed'];
				if(!$thread['forumstick'] && ($thread['isgroup'] == 1 || $thread['fid'] != $_G['fid'])) {
					$thread['icontid'] = $thread['closed'] > 1 ? $thread['closed'] : $thread['tid'];
				}
				$thread['istoday'] = $thread['dateline'] > $todaytime ? 1 : 0;
				$thread['dbdateline'] = $thread['dateline'];
				$thread['dateline'] = dgmdate($thread['dateline'], 'u', '9999', getglobal('setting/dateformat'));
				$thread['dblastpost'] = $thread['lastpost'];
				$thread['lastpost'] = dgmdate($thread['lastpost'], 'u');
				$thread['hidden'] = $_G['setting']['threadhidethreshold'] && $thread['hidden'] >= $_G['setting']['threadhidethreshold'] || in_array($thread['tid'], $thide);
				if($thread['hidden']) {
					$_G['hiddenexists']++;
				}

				if(in_array($thread['displayorder'], [1, 2, 3, 4])) {
					$thread['id'] = 'stickthread_'.$thread['tid'];
				} else {
					$thread['id'] = 'normalthread_'.$thread['tid'];
					if($thread['folder'] == 'common' && $thread['dblastpost'] >= $forumlastvisit || !$forumlastvisit) {
						$thread['new'] = 1;
						$thread['folder'] = 'new';
						$thread['weeknew'] = TIMESTAMP - 604800 <= $thread['dbdateline'];
					}
					$_G['showrows']++;
				}
				if(isset($_G['setting']['verify']['enabled']) && $_G['setting']['verify']['enabled']) {
					$verifyuids[$thread['authorid']] = $thread['authorid'];
				}
				$authorids[$thread['authorid']] = $thread['authorid'];
				$thread['mobile'] = base_convert(getstatus($thread['status'], 13).getstatus($thread['status'], 12).getstatus($thread['status'], 11), 2, 10);
				$thread['rushreply'] = getstatus($thread['status'], 3);
				if($thread['rushreply']) {
					$rushtids[$thread['tid']] = $thread['tid'];
				}
				$threadids[$threadindex] = $thread['tid'];
				$_G['forum_threadlist'][$threadindex] = $thread;
				$rIndex[$thread['tid']] = $threadindex;

				if($_G['forum']['status'] == 3) {
					$groupnames[$thread['tid']] = [
						'fid' => $thread['fid'],
						'views' => $thread['views'],
						'name' => $_G['forum']['name'],
						'type' => $_G['forum']['type'],
						'status' => $_G['forum']['status'],
					];
					unset($grouptids[$thread['tid']]);
				}
				$threadindex++;
			}
			if(!defined('IN_RESTFUL')) {
				unset($_G['fid']);
				unset($_G['forum']);
			}

			if($rushtids) {
				$rushinfo = table_forum_threadrush::t()->fetch_all($rushtids);
				foreach($rushinfo as $tid => $info) {
					if($info['starttimefrom'] > TIMESTAMP) {
						$info['timer'] = $info['starttimefrom'] - TIMESTAMP;
						$info['timertype'] = 'start';
					} elseif($info['starttimeto'] > TIMESTAMP) {
						$info['timer'] = $info['starttimeto'] - TIMESTAMP;
						$info['timertype'] = 'end';
					} else {
						$info = '';
					}
					$rushinfo[$tid] = $info;
				}
			}

			if(!empty($grouptids)) {
				$groupfids = [];
				foreach(table_forum_thread::t()->fetch_all_by_tid($grouptids) as $row) {
					$groupnames[$row['tid']]['fid'] = $row['fid'];
					$groupnames[$row['tid']]['views'] = $row['views'];
					$groupfids[] = $row['fid'];
				}
				$forumsinfo = table_forum_forum::t()->fetch_all($groupfids);
				foreach($groupnames as $gtid => $value) {
					$gfid = $groupnames[$gtid]['fid'];
					$groupnames[$gtid]['name'] = $forumsinfo[$gfid]['name'];
					$groupnames[$gtid]['type'] = $forumsinfo[$gfid]['type'];
					$groupnames[$gtid]['status'] = $forumsinfo[$gfid]['status'];
				}
			}
		} else {
			foreach($_G['forum_threadlist'] as $threadindex => $thread) {
				$threadids[$threadindex] = $thread['tid'];
				$rIndex[$thread['tid']] = $threadindex;
			}
		}

		$used = cells::getUsed($cellId);
		if(defined('IN_RESTFUL')) {
			$used['image'] = $used['message'] = 1;
		}

		$postIds = [];
		if(!empty($used['message'])) {
			$posts = table_forum_post::t()->fetch_all_by_tid(0, $threadids, true, '', 0, 0, 1, 0);
			foreach($posts as $post) {
				$postIds[] = $post['pid'];
				if(!empty($_G['forum_threadlist'][$rIndex[$post['tid']]]['summary'])) {
					$_G['forum_threadlist'][$rIndex[$post['tid']]]['message'] = $_G['forum_threadlist'][$rIndex[$post['tid']]]['summary'];
					unset($_G['forum_threadlist'][$rIndex[$post['tid']]]['summary']);
				} else {
					$_G['forum_threadlist'][$rIndex[$post['tid']]]['message'] = str_replace(["\n", "\r"], '',
						messagecutstr($post['message'], 80, '...'));
				}
			}
		}

		if(!empty($used['image'])) {
			if(empty($used['message'])) {
				$posts = table_forum_post::t()->fetch_all_by_tid(0, $threadids, true, '', 0, 0, 1, 0);
				foreach($posts as $post) {
					$postIds[] = $post['pid'];
				}
			}
			if($postIds) {
				$width = !empty($_G['forum_threadimage']['width']) ? $_G['forum_threadimage']['width'] : 140;
				$height = !empty($_G['forum_threadimage']['width']) ? $_G['forum_threadimage']['height'] : 140;
				$attachs = table_forum_attachment::t()->fetch_all_by_id('pid', $postIds);
				if($attachs) {
					$tableIds = table_forum_attachment::t()->get_tableids();
					foreach($tableIds as $tableId => $aids) {
						$attachs = table_forum_attachment_n::t()->fetch_all_attachment($tableId, $aids);
						foreach($attachs as $attach) {
							if($attach['isimage'] && !empty($_G['forum_threadlist'][$rIndex[$attach['tid']]])) {
								$_G['forum_threadlist'][$rIndex[$attach['tid']]]['images'][] = getforumimg($attach['aid'], 0, $width, $height, 'fixwr');
							}
						}
					}
				}
			}
		}
	}

}