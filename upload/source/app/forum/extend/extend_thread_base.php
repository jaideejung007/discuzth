<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace forum;
use discuz_extend;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class extend_thread_base extends discuz_extend {

	public $forum;
	public $thread;
	public $post;
	public $feed;

	public function init_base_var() {
		$this->forum = &$this->_obj->forum;
		$this->thread = &$this->_obj->thread;
		$this->post = &$this->_obj->post;
		$this->feed = &$this->_obj->feed;
		parent::init_base_var();
	}

}

