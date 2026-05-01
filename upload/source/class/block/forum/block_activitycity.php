<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_activity', 'class/block/forum');

class block_activitycity extends block_activity {
	function __construct() {
		$this->setting = [
			'fids' => [
				'title' => 'activitylist_fids',
				'type' => 'mselect',
				'value' => []
			],
			'viewmod' => [
				'title' => 'threadlist_viewmod',
				'type' => 'radio'
			],
			'place' => [
				'title' => 'activitylist_place',
				'type' => 'text'
			],
			'class' => [
				'title' => 'activitylist_class',
				'type' => 'select',
				'value' => []
			],
			'orderby' => [
				'title' => 'activitylist_orderby',
				'type' => 'mradio',
				'value' => [
					['dateline', 'activitylist_orderby_dateline'],
					['weekstart', 'activitylist_orderby_weekstart'],
					['monthstart', 'activitylist_orderby_monthstart'],
					['weekexp', 'activitylist_orderby_weekexp'],
					['monthexp', 'activitylist_orderby_monthexp'],
				],
				'default' => 'dateline'
			],
			'titlelength' => [
				'title' => 'activitylist_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'activitylist_summarylength',
				'type' => 'text',
				'default' => 80
			],
			'startrow' => [
				'title' => 'activitylist_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_activity_script_activitycity');
	}
}


