<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_stick {

	var $version = '1.0';
	var $name = 'stick_name';
	var $description = 'stick_desc';
	var $price = '10';
	var $weight = '10';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $magic = [];
	var $parameters = [];

	function getsetting(&$magic) {
		global $_G;
		$settings = [
			'expiration' => [
				'title' => 'stick_expiration',
				'type' => 'text',
				'value' => '',
				'default' => 24,
			],
			'fids' => [
				'title' => 'stick_forum',
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
		$magicnew['expiration'] = intval($parameters['expiration']);
	}

	function usesubmit() {
		global $_G;
		if(empty($_GET['tid'])) {
			showmessage(lang('magic/stick', 'stick_info_nonexistence'));
		}

		$thread = getpostinfo($_GET['tid'], 'tid', ['fid', 'authorid', 'subject']);
		$this->_check($thread['fid']);
		magicthreadmod($_GET['tid']);

		table_forum_thread::t()->update($_GET['tid'], ['displayorder' => 1, 'moderated' => 1]);
		$this->parameters['expiration'] = $this->parameters['expiration'] ? intval($this->parameters['expiration']) : 24;
		$expiration = TIMESTAMP + $this->parameters['expiration'] * 3600;

		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', 0, 'tid', $_GET['tid']);
		updatemagicthreadlog($_GET['tid'], $this->magic['magicid'], $expiration > 0 ? 'EST' : 'STK', $expiration);

		if($thread['authorid'] != $_G['uid']) {
			notification_add($thread['authorid'], 'magic', lang('magic/stick', 'stick_notification'), ['tid' => $_GET['tid'], 'subject' => $thread['subject'], 'magicname' => $this->magic['name']]);
		}

		showmessage(lang('magic/stick', 'stick_succeed'), dreferer(), [], ['alert' => 'right', 'showdialog' => 1, 'locationtime' => true]);
	}

	function show() {
		global $_G;
		$tid = !empty($_GET['id']) ? dhtmlspecialchars($_GET['id']) : '';
		if($tid) {
			$thread = getpostinfo($_GET['id'], 'tid', ['fid']);
			$this->_check($thread['fid']);
		}
		$this->parameters['expiration'] = $this->parameters['expiration'] ? intval($this->parameters['expiration']) : 24;
		magicshowtype('top');
		magicshowsetting(lang('magic/stick', 'stick_info', ['expiration' => $this->parameters['expiration']]), 'tid', $tid, 'text');
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
			showmessage(lang('magic/stick', 'stick_info_noperm'));
		}
	}

}

