<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_forums() {
	$data = [];
	$forums = table_forum_forum::t()->fetch_all_forum();
	$pluginvalue = $forumlist = $stylevalue = [];
	$pluginvalue = pluginsettingvalue('forums');
	$stylevalue = stylesettingvalue('forums');

	$forumnoperms = [];
	foreach($forums as $val) {
		$forum = ['fid' => $val['fid'], 'type' => $val['type'], 'name' => $val['name'], 'fup' => $val['fup'], 'simple' => $val['simple'], 'status' => $val['status'], 'allowpostspecial' => $val['allowpostspecial'], 'viewperm' => $val['viewperm'], 'formulaperm' => $val['formulaperm'], 'havepassword' => $val['password'], 'postperm' => $val['postperm'], 'replyperm' => $val['replyperm'], 'getattachperm' => $val['getattachperm'], 'postattachperm' => $val['postattachperm'], 'extra' => $val['extra'], 'commentitem' => $val['commentitem'], 'uid' => $val['uid'], 'archive' => $val['archive'], 'domain' => $val['domain']];
		$forum['orderby'] = bindec((($forum['simple'] & 256) ? 1 : 0).(($forum['simple'] & 128) ? 1 : 0).(($forum['simple'] & 64) ? 1 : 0));
		$forum['ascdesc'] = ($forum['simple'] & 32) ? 'ASC' : 'DESC';
		$forum['extra'] = dunserialize($forum['extra']);
		if(!is_array($forum['extra'])) {
			$forum['extra'] = [];
		}

		if(!isset($forumlist[$forum['fid']])) {
			if($forum['uid']) {
				$forum['users'] = "\t{$forum['uid']}\t";
			}
			unset($forum['uid']);
			if($forum['fup']) {
				$forumlist[$forum['fup']]['count']++;
			}
			$forumlist[$forum['fid']] = $forum;
		} elseif($forum['uid']) {
			if(!$forumlist[$forum['fid']]['users']) {
				$forumlist[$forum['fid']]['users'] = "\t";
			}
			$forumlist[$forum['fid']]['users'] .= "{$forum['uid']}\t";
		}
	}

	$data = [];
	if(!empty($forumlist)) {
		foreach($forumlist as $fid1 => $forum1) {
			if(($forum1['type'] == 'group' && $forum1['count'])) {
				$data[$fid1] = formatforumdata($forum1, $pluginvalue, $stylevalue);
				unset($data[$fid1]['users'], $data[$fid1]['allowpostspecial'], $data[$fid1]['commentitem']);
				foreach($forumlist as $fid2 => $forum2) {
					if($forum2['fup'] == $fid1 && $forum2['type'] == 'forum') {
						$data[$fid2] = formatforumdata($forum2, $pluginvalue, $stylevalue);
						foreach($forumlist as $fid3 => $forum3) {
							if($forum3['fup'] == $fid2 && $forum3['type'] == 'sub') {
								$data[$fid3] = formatforumdata($forum3, $pluginvalue, $stylevalue);
							}
						}
					}
				}
			}
		}
	}
	savecache('forums', $data);
}

function formatforumdata($forum, &$pluginvalue, &$stylevalue) {
	static $keys = ['fid', 'type', 'name', 'fup', 'viewperm', 'postperm', 'orderby', 'ascdesc', 'users', 'status',
		'extra', 'plugin', 'style', 'allowpostspecial', 'commentitem', 'archive', 'domain', 'havepassword'];
	static $orders = ['lastpost', 'dateline', 'replies', 'views', 'recommends', 'heats'];

	$data = [];
	foreach($keys as $key) {
		$data[$key] = match ($key) {
			'orderby' => $orders[$forum['orderby']],
			'plugin' => $pluginvalue[$forum['fid']],
			'style' => $stylevalue[$forum['fid']],
			'havepassword' => $forum[$key] ? 1 : 0,
			'allowpostspecial' => sprintf('%06b', $forum['allowpostspecial']),
			default => $forum[$key],
		};
	}
	return $data;
}

