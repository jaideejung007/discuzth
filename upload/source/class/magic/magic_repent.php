<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_repent {

	var $version = '1.0';
	var $name = 'repent_name';
	var $description = 'repent_desc';
	var $price = '10';
	var $weight = '10';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $magic = [];
	var $parameters = [];

	function getsetting(&$magic) {
		global $_G;
		$settings = [
			'fids' => [
				'title' => 'repent_forum',
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
		if(empty($_GET['pid'])) {
			showmessage(lang('magic/repent', 'repent_info_nonexistence'));
		}
		$_G['tid'] = $_GET['ptid'];

		$post = getpostinfo($_GET['pid'], 'pid', ['p.first', 'p.tid', 'p.fid', 'p.authorid', 'p.replycredit', 't.status as thread_status']);
		$this->_check($post);

		require_once libfile('function/post');
		require_once libfile('function/delete');
		if($post['first']) {
			if($have_replycredit = table_forum_replycredit::t()->fetch($post['tid'])) {
				$thread = table_forum_thread::t()->fetch_thread($post['tid']);
				if($thread['replycredit']) {
					updatemembercount($post['authorid'], [$_G['setting']['creditstransextra'][10] => -$thread['replycredit']]);
				}
				table_forum_replycredit::t()->delete($post['tid']);
				table_common_credit_log::t()->delete_by_operation_relatedid(['RCT', 'RCA', 'RCB'], $post['tid']);
			}

			deletethread([$post['tid']]);
			updateforumcount($post['fid']);
		} else {
			if($post['replycredit'] > 0) {
				updatemembercount($post['authorid'], [$_G['setting']['creditstransextra'][10] => -$post['replycredit']]);
				table_common_credit_log::t()->delete_by_uid_operation_relatedid($post['authorid'], 'RCA', $post['tid']);
			}
			deletepost([$_GET['pid']]);
			updatethreadcount($post['tid']);
		}

		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', 0, 'tid', $_G['tid']);

		showmessage(lang('magic/repent', 'repent_succeed'), $post['first'] ? 'forum.php?mod=forumdisplay&fid='.$post['fid'] : dreferer(), [], ['alert' => 'right', 'showdialog' => 1, 'locationtime' => true]);
	}

	function show() {
		global $_G;
		$pid = !empty($_GET['id']) ? dhtmlspecialchars($_GET['id']) : '';
		list($pid, $_G['tid']) = explode(':', $pid);
		if($_G['tid']) {
			$post = getpostinfo($_GET['id'], 'pid', ['p.fid', 'p.authorid', 't.status as thread_status']);
			$this->_check($post);
		}
		magicshowtype('top');
		magicshowsetting(lang('magic/repent', 'repent_info'), 'pid', $pid, 'text');
		magicshowsetting('', 'ptid', $_G['tid'], 'hidden');
		magicshowtype('bottom');
	}

	function buy() {
		global $_G;
		if(!empty($_GET['id'])) {
			list($_GET['id'], $_G['tid']) = explode(':', $_GET['id']);
			$post = getpostinfo($_GET['id'], 'pid', ['p.fid', 'p.authorid']);
			$this->_check($post);
		}
	}

	function _check($post) {
		global $_G;
		if(!checkmagicperm($this->parameters['forum'], $post['fid'])) {
			showmessage(lang('magic/repent', 'repent_info_noperm'));
		}
		if($post['authorid'] != $_G['uid']) {
			showmessage(lang('magic/repent', 'repent_info_user_noperm'));
		}
		if(getstatus($post['status'], 3)) {
			showmessage(lang('magic/repent', 'repent_do_not_rushreply'));
		}
	}

}

