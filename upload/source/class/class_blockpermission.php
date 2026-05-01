<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_permission {

	function __construct() {
	}

	public static function &instance() {
		static $object;
		if(empty($object)) {
			$object = new block_permission();
		}
		return $object;
	}

	function add_users_perm($bid, $users) {
		if(($uids = table_common_block_permission::t()->insert_by_bid($bid, $users))) {
			$this->_update_member_allowadmincp($uids);
		}

	}

	function _update_member_allowadmincp($uids) {
		if(!empty($uids)) {
			$userperms = table_common_block_permission::t()->fetch_permission_by_uid($uids);
			foreach(table_common_member::t()->fetch_all($uids, false, 0) as $uid => $v) {
				$v['allowadmincp'] = setstatus(4, empty($userperms[$uid]['allowmanage']) ? 0 : 1, $v['allowadmincp']);
				if($userperms[$uid]['allowrecommend'] > 0) {
					if($userperms[$uid]['allowrecommend'] == $userperms[$uid]['needverify']) {
						$v['allowadmincp'] = setstatus(5, 1, $v['allowadmincp']);
						$v['allowadmincp'] = setstatus(6, 0, $v['allowadmincp']);
					} else {
						$v['allowadmincp'] = setstatus(5, 0, $v['allowadmincp']);
						$v['allowadmincp'] = setstatus(6, 1, $v['allowadmincp']);
					}
				} else {
					$v['allowadmincp'] = setstatus(5, 0, $v['allowadmincp']);
					$v['allowadmincp'] = setstatus(6, 0, $v['allowadmincp']);
				}
				table_common_member::t()->update($uid, ['allowadmincp' => $v['allowadmincp']]);
			}
		}
	}

	function delete_users_perm($bid, $users) {
		$bid = intval($bid);
		if($bid && $users) {
			table_common_block_permission::t()->delete_by_bid_uid_inheritedtplname($bid, $users, '');
			table_common_block_favorite::t()->delete_by_uid_bid($users, $bid);
			$this->_update_member_allowadmincp($users);
		}
	}

	function delete_inherited_perm_by_bid($bids, $inheritedtplname = '', $uid = 0) {
		if(!is_array($bids)) $bids = [$bids];
		if($bids) {
			$uid = intval($uid);
			table_common_block_permission::t()->delete_by_bid_uid_inheritedtplname($bids, $uid, empty($inheritedtplname) ? true : $inheritedtplname);
			if($uid) {
				table_common_block_favorite::t()->delete_by_uid_bid($uid, $bids);
				$this->_update_member_allowadmincp([$uid]);
			}
		}
	}

	function remake_inherited_perm($bid) {
		$bid = intval($bid);
		if($bid) {
			if(($targettplname = table_common_template_block::t()->fetch_targettplname_by_bid($bid))) {
				$tplpermsission = &template_permission::instance();
				$userperm = $tplpermsission->get_users_perm_by_template($targettplname);
				$this->add_users_blocks($userperm, $bid, $targettplname);
			}
		}
	}

	function get_perms_by_bid($bid, $uid = 0) {
		$perms = [];
		$bid = intval($bid);
		$uid = intval($uid);
		if($bid) {
			$perms = table_common_block_permission::t()->fetch_all_by_bid($bid, $uid);
		}
		return $perms;
	}


	function add_users_blocks($users, $bids, $tplname = '') {
		if(($uids = table_common_block_permission::t()->insert_batch($users, $bids, $tplname))) {
			$this->_update_member_allowadmincp($uids);
		}
	}

	function delete_perm_by_inheritedtpl($tplname, $uids) {
		if(!empty($uids) && !is_array($uids)) $uids = [$uids];
		if($tplname) {
			table_common_block_permission::t()->delete_by_bid_uid_inheritedtplname(FALSE, $uids, $tplname);
			if($uids) {
				$this->_update_member_allowadmincp($uids);
			}
		}
	}

	function delete_perm_by_template($templates) {
		if($templates) {
			table_common_block_permission::t()->delete_by_bid_uid_inheritedtplname(FALSE, FALSE, $templates);
		}
	}

	function get_bids_by_template($tplname) {
		return $tplname ? table_common_template_block::t()->fetch_all_bid_by_targettplname_notinherited($tplname, 0) : [];
	}
}

class template_permission {
	function __construct() {
	}

	public static function &instance() {
		static $object;
		if(empty($object)) {
			$object = new template_permission();
		}
		return $object;
	}

	function add_users($tplname, $users) {
		$templates = $this->_get_templates_subs($tplname);
		$this->_add_users_templates($users, $templates);

		$blockpermission = &block_permission::instance();
		$bids = $blockpermission->get_bids_by_template($templates);
		$blockpermission->add_users_blocks($users, $bids, $tplname);
	}

	function delete_users($tplname, $uids) {
		$uids = !is_array($uids) ? [$uids] : $uids;
		$uids = array_map('intval', $uids);
		$uids = array_filter($uids);
		if($uids) {
			table_common_template_permission::t()->delete_by_targettplname_uid_inheritedtplname($tplname, $uids, '');
		}
		$this->delete_perm_by_inheritedtpl($tplname, $uids);
	}

	function add_blocks($tplname, $bids) {
		$users = $this->get_users_perm_by_template($tplname);
		if($users) {
			$blockpermission = &block_permission::instance();
			$blockpermission->add_users_blocks($users, $bids, $tplname);
		}
	}

	function get_users_perm_by_template($tplname) {
		$perm = [];
		if($tplname) {
			$perm = table_common_template_permission::t()->fetch_all_by_targettplname($tplname);
		}
		return $perm;
	}

	function _add_users_templates($users, $templates, $uptplname = '') {
		table_common_template_permission::t()->insert_batch($users, $templates, $uptplname);
	}

	function delete_allperm_by_tplname($tplname) {
		if($tplname) {
			$tplname = is_array($tplname) ? $tplname : [$tplname];
			$blockpermission = &block_permission::instance();
			$blockpermission->delete_perm_by_template($tplname);
			$tplnames = dimplode($tplname);
			table_common_template_permission::t()->delete_by_targettplname_uid_inheritedtplname($tplnames);
			table_common_template_permission::t()->delete_by_targettplname_uid_inheritedtplname(false, false, $tplnames);
		}
	}

	function delete_inherited_perm_by_tplname($templates, $inheritedtplname = '', $uid = 0) {
		if($templates && !is_array($templates)) {
			$templates = $this->_get_templates_subs($templates);
		}
		if($templates) {
			$uid = intval($uid);
			table_common_template_permission::t()->delete_by_targettplname_uid_inheritedtplname($templates, $uid, $inheritedtplname ? $inheritedtplname : true);

			$blockpermission = &block_permission::instance();
			$blocks = $blockpermission->get_bids_by_template($templates);
			$blockpermission->delete_inherited_perm_by_bid($blocks, $inheritedtplname, $uid);
		}
	}

	function delete_perm_by_inheritedtpl($tplname, $uids = []) {
		if($uids && !is_array($uids)) $uids = [$uids];
		if($tplname) {
			table_common_template_permission::t()->delete_by_targettplname_uid_inheritedtplname(false, $uids, $tplname);
			$blockpermission = &block_permission::instance();
			$blockpermission->delete_perm_by_inheritedtpl($tplname, $uids);
		}
	}

	function remake_inherited_perm($tplname, $parenttplname) {
		if($tplname && $parenttplname) {
			$users = $this->get_users_perm_by_template($parenttplname);
			$templates = $this->_get_templates_subs($tplname);
			$this->_add_users_templates($users, $templates, $parenttplname);

			$blockpermission = &block_permission::instance();
			$bids = $blockpermission->get_bids_by_template($templates);
			$blockpermission->add_users_blocks($users, $bids, $parenttplname);
		}
	}

	function _get_templates_subs($tplname) {
		global $_G;
		$tplpre = 'portal/list_';
		$cattpls = [$tplname];
		if(substr($tplname, 0, 12) == $tplpre) {
			loadcache('portalcategory');
			$portalcategory = $_G['cache']['portalcategory'];
			$catid = intval(str_replace($tplpre, '', $tplname));
			if(isset($portalcategory[$catid]) && !empty($portalcategory[$catid]['children'])) {
				$children = [];
				foreach($portalcategory[$catid]['children'] as $cid) {
					if(!$portalcategory[$cid]['notinheritedblock']) {
						$cattpls[] = $tplpre.$cid;
						if(!empty($portalcategory[$cid]['children'])) {
							$children = array_merge($children, $portalcategory[$cid]['children']);
						}
					}
				}
				if(!empty($children)) {
					foreach($children as $cid) {
						if(!$portalcategory[$cid]['notinheritedblock']) {
							$cattpls[] = $tplpre.$cid;
						}
					}
				}
			}
		}
		return $cattpls;
	}

	function _get_templates_ups($tplname) {
		global $_G;
		$tplpre = 'portal/list_';
		$cattpls = [$tplname];
		if(substr($tplname, 0, 12) == $tplpre) {
			loadcache('portalcategory');
			$portalcategory = $_G['cache']['portalcategory'];
			$catid = intval(str_replace($tplpre, '', $tplname));
			if(isset($portalcategory[$catid]) && !$portalcategory[$catid]['notinheritedblock']) {
				$upid = $portalcategory[$catid]['upid'];
				while(!empty($upid)) {
					$cattpls[] = $tplpre.$upid;
					$upid = !$portalcategory[$upid]['notinheritedblock'] ? $portalcategory[$upid]['upid'] : 0;
				}
			}
		}
		return $cattpls;
	}

}

