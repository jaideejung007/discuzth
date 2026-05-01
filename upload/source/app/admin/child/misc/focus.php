<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once libfile('function/post');

$focus = table_common_setting::t()->fetch_setting('focus', true);
$focus_position_array = [
	['portal', cplang('misc_focus_position_portal')],
	['home', cplang('misc_focus_position_home')],
	['member', cplang('misc_focus_position_member')],
	['forum', cplang('misc_focus_position_forum')],
	['group', cplang('misc_focus_position_group')],
	['search', cplang('misc_focus_position_search')],
];

if(!$do) {

	if(!submitcheck('focussubmit')) {

		shownav('extended', 'misc_focus');
		showsubmenu('misc_focus', [
			['admin', 'misc&operation=focus', 1],
			['add', 'misc&operation=focus&do=add'],
			['config', 'misc&operation=focus&do=config', 0],
		]);
		/*search={"misc_focus":"action=misc&operation=focus","admin":"action=misc&operation=focus"}*/
		showtips('misc_focus_tips');
		showformheader('misc&operation=focus');
		showtableheader('admin', 'fixpadding');
		showsubtitle(['', 'subject', 'available', '']);
		if(is_array($focus['data'])) {
			foreach($focus['data'] as $k => $v) {
				showtablerow('', ['class="td25"', '', 'class="td25"', 'class="td25"'], [
					"<input type=\"checkbox\" class=\"checkbox\" name=\"delete[]\" value=\"$k\">",
					'<a href="'.$v['url'].'" target="_blank">'.$v['subject'].'</a>',
					"<input type=\"checkbox\" class=\"checkbox\" name=\"available[$k]\" value=\"1\" ".($v['available'] ? 'checked' : '').'>',
					"<a href=\"".ADMINSCRIPT."?action=misc&operation=focus&do=edit&id=$k\" class=\"act\">{$lang['edit']}</a>",
				]);
			}
		}

		showsubmit('focussubmit', 'submit', 'del');
		showtablefooter();
		showformfooter();
		/*search*/

	} else {

		$newfocus = [];
		$newfocus['title'] = $focus['title'];
		$newfocus['data'] = [];
		if(isset($focus['data']) && is_array($focus['data'])) foreach($focus['data'] as $k => $v) {
			if(is_array($_GET['delete']) && in_array($k, $_GET['delete'])) {
				unset($focus['data'][$k]);
			} else {
				$v['available'] = $_GET['available'][$k] ? 1 : 0;
				$newfocus['data'][$k] = $v;
			}
		}
		$newfocus['cookie'] = $focus['cookie'];
		table_common_setting::t()->update_setting('focus', $newfocus);
		updatecache(['setting', 'focus']);

		cpmsg('focus_update_succeed', 'action=misc&operation=focus', 'succeed');

	}

} elseif($do == 'add') {

	if(count($focus['data']) >= 10) {
		cpmsg('focus_add_num_limit', 'action=misc&operation=focus', 'error');
	}

	if(!submitcheck('addsubmit')) {

		shownav('extended', 'misc_focus');
		showsubmenu('misc_focus', [
			['admin', 'misc&operation=focus', 0],
			['add', 'misc&operation=focus&do=add', 1],
			['config', 'misc&operation=focus&do=config', 0],
		]);
		/*search={"misc_focus":"action=misc&operation=focus","add":"action=misc&operation=focus&do=add"}*/
		showformheader('misc&operation=focus&do=add');
		showtableheader('misc_focus_handadd', 'fixpadding');
		showsetting('misc_focus_handurl', 'focus_url', '', 'text');
		showsetting('misc_focus_handsubject', 'focus_subject', '', 'text');
		showsetting('misc_focus_handsummary', 'focus_summary', '', 'textarea');
		showsetting('misc_focus_handimg', 'focus_image', '', 'text');

		showsetting('misc_focus_position', ['focus_position', $focus_position_array], '', 'mcheckbox');
		showsubmit('addsubmit', 'submit', '', '');
		showtablefooter();
		showformfooter();
		/*search*/

	} else {

		if($_GET['focus_url'] && $_GET['focus_subject'] && $_GET['focus_summary']) {

			if(is_array($focus['data'])) {
				foreach($focus['data'] as $item) {
					if($item['url'] == $_GET['focus_url']) {
						cpmsg('focus_topic_exists', 'action=misc&operation=focus', 'error');
					}
				}
			}
			$focus['data'][] = [
				'url' => $_GET['focus_url'],
				'available' => '1',
				'subject' => cutstr($_GET['focus_subject'], 80),
				'summary' => $_GET['focus_summary'],
				'image' => $_GET['focus_image'],
				'aid' => 0,
				'filename' => basename($_GET['focus_image']),
				'position' => $_GET['focus_position'],
			];
			table_common_setting::t()->update_setting('focus', $focus);
			updatecache(['setting', 'focus']);
		} else {
			cpmsg('focus_topic_addrequired', '', 'error');
		}

		cpmsg('focus_add_succeed', 'action=misc&operation=focus', 'succeed');

	}

} elseif($do == 'edit') {
	$id = intval($_GET['id']);
	if(!$item = $focus['data'][$id]) {
		cpmsg('focus_topic_noexists', 'action=misc&operation=focus', 'error');
	}
	if(!submitcheck('editsubmit')) {

		shownav('extended', 'misc_focus');
		showchildmenu([['misc_focus', 'misc&operation=focus']], $item['subject']);

		showformheader('misc&operation=focus&do=edit&id='.$id);
		showtableheader('misc_focus_edit', 'fixpadding');
		showsetting('misc_focus_handurl', 'focus_url', $item['url'], 'text');
		showsetting('misc_focus_handsubject', 'focus_subject', $item['subject'], 'text');
		showsetting('misc_focus_handsummary', 'focus_summary', $item['summary'], 'textarea');
		showsetting('misc_focus_handimg', 'focus_image', $item['image'], 'text');
		showsetting('misc_focus_position', ['focus_position', $focus_position_array], $item['position'], 'mcheckbox');

		showsubmit('editsubmit', 'submit');
		showtablefooter();
		showformfooter();

	} else {

		if($_GET['focus_url'] && $_GET['focus_subject'] && $_GET['focus_summary']) {
			if($item['type'] == 'thread') {
				$_GET['focus_url'] = $item['url'];
			} else {
				$focus_filename = basename($_GET['focus_image']);
			}
			$item = [
				'url' => $_GET['focus_url'],
				'tid' => $item['tid'],
				'available' => '1',
				'subject' => cutstr($_GET['focus_subject'], 80),
				'summary' => $_GET['focus_summary'],
				'image' => $_GET['focus_image'],
				'aid' => 0,
				'filename' => $focus_filename,
				'position' => $_GET['focus_position'],
			];
			$focus['data'][$id] = $item;
			table_common_setting::t()->update_setting('focus', $focus);
			updatecache(['setting', 'focus']);
		}

		cpmsg('focus_edit_succeed', 'action=misc&operation=focus', 'succeed');

	}

} elseif($do == 'config') {

	if(!submitcheck('confsubmit')) {

		shownav('extended', 'misc_focus');
		showsubmenu('misc_focus', [
			['admin', 'misc&operation=focus', 0],
			['add', 'misc&operation=focus&do=add', 0],
			['config', 'misc&operation=focus&do=config', 1],
		]);
		/*search={"misc_focus":"action=misc&operation=focus","config":"action=misc&operation=focus&do=config"}*/
		showformheader('misc&operation=focus&do=config');
		showtableheader('config', 'fixpadding');
		showsetting('misc_focus_area_title', 'focus_title', empty($focus['title']) ? cplang('misc_focus') : $focus['title'], 'text');
		showsetting('misc_focus_area_cookie', 'focus_cookie', empty($focus['cookie']) ? 0 : $focus['cookie'], 'text');
		showsubmit('confsubmit', 'submit');
		showtablefooter();
		showformfooter();
		/*search*/

	} else {

		$focus['title'] = trim($_GET['focus_title']);
		$focus['title'] = empty($focus['title']) ? cplang('misc_focus') : $focus['title'];
		$focus['cookie'] = trim(intval($_GET['focus_cookie']));
		$focus['cookie'] = empty($focus['cookie']) ? 0 : $focus['cookie'];
		table_common_setting::t()->update_setting('focus', $focus);
		updatecache(['setting', 'focus']);

		cpmsg('focus_conf_succeed', 'action=misc&operation=focus&do=config', 'succeed');

	}

}
	