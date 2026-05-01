<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class adv_search {

	var $version = '1.0';
	var $name = 'search_name';
	var $description = 'search_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $targets = ['search'];
	var $imagesizes = ['120x60', '120x240'];

	function getsetting() {
	}

	function setsetting(&$advnew, &$parameters) {
		global $_G;
		if(is_array($advnew['targets'])) {
			$advnew['targets'] = implode("\t", $advnew['targets']);
		}
	}

	function evalcode() {
		return [
			'check' => '',
			'create' => '$adcode = $codes[$adids[array_rand($adids)]];',
		];
	}

}

