<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_bump {

	var $version = '1.0';
	var $name = 'bump_name';
	var $description = 'bump_desc';
	var $price = '10';
	var $weight = '10';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $magic = [];
	var $parameters = [];

	function getsetting(&$magic) {
		global $_G;
		$settings = [
			'fids' => [
				'title' => 'bump_forum',
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
		if(empty($_GET['tid'])) {
			showmessage(lang('magic/bump', 'bump_info_nonexistence'));
		}

		$thread = getpostinfo($_GET['tid'], 'tid', ['fid', 'authorid', 'subject']);
		$this->_check($thread['fid']);

		table_forum_thread::t()->update($_GET['tid'], ['moderated' => 1, 'lastpost' => TIMESTAMP]);
		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', 0, 'tid', $_GET['tid']);
		updatemagicthreadlog($_GET['tid'], $this->magic['magicid'], 'BMP');

		if($thread['authorid'] != $_G['uid']) {
			notification_add($thread['authorid'], 'magic', lang('magic/bump', 'bump_notification'), ['tid' => $_GET['tid'], 'subject' => $thread['subject'], 'magicname' => $this->magic['name']]);
		}

		showmessage(lang('magic/bump', 'bump_succeed'), dreferer(), [], ['alert' => 'right', 'showdialog' => 1, 'locationtime' => true]);
	}

	function show() {
		global $_G;
		$tid = !empty($_GET['id']) ? dhtmlspecialchars($_GET['id']) : '';
		if($tid) {
			$thread = getpostinfo($_GET['id'], 'tid', ['fid']);
			$this->_check($thread['fid']);
		}
		magicshowtype('top');
		magicshowsetting(lang('magic/bump', 'bump_info'), 'tid', $tid, 'text');
		magicshowtype('bottom');
	}

	function buy() {
		global $_G;
		if(!empty($_GET['id'])) {
			$thread = getpostinfo($_GET['id'], 'tid', ['fid']);
			$this->_check($thread['fid']);
		}
	}

	function _check($fid) {
		if(!checkmagicperm($this->parameters['forum'], $fid)) {
			showmessage(lang('magic/bump', 'bump_info_noperm'));
		}
	}

}

