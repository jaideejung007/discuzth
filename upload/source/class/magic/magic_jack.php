<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_jack {

	var $version = '1.0';
	var $name = 'jack_name';
	var $description = 'jack_desc';
	var $price = '10';
	var $weight = '10';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $magic = [];
	var $parameters = [];

	function getsetting(&$magic) {
		global $_G;
		$settings = [
			'expiration' => [
				'title' => 'jack_expiration',
				'type' => 'text',
				'value' => '',
				'default' => 1,
			],
			'fids' => [
				'title' => 'jack_forum',
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
			showmessage(lang('magic/jack', 'jack_info_nonexistence'));
		}

		$thread = getpostinfo($_GET['tid'], 'tid', ['fid', 'authorid', 'subject', 'lastpost']);
		$this->_check($thread['fid']);
		magicthreadmod($_GET['tid']);

		$this->parameters['expiration'] = $this->parameters['expiration'] ? intval($this->parameters['expiration']) : 1;
		$magicnum = abs(intval($_GET['magicnum']));
		if(empty($magicnum) || $magicnum > $this->magic['num']) {
			showmessage(lang('magic/jack', 'jack_num_not_enough'));
		}
		$expiration = ($thread['lastpost'] > TIMESTAMP ? $thread['lastpost'] : TIMESTAMP) + $this->parameters['expiration'] * $magicnum * 3600;
		table_forum_thread::t()->update($_GET['tid'], ['lastpost' => $expiration]);
		usemagic($this->magic['magicid'], $this->magic['num'], $magicnum);
		updatemagiclog($this->magic['magicid'], '2', $magicnum, '0', 0, 'tid', $_GET['tid']);

		if($thread['authorid'] != $_G['uid']) {
			notification_add($thread['authorid'], 'magic', lang('magic/jack', 'jack_notification'), ['tid' => $_GET['tid'], 'subject' => $thread['subject'], 'magicname' => $this->magic['name']]);
		}
		showmessage(lang('magic/jack', 'jack_succeed'), dreferer(), [], ['alert' => 'right', 'showdialog' => 1, 'locationtime' => true]);
	}

	function show() {
		global $_G;
		$tid = !empty($_GET['id']) ? dhtmlspecialchars($_GET['id']) : '';
		if($tid) {
			$thread = getpostinfo($_GET['id'], 'tid', ['fid']);
			$this->_check($thread['fid']);
		}
		$this->parameters['expiration'] = $this->parameters['expiration'] ? intval($this->parameters['expiration']) : 1;
		magicshowtype('top');
		magicshowtips(lang('magic/jack', 'jack_info', ['expiration' => $this->parameters['expiration'], 'magicnum' => $this->magic['num']]));
		magicshowsetting(lang('magic/jack', 'jack_num'), 'magicnum', '1', 'text');
		magicshowsetting('', 'tid', $tid, 'hidden');
		magicshowtype('bottom');
	}

	function buy() {
		global $_G;
		if(!empty($_GET['id'])) {
			$thread = getpostinfo($_GET['id'], 'tid', ['fid']);
			$this->_check($thread['fid']);
		}
		$this->parameters['expiration'] = $this->parameters['expiration'] ? intval($this->parameters['expiration']) : 1;
		magicshowtips(lang('magic/jack', 'jack_info', ['expiration' => $this->parameters['expiration']]));
	}

	function _check($fid) {
		if(!checkmagicperm($this->parameters['forum'], $fid)) {
			showmessage(lang('magic/jack', 'jack_info_noperm'));
		}
	}

}

