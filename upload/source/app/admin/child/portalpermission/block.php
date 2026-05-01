<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showsubtitle(['username', 'block_name', 'block_perm_manage', 'block_perm_recommend', 'block_perm_needverify', 'block_perm_inherited']);
showtagheader('tbody', '', true);
loadcache('diytemplatename');
$uids = $_GET['uid'] ? [$_GET['uid']] : $uids;
if(($count = table_common_block_permission::t()->count_by_uids($uids, !$inherited))) {
	$blocks = $bids = [];
	$permissions = table_common_block_permission::t()->fetch_all_by_uid($uids, !$inherited, $_GET['ordersc'], $start, $perpage);
	foreach($permissions as $value) {
		$uids[$value['uid']] = $value['uid'];
		$bids[$value['bid']] = $value['bid'];
	}
	if($bids) $blocks = table_common_block::t()->fetch_all($bids);
	if(empty($members)) $members = table_common_member::t()->fetch_all($uids);
	$multipage = multi($count, $perpage, $page, $mpurl.'&perpage='.$perpage);
	foreach($permissions as $value) {
		$blockname = $blocks[$value['bid']]['name'] ? $blocks[$value['bid']]['name'] : $value['bid'];
		showtablerow('', '', [
			$members[$value['uid']]['username'],
			'<a href="'.ADMINSCRIPT.'?action=block&operation=perm&bid='.$value['bid'].'">'.$blockname.'</a>',
			$value['allowmanage'] ? $right : $line,
			$value['allowrecommend'] ? $right : $line,
			$value['needverify'] ? $right : $line,
			$value['inheritedtplname'] ? '<a href="'.ADMINSCRIPT.'?action=diytemplate&operation=perm&targettplname='.$value['inheritedtplname'].'">'.$_G['cache']['diytemplatename'][$value['inheritedtplname']].'</a>' : $line,
		]);
	}
	echo '<tr><td colspan="6">'.$multipage.'</td></tr>';
}
showtagfooter('tbody');
	