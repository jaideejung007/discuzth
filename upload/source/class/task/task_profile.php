<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class task_profile {

	var $version = '1.0';
	var $name = 'profile_name';
	var $description = 'profile_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $icon = '';
	var $period = '';
	var $periodtype = 0;
	var $conditions = [];

	function csc($task = []) {
		global $_G;

		$data = $this->checkfield();
		if(!$data[0]) {
			return true;
		}
		return ['csc' => $data[1], 'remaintime' => 0];
	}

	function view() {
		$data = $this->checkfield();
		return lang('task/profile', 'profile_view', ['profiles' => implode(', ', $data[0])]);
	}

	function checkfield() {
		global $_G;

		$fields = ['realname', 'gender', 'birthyear', 'birthmonth', 'birthday', 'bloodtype', 'affectivestatus', 'birthcountry', 'birthprovince', 'birthcity', 'residecountry', 'resideprovince', 'residecity'];
		loadcache('profilesetting');
		$fieldsnew = [];
		foreach($fields as $v) {
			if(isset($_G['cache']['profilesetting'][$v])) {
				$fieldsnew[$v] = $_G['cache']['profilesetting'][$v]['title'];
			}
		}
		if($fieldsnew) {
			space_merge($_G['member'], 'profile');
			$none = [];
			foreach($_G['member'] as $k => $v) {
				if(in_array($k, $fields, true) && !trim($v) && !empty($fieldsnew[$k])) {
					$none[] = $fieldsnew[$k];
				}
			}
			$all = count($fields);
			$csc = intval(($all - count($none)) / $all * 100);
			return [$none, $csc];
		} else {
			return true;
		}
	}

}

