<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace admin;
use C;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class optimizer_log {

	private $table = [];

	public function __construct() {
		global $_G;
		$this->table = [
			'common_magiclog' =>
				['tablename' => 'common_magiclog',
					'splitvalue' => $_G['timestamp'] - '365',
					'splitfield' => 'dateline',
					'splitglue' => '<',
				],
			'common_card_log' =>
				['tablename' => 'common_card_log',
					'splitvalue' => $_G['timestamp'] - '86400 * 365',
					'splitfield' => 'dateline',
					'splitglue' => '<',
				],
		];
	}

	public function mergetable($tablename) {
		return C::t($tablename)->merge_table();
	}

	public function check() {
		$count = 0;
		foreach($this->table as $table) {
			$wheresql = $table['splitfield'].$table['splitglue'].$table['splitvalue'];
			if(C::t($table['tablename'])->split_check($wheresql)) {
				$count++;
			}
		}
		if($count) {
			return ['status' => '1', 'type' => 'view', 'lang' => lang('optimizer', 'optimizer_log_clean', ['count' => $count])];
		} else {
			return ['status' => '0', 'type' => 'view', 'lang' => lang('optimizer', 'optimizer_log_not_found')];
		}
	}

	public function optimizer() {
		$adminfile = defined(ADMINSCRIPT) ? ADMINSCRIPT : 'admin.php';
		dheader('Location: '.$_G['siteurl'].$adminfile.'?action=optimizer&operation=log_optimizer&type=optimizer_log');
	}

	public function get_option() {
		$return = [];
		foreach($this->table as $table) {
			$wheresql = $table['splitfield'].$table['splitglue'].$table['splitvalue'];
			if(C::t($table['tablename'])->split_check($wheresql)) {
				$status = C::t($table['tablename'])->tablestatus;
				$return[] = [
					'tablename' => $table['tablename'],
					'name' => $status['Name'],
					'rows' => $status['Rows'],
					'moverows' => $status['Move_rows'],
					'data_length' => $status['Data_length'],
					'index_length' => $status['Index_length'],
					'create_time' => $status['Create_time'],
				];
			}
		}
		return $return;
	}

	public function option_optimizer($tablename) {
		$table = $this->table[$tablename];
		$wheresql = $table['splitfield'].$table['splitglue'].$table['splitvalue'];
		return C::t($tablename)->split_table($wheresql);
	}
}

