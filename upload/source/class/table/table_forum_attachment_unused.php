<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_attachment_unused extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'forum_attachment_unused';
		$this->_pk = 'aid';

		parent::__construct();
	}

	public function clear() {
		require_once libfile('function/forum');
		$delaids = [];
		$query = DB::query('SELECT aid, attachment, thumb FROM %t WHERE %i', [$this->_table, DB::field('dateline', TIMESTAMP - 86400, '<')]);
		while($attach = DB::fetch($query)) {
			dunlink($attach);
			$delaids[] = $attach['aid'];
		}
		if($delaids) {
			DB::query('DELETE FROM %t WHERE %i', ['forum_attachment', DB::field('aid', $delaids)], false, true);
			DB::query('DELETE FROM %t WHERE %i', [$this->_table, DB::field('dateline', TIMESTAMP - 86400, '<')], false, true);
		}
	}

}

