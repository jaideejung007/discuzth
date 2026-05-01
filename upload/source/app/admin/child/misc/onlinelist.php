<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('onlinesubmit')) {

	shownav('style', 'misc_onlinelist');
	showsubmenu('nav_misc_onlinelist');
	showtips('misc_onlinelist_tips');
	showformheader('misc&operation=onlinelist&');
	showtableheader('', 'fixpadding');
	showsubtitle(['', 'display_order', 'usergroup', 'usergroups_title', 'misc_onlinelist_image']);

	$listarray = [];
	foreach(table_forum_onlinelist::t()->range() as $list) {
		$list['title'] = dhtmlspecialchars($list['title']);
		$listarray[$list['groupid']] = $list;
	}

	$onlinelist = '';
	$query = array_merge([0 => ['groupid' => 0, 'grouptitle' => 'Member']], table_common_usergroup::t()->range());
	foreach($query as $group) {
		$id = $group['groupid'];
		$url = preg_match('/^https?:\/\//is', $listarray[$id]['url']) ? $listarray[$id]['url'] : STATICURL.'image/common/'.$listarray[$id]['url'];
		showtablerow('', ['class="td25"', 'class="td23 td28"', 'class="td24"', 'class="td24"', 'class="td21 td26"'], [
			$listarray[$id]['url'] ? " <img src=\"$url\">" : '',
			'<input type="text" class="txt" name="displayordernew['.$id.']" value="'.$listarray[$id]['displayorder'].'" size="3" />',
			$group['groupid'] <= 8 ? cplang('usergroups_system_'.$id) : $group['grouptitle'],
			'<input type="text" class="txt" name="titlenew['.$id.']" value="'.($listarray[$id]['title'] ? $listarray[$id]['title'] : $group['grouptitle']).'" size="15" />',
			'<input type="text" class="txt" name="urlnew['.$id.']" value="'.$listarray[$id]['url'].'" size="20" />'
		]);

	}

	showsubmit('onlinesubmit', 'submit', 'td');
	showtablefooter();
	showformfooter();

} else {

	if(is_array($_GET['urlnew'])) {
		table_forum_onlinelist::t()->delete_all();
		foreach($_GET['urlnew'] as $id => $url) {
			$url = trim($url);
			if($id == 0 || $url) {
				$data = [
					'groupid' => $id,
					'displayorder' => $_GET['displayordernew'][$id],
					'title' => $_GET['titlenew'][$id],
					'url' => $url,
				];
				table_forum_onlinelist::t()->insert($data);
			}
		}
	}

	updatecache(['onlinelist', 'groupicon']);
	cpmsg('onlinelist_succeed', 'action=misc&operation=onlinelist', 'succeed');

}
	