<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_namepost {

	var $version = '1.0';
	var $name = 'namepost_name';
	var $description = 'namepost_desc';
	var $price = '10';
	var $weight = '10';
	var $targetgroupperm = true;
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $magic = [];
	var $parameters = [];

	function getsetting(&$magic) {
		global $_G;
		$settings = [
			'fids' => [
				'title' => 'namepost_forum',
				'type' => 'mselect',
				'value' => [],
			],
		];
		loadcache('forums');
		$settings['fids']['value'][] = [0, '&nbsp;'];
		if(empty($_G['cache']['forums'])) $_G['cache']['forums'] = [];
		foreach($_G['cache']['forums'] as $fid => $forum) {
			$settings['fids']['value'][] = [$fid, ($forum['type'] == 'forum' ? str_repeat('&nbsp;', 4) : ($forum['type'] == 'sub' ? str_repeat('&nbsp;', 8) : '')).$forum['name']];
		}
		$magic['fids'] = explode("\t", $magic['forum']);

		return $settings;
	}

	function setsetting(&$magicnew, &$parameters) {
		global $_G;
		$magicnew['forum'] = is_array($parameters['fids']) && !empty($parameters['fids']) ? implode("\t", $parameters['fids']) : '';
	}

	function usesubmit() {
		global $_G;
		$id = intval($_GET['id']);
		if(empty($id)) {
			showmessage(lang('magic/namepost', 'namepost_info_nonexistence'));
		}
		$idtype = !empty($_GET['idtype']) ? dhtmlspecialchars($_GET['idtype']) : '';
		if(!in_array($idtype, ['pid', 'cid'])) {
			showmessage(lang('magic/namepost', 'namepost_use_error'));
		}
		if($idtype == 'pid') {
			$_G['tid'] = intval($_GET['ptid']);
			$post = getpostinfo($id, 'pid', ['p.first', 'p.tid', 'p.fid', 'p.authorid', 'p.dateline', 'p.anonymous']);
			$this->_check($post);
			$authorid = $post['authorid'];
			$author = $post['anonymous'] ? '' : 1;
		} elseif($idtype == 'cid') {
			$comment = table_home_comment::t()->fetch_comment($id);
			$authorid = $comment['authorid'];
			$author = $comment['author'];
		}
		if($author) {
			showmessage('magicuse_bad_object');
		}
		$member = getuserbyuid($authorid);
		if(!checkmagicperm($this->parameters['targetgroups'], $member['groupid'])) {
			showmessage(lang('magic/namepost', 'namepost_info_user_noperm'));
		}
		$author = daddslashes($member['username']);

		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', 0, $idtype, $id);
		showmessage(lang('magic/namepost', 'magic_namepost_succeed'), 'javascript:;', ['uid' => $authorid, 'username' => $author, 'avatar' => 1], ['alert' => 'right']);
	}

	function show() {
		global $_G;
		$id = !empty($_GET['id']) ? dhtmlspecialchars($_GET['id']) : '';
		$idtype = !empty($_GET['idtype']) ? dhtmlspecialchars($_GET['idtype']) : '';
		if($idtype == 'pid') {
			list($id, $_G['tid']) = explode(':', $id);
			if($id && $_G['tid']) {
				$post = getpostinfo($id, 'pid', ['p.fid', 'p.authorid']);
				$this->_check($post);
			}
		}

		magicshowtype('top');
		magicshowtips(lang('magic/namepost', 'namepost_desc'));
		magicshowtips(lang('magic/namepost', 'namepost_num', ['magicnum' => $this->magic['num']]));
		magicshowsetting('', 'id', $id, 'hidden');
		magicshowsetting('', 'idtype', $idtype, 'hidden');
		if($idtype == 'pid') {
			magicshowsetting('', 'ptid', $_G['tid'], 'hidden');
		}
		magicshowtype('bottom');
	}

	function buy() {
		global $_G;
		$id = !empty($_GET['id']) ? dhtmlspecialchars($_GET['id']) : '';
		$idtype = !empty($_GET['idtype']) ? dhtmlspecialchars($_GET['idtype']) : '';
		if(!empty($id) && $_GET['idtype'] == 'pid') {
			list($id, $_G['tid']) = explode(':', $id);
			$post = getpostinfo(intval($id), 'pid', ['p.fid', 'p.authorid']);
			$this->_check($post);
		}
	}

	function _check($post) {
		global $_G;
		if(!checkmagicperm($this->parameters['forum'], $post['fid'])) {
			showmessage(lang('magic/namepost', 'namepost_info_noperm'));
		}
		$member = getuserbyuid($post['authorid']);
		if(!checkmagicperm($this->parameters['targetgroups'], $member['groupid'])) {
			showmessage(lang('magic/namepost', 'namepost_info_user_noperm'));
		}
	}

}

