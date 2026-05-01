<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showsubtitle(['username', 'portalcategory', 'portalcategory_perm_publish', 'portalcategory_perm_manage', 'block_perm_inherited']);
showtagheader('tbody', '', true);
loadcache('portalcategory');
$wherearr = [];
if(($where = $_GET['uid'] ? 'uid='.$_GET['uid'] : ($uids ? 'uid IN('.dimplode($uids).')' : ''))) {
	$wherearr[] = $where;
}
if($inherited) {
	$wherearr[] = 'inheritedcatid = \'\'';
}
$wheresql = $wherearr ? ' WHERE '.implode(' AND ', $wherearr) : '';
$uids = $_GET['uid'] ? [$_GET['uid']] : $uids;
$count = table_portal_category_permission::t()->count_by_uids($uids, !$inherited);
if($count) {
	$permissions = table_portal_category_permission::t()->fetch_all_by_uid($uids, !$inherited, $_GET['ordersc'], $start, $perpage);
	foreach($permissions as $value) {
		$uids[$value['uid']] = $value['uid'];
	}
	if(empty($members)) $members = table_common_member::t()->fetch_all($uids);
	$multipage = multi($count, $perpage, $page, $mpurl.'&perpage='.$perpage);
	foreach($permissions as $value) {
		showtablerow('', '', [
			$members[$value['uid']]['username'],
			'<a href="'.ADMINSCRIPT.'?action=portalcategory&operation=perm&catid='.$value['catid'].'">'.$_G['cache']['portalcategory'][$value['catid']]['catname'].'</a>',
			$value['allowpublish'] ? $right : $line,
			$value['allowmanage'] ? $right : $line,
			$value['inheritedcatid'] ? '<a href="'.ADMINSCRIPT.'?action=portalcategory&operation=perm&catid='.$value['inheritedcatid'].'">'.$_G['cache']['portalcategory'][$value['inheritedcatid']]['catname'].'</a>' : $line,
		]);
	}
	echo '<tr><td colspan="6">'.$multipage.'</td></tr>';
}
showtagfooter('tbody');
	