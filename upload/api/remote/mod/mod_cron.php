<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class mod_cron extends remote_service {
	function run() {

		if(!$this->config['cron']) {
			$this->error(100, 'cron service is off. Please check "config.global.php" on your webserver folder.');
		}

		$discuz = C::app();
		$discuz->initated = false;
		$discuz->init_db = false;
		$discuz->init_setting = true;
		$discuz->init_user = false;
		$discuz->init_session = false;
		$discuz->init_misc = false;
		$discuz->init_mobile = false;
		$discuz->init_cron = true;
		$discuz->init();

		$this->success('Cron work is done');
	}

}