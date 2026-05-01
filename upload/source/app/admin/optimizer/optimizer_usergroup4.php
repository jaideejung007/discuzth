<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace admin;
use table_common_usergroup;
use table_common_usergroup_field;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class optimizer_usergroup4 {

	public function __construct() {

	}

	public function check() {
		$usergroup = table_common_usergroup::t()->fetch(4);
		$usergroupfield = table_common_usergroup_field::t()->fetch(4);
		if(!$usergroup['allowsendpm'] && !$usergroupfiled['allowposturl'] && !$usergroupfield['allowgroupposturl'] && !$usergroupfield['allowpost'] && !$usergroupfield['allowreply'] && !$usergroupfiled['allowdirectpost'] && !$usergroupfield['allowgroupdirectpost']) {
			$return = ['status' => 0, 'type' => 'none', 'lang' => lang('optimizer', 'optimizer_usergroup4_no_need')];
		} else {
			$option = [
				'allowsendpm' => lang('optimizer', 'optimizer_usergroup_need_allowsendpm'),
				'allowposturl' => lang('optimizer', 'optimizer_usergroup_need_allowposturl'),
				'allowgroupposturl' => lang('optimizer', 'optimizer_usergroup_need_allowgroupposturl'),
				'allowpost' => lang('optimizer', 'optimizer_usergroup_need_allowpost'),
				'allowreply' => lang('optimizer', 'optimizer_usergroup_need_allowreply'),
				'allowdirectpost' => lang('optimizer', 'optimizer_usergroup_need_allowdirectpost'),
				'allowgroupdirectpost' => lang('optimizer', 'optimizer_usergroup_need_allowgroupdirectpost'),
			];
			$usergroup = array_merge((array)$usergroup, (array)$usergroupfield);
			$desc = [];
			foreach($option as $key => $value) {
				if($usergroup[$key]) {
					$desc[] = $value;
				}
			}
			$return = ['status' => 1, 'type' => 'header', 'lang' => lang('optimizer', 'optimizer_usergroup4_need', ['desc' => implode(',', $desc)])];
		}
		return $return;
	}

	public function optimizer() {
		$adminfile = defined(ADMINSCRIPT) ? ADMINSCRIPT : 'admin.php';
		dheader('Location: '.$_G['siteurl'].$adminfile.'?action=usergroups&operation=edit&id=4');
	}
}

