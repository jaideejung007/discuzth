<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class mod_index extends remote_service {

	var $config;

	function __construct() {
		parent::__construct();
	}

	function run() {
		$this->success('Discuz! Remote Service API '.$this->version);
	}
}