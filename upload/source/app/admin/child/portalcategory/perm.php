<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$catid = intval($_GET['catid']);
if(!submitcheck('permsubmit')) {
	$category = table_portal_category::t()->fetch($catid);
	shownav('portal', 'portalcategory');
	$parents = [['portalcategory', 'portalcategory']];
	if($category['upid']) {
		$parents[] = [$portalcategory[$category['upid']]['catname'], 'portalcategory&operation=perm&catid='.$category['upid']];
	}
	$parents[] = [$category['catname'].' ', ''];
	showchildmenu($parents, cplang('portalcategory_perm_edit'));
	showtips('portalcategory_article_perm_tips');
	showformheader("portalcategory&operation=perm&catid=$catid");

	showtableheader('', 'fixpadding');

	$inherited_checked = !$category['notinheritedarticle'] ? 'checked' : '';
	if($portalcategory[$catid]['level']) showsubtitle(['', '<input class="checkbox" type="checkbox" name="inherited" value="1" '.$inherited_checked.'/>'.cplang('portalcategory_inheritance'), '', '', '']);
	showsubtitle(['', 'username',
		'<input class="checkbox" type="checkbox" name="chkallpublish" onclick="checkAll(\'prefix\', this.form, \'publish\', \'chkallpublish\')" id="chkallpublish" /><label for="chkallpublish">'.cplang('portalcategory_perm_publish').'</label>',
		'<input class="checkbox" type="checkbox" name="chkallmanage" onclick="checkAll(\'prefix\', this.form, \'manage\', \'chkallmanage\')" id="chkallmanage" /><label for="chkallmanage">'.cplang('portalcategory_perm_manage').'</label>',
		'block_perm_inherited',
	]);

	$line = '&minus;';
	$permissions = table_portal_category_permission::t()->fetch_all_by_catid($catid);
	$members = table_common_member::t()->fetch_all(array_keys($permissions));
	foreach($permissions as $uid => $value) {
		$value = array_merge($value, $members[$uid]);
		if(!empty($value['inheritedcatid'])) {
			showtablerow('', ['class="td25"'], [
				'',
				"{$value['username']}",
				$value['allowpublish'] ? '&radic;' : $line,
				$value['allowmanage'] ? '&radic;' : $line,
				'<a href="'.ADMINSCRIPT.'?action=portalcategory&operation=perm&catid='.$value['inheritedcatid'].'">'.$portalcategory[$value['inheritedcatid']]['catname'].'</a>',
			]);
		} else {
			showtablerow('', ['class="td25"'], [
				"<input type=\"checkbox\" class=\"checkbox\" name=\"delete[{$value['uid']}]\" value=\"{$value['uid']}\" /><input type=\"hidden\" name=\"perm[{$value['uid']}]\" value=\"{$value['catid']}\" />
					<input type=\"hidden\" name=\"perm[{$value['uid']}][allowpublish]\" value=\"{$value['allowpublish']}\" />
					<input type=\"hidden\" name=\"perm[{$value['uid']}][allowmanage]\" value=\"{$value['allowmanage']}\" />",
				"{$value['username']}",
				"<input type=\"checkbox\" class=\"checkbox\" name=\"allowpublish[{$value['uid']}]\" value=\"1\" ".($value['allowpublish'] ? 'checked' : '').' />',
				"<input type=\"checkbox\" class=\"checkbox\" name=\"allowmanage[{$value['uid']}]\" value=\"1\" ".($value['allowmanage'] ? 'checked' : '').' />',
				$line,
			]);
		}
	}
	showtablerow('', ['class="td25"'], [
		cplang('add_new'),
		'<input type="text" class="txt" name="newuser" value="" size="20" />',
		'<input type="checkbox" class="checkbox" name="newpublish" value="1" />',
		'<input type="checkbox" class="checkbox" name="newmanage" value="1" />',
		'',
	]);

	showsubmit('permsubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();
} else {

	$users = [];
	if(is_array($_GET['perm'])) {
		foreach($_GET['perm'] as $uid => $value) {
			if(empty($_GET['delete']) || !in_array($uid, $_GET['delete'])) {
				$user = [];
				$user['allowpublish'] = $_GET['allowpublish'][$uid] ? 1 : 0;
				$user['allowmanage'] = $_GET['allowmanage'][$uid] ? 1 : 0;
				if($value['allowpublish'] != $user['allowpublish'] || $value['allowmanage'] != $user['allowmanage']) {
					$user['uid'] = intval($uid);
					$users[] = $user;
				}
			}
		}
	}
	if(!empty($_GET['newuser'])) {
		$newuid = table_common_member::t()->fetch_uid_by_username($_GET['newuser']);
		if($newuid) {
			$user['uid'] = $newuid;
			$user['allowpublish'] = $_GET['newpublish'] ? 1 : 0;
			$user['allowmanage'] = $_GET['newmanage'] ? 1 : 0;
			$users[$user['uid']] = $user;
		} else {
			cpmsg_error($_GET['newuser'].cplang('portalcategory_has_no_allowauthorizedarticle'));
		}
	}

	require_once libfile('class/portalcategory');
	$categorypermsission = &portal_category::instance();
	if(!empty($users)) {
		$categorypermsission->add_users_perm($catid, $users);
	}

	if(!empty($_GET['delete'])) {
		$categorypermsission->delete_users_perm($catid, $_GET['delete']);
	}

	$notinherited = !$_POST['inherited'] ? '1' : '0';
	if($notinherited != $portalcategory[$catid]['notinheritedarticle']) {
		if($notinherited) {
			$categorypermsission->delete_inherited_perm_by_catid($catid, $portalcategory[$catid]['upid']);
		} else {
			$categorypermsission->remake_inherited_perm($catid);
		}
		table_portal_category::t()->update($catid, ['notinheritedarticle' => $notinherited]);
	}

	include_once libfile('function/cache');
	updatecache('portalcategory');

	cpmsg('portalcategory_perm_update_succeed', "action=portalcategory&operation=perm&catid=$catid", 'succeed');
}
	