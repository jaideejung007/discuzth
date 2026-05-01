<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class portal_category {

	function __construct() {
	}

	public static function &instance() {
		static $object;
		if(empty($object)) {
			$object = new portal_category();
		}
		return $object;
	}

	function add_users_perm($catid, $users) {
		$sqlarr = $uids = [];
		$catid = intval($catid);
		if(!empty($catid) && !empty($users)) {
			$catids = $this->get_subcatids_by_catid($catid);
			$this->_add_users_cats($users, $catids);
			foreach($users as $v) {
				$uids[$v['uid']] = $v['uid'];
			}
			$this->_update_member_allowadmincp($uids);
		}
	}

	function _update_member_allowadmincp($uids) {
		if(!empty($uids)) {
			$userperms = [];
			$userperms = table_portal_category_permission::t()->fetch_permission_by_uid($uids);
			foreach(table_common_member::t()->fetch_all($uids, false, 0) as $uid => $v) {
				$v['allowadmincp'] = setstatus(3, empty($userperms[$v['uid']]['allowpublish']) ? 0 : 1, $v['allowadmincp']);
				$v['allowadmincp'] = setstatus(2, empty($userperms[$v['uid']]['allowmanage']) ? 0 : 1, $v['allowadmincp']);
				table_common_member::t()->update($uid, ['allowadmincp' => $v['allowadmincp']]);
			}
		}
	}

	function delete_users_perm($catid, $uids) {

		$uids = !is_array($uids) ? [$uids] : $uids;
		$uids = array_map('intval', $uids);
		$uids = array_filter($uids);
		if($uids) {
			table_portal_category_permission::t()->delete_by_catid_uid_inheritedcatid($catid, $uids, 0);
			$this->delete_inherited_perm_by_catid($catid, $catid, $uids);
			$this->_update_member_allowadmincp($uids);
		}
	}

	function delete_inherited_perm_by_catid($catid, $upid = 0, $uid = 0) {
		if(!is_array($catid)) {
			$catids = $this->get_subcatids_by_catid($catid);
		}
		if($catids) {
			$uids = is_array($uid) ? $uid : [$uid];
			foreach($uids as $uid_) {
				$uid_ = intval($uid_);
				table_portal_category_permission::t()->delete_by_catid_uid_inheritedcatid($catids, $uid_, $upid ? $upid : true);
				if($uid_) {
					$this->_update_member_allowadmincp([$uid_]);
				}
			}
		}
	}

	function remake_inherited_perm($catid) {
		loadcache('portalcategory');
		$portalcategory = getglobal('cache/portalcategory');
		$catid = intval($catid);
		$upid = !empty($portalcategory[$catid]) ? $portalcategory[$catid]['upid'] : 0;
		if($upid) {
			$catids = $this->get_subcatids_by_catid($catid);
			$users = $this->get_perms_by_catid($upid);
			$this->_add_users_cats($users, $catids, $upid);
		}
	}

	function get_perms_by_catid($catid, $uid = 0) {
		$perms = [];
		$catid = intval($catid);
		$uid = intval($uid);
		if($catid) {
			$perms = table_portal_category_permission::t()->fetch_all_by_catid($catid, $uid);
		}
		return $perms;
	}


	function _add_users_cats($users, $catids, $upid = 0) {
		table_portal_category_permission::t()->insert_batch($users, $catids, $upid);
	}

	function delete_perm_by_inheritedcatid($catid, $uids = []) {
		if($uids && !is_array($uids)) $uids = [$uids];
		if($catid) {
			table_portal_category_permission::t()->delete_by_catid_uid_inheritedcatid(false, $uids, $catid);
			if($uids) {
				$this->_update_member_allowadmincp($uids);
			}
		}
	}

	function delete_allperm_by_catid($catid) {
		if($catid) {
			table_portal_category_permission::t()->delete_by_catid_uid_inheritedcatid($catid);
			table_portal_category_permission::t()->delete_by_catid_uid_inheritedcatid(false, false, $catid);
		}
	}

	function get_subcatids_by_catid($catid) {
		loadcache('portalcategory');
		$portalcategory = getglobal('cache/portalcategory');
		$catids = [];
		$catids[$catid] = $catid;
		if(isset($portalcategory[$catid]) && !empty($portalcategory[$catid]['children'])) {
			$children = [];
			foreach($portalcategory[$catid]['children'] as $cid) {
				if(!$portalcategory[$cid]['notinheritedarticle']) {
					$catids[$cid] = $cid;
					if(!empty($portalcategory[$cid]['children'])) {
						$children = array_merge($children, $portalcategory[$cid]['children']);
					}
				}
			}
			if(!empty($children)) {
				foreach($children as $cid) {
					if(!$portalcategory[$cid]['notinheritedarticle']) {
						$catids[$cid] = $cid;
					}
				}
			}
		}
		return $catids;
	}
}

