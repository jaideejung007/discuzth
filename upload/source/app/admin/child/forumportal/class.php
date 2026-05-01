<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

class fp {

	public static function list() {
		global $_G;

		$s = &$_G['setting']['forumportal'];
		if(!submitcheck('submit')) {
			showformheader('forumportal&operation=list');
			self::_showList($s['navList']);
			showformfooter();
		} else {
			self::_listSubmit($_GET['list']);
			cpmsg('setting_update_succeed', 'action=forumportal', 'succeed');
		}
	}

	public static function edit() {
		global $_G;

		$s = &$_G['setting']['forumportal'];
		if(empty($s['navList'][$_GET['id']])) {
			cpmsg('forumportal_nav_not_found', '', 'error');
		}
		if(!submitcheck('submit')) {
			showchildmenu([['menu_forums_portal', 'forumportal']], $s['navList'][$_GET['id']]['name']);
			showformheader('forumportal&operation=edit&id='.$_GET['id']);
			showtableheader('', 'tb2');
			self::_showForum($s['navList'][$_GET['id']]);
			showtablefooter();
			showformfooter();
		} else {
			self::_editSubmit($_GET['id'], $_GET['s']);
			cpmsg('setting_update_succeed', 'action=forumportal', 'succeed');
		}
	}

	public static function add() {
		if(!submitcheck('submit')) {
			showformheader('forumportal&operation=add');
			showtableheader('', 'tb2');
			self::_showForum();
			showtablefooter();
			showformfooter();
		} else {
			self::_addSubmit($_GET['s']);
			cpmsg('setting_update_succeed', 'action=forumportal', 'succeed');
		}
	}

	public static function setting() {
		global $_G;
		if(!submitcheck('submit')) {
			showformheader('forumportal&operation=setting');
			showtableheader();
			showsetting('forumportal_tpp', 's[tpp]', $_G['setting']['forumportal']['setting']['tpp'], 'text');
			showsetting('forumportal_image_width', 's[image][width]', $_G['setting']['forumportal']['setting']['image']['width'], 'text');
			showsetting('forumportal_image_height', 's[image][height]', $_G['setting']['forumportal']['setting']['image']['height'], 'text');
			showsubmit('submit');
			showtablefooter();
			showformfooter();
		} else {
			self::_settingSubmit($_GET['s']);
			cpmsg('setting_update_succeed', 'action=forumportal&operation=setting', 'succeed');
		}
	}

	private static function _showList($data) {
		showtableheader('', '');
		showsubtitle(['del', 'display_order', 'available', 'name']);
		foreach($data as $id => $row) {
			showtablerow('header', ['width="30"', 'class="td25"', 'width="30"', '', ''], [
				'<input name="list[del][]" type="checkbox" class="checkbox" value="'.$id.'">',
				'<input name="list[display_order]['.$id.']" class="txt" value="'.$row['display_order'].'">',
				'<input name="list[allow]['.$id.']" type="checkbox" class="checkbox" value="1"'.($row['allow'] ? ' checked' : '').'>',
				'<a href="'.ADMINSCRIPT.'?action=forumportal&operation=edit&id='.$id.'">'.$row['name'].'</a>',
			]);
		}
		global $_G;
		$styleid = $_G['style']['styleid'];
		showsubmit('submit', 'submit', '',
			'<input type="button" class="btn" value="'.cplang('add').'" onclick="location.href=\''.ADMINSCRIPT.'?action=forumportal&operation=add\'"/>'.
			'<a href="'.ADMINSCRIPT.'?action=cells&id='.$styleid.'&frames=yes&cellId=forum/portal/navlist" target="_blank">'.cplang('forumportal_edit_tpl_nav').'</a>&nbsp;&nbsp;'.
			'<a href="'.ADMINSCRIPT.'?action=cells&id='.$styleid.'&frames=yes&cellId=forum/portal/threadlist" target="_blank">'.cplang('forumportal_edit_tpl_list').'</a>'
		);
		showtablefooter();
	}

	private static function _showForum($setting = []) {
		showhiddenfields(['s[display_order]' => $setting['display_order']]);
		/*search={"menu_forums_portal":"action=forumportal"}*/
		showsetting('subject', 's[name]', $setting['name'], 'text');
		showsetting('available', 's[allow]', $setting['allow'], 'radio');
		showsetting('forumportal_adminid', ['s[adminid]', [
			[0, cplang('usergroups_system_0')],
			[1, cplang('usergroups_system_1')],
			[2, cplang('usergroups_system_2')],
			[3, cplang('usergroups_system_3')],
		]], $setting['adminid'] ?? 0, 'select');
		showtitle('forumportal_filter');
		showsetting('forumportal_forum_fids', '', '', self::_forumList($setting['forum_fids']));
		showsetting('forumportal_group_fids', 's[group_fids]', $setting['group_fids'], 'text');
		showsetting('forumportal_uids', 's[authorids]', $setting['authorids'], 'text');
		showsetting('forumportal_follow', ['s[follow]', [
			[0, cplang('forumportal_follow_nolimit')],
			[1, cplang('forumportal_follow_onlyfollower')],
		]], $setting['follow'] ?? 0, 'mradio');
		showsetting('forumportal_digest_thread', ['s[digest]', [
			[0, cplang('forumportal_normal')],
			[1, cplang('forumportal_digest').' I'],
			[2, cplang('forumportal_digest').' II'],
			[3, cplang('forumportal_digest').' III'],
		]], $setting['digest'] ?? 0, 'select');
		showsetting('forumportal_sticky_thread', ['s[displayorder]', [
			[0, cplang('forumportal_normal')],
			[1, cplang('forumportal_sticky').' I'],
			[2, cplang('forumportal_sticky').' II'],
			[3, cplang('forumportal_sticky').' III'],
			[4, cplang('forumportal_sticky_all')],
		]], $setting['displayorder'] ?? 0, 'select');
		showsetting('forumportal_special_thread', ['s[special][]', [
			[0, cplang('thread_general')],
			[1, cplang('thread_poll')],
			[2, cplang('thread_trade')],
			[3, cplang('thread_reward')],
			[4, cplang('thread_activity')],
			[5, cplang('thread_debate')],
		]], $setting['special'], 'mselect');
		showsetting('forumportal_heats', 's[heats]', $setting['heats'] ?? 0, 'text');
		showsetting('forumportal_recommends', 's[recommends]', $setting['recommends'] ?? 0, 'text');
		showsetting('forumportal_dateline', ['s[dateline]', [
			[0, cplang('forumportal_time_nolimit')],
			[3600, cplang('forumportal_time_hour')],
			[86400, cplang('forumportal_time_24hours')],
			[604800, cplang('forumportal_time_7days')],
			[2592000, cplang('forumportal_time_month')],
		]], $setting['dateline'] ?? 0, 'mradio');
		showsetting('forumportal_lastupdate', ['s[laspost]', [
			[0, cplang('forumportal_time_nolimit')],
			[3600, cplang('forumportal_time_hour')],
			[86400, cplang('forumportal_time_24hours')],
			[604800, cplang('forumportal_time_7days')],
			[2592000, cplang('forumportal_time_month')],
		]], $setting['laspost'] ?? 0, 'mradio');
		showtitle('forumportal_sort');
		showsetting('forumportal_order_before', ['s[order_before]', [
			[0, cplang('forumportal_none')],
			[1, cplang('forumportal_displayorder')],
			[2, cplang('forumportal_digest')],
		]], $setting['order_before'] ?? 0, 'mradio');
		showsetting('forumportal_order', ['s[order]', [
			[0, cplang('forumportal_lastpost')],
			[1, cplang('forumportal_dateline')],
			[2, cplang('forumportal_replies')],
			[3, cplang('forumportal_views')],
			[4, cplang('forumportal_heads')],
			[5, cplang('forumportal_recommends')],
		]], $setting['order'] ?? 0, 'mradio');
		showsubmit('submit');
		/*search*/
	}

	private static function _addSubmit($data) {
		global $_G;

		$s = &$_G['setting']['forumportal'];

		$data['name'] = strip_tags($data['name']);
		$s['navList'][] = $data;

		self::_save($s);
	}

	private static function _editSubmit($id, $data) {
		global $_G;

		$s = &$_G['setting']['forumportal'];

		$data['name'] = strip_tags($data['name']);
		$s['navList'][$id] = $data;

		self::_save($s);
	}

	private static function _settingSubmit($data) {
		global $_G;

		$s = &$_G['setting']['forumportal'];

		$s['setting'] = $data;

		self::_save($s);
	}

	private static function _listSubmit($data) {
		global $_G;
		$s = &$_G['setting']['forumportal'];

		if($data['del']) {
			foreach($data['del'] as $id) {
				unset($s['navList'][$id]);
			}
		}

		if($data['display_order']) {
			foreach($data['display_order'] as $id => $display_order) {
				if(!isset($s['navList'][$id])) {
					continue;
				}
				$s['navList'][$id]['display_order'] = $display_order;
			}
			uasort($s['navList'], function($a, $b) {
				return $a['display_order'] < $b['display_order'] ? -1 : 1;
			});
		}
		foreach($s['navList'] as $id => $row) {
			$s['navList'][$id]['allow'] = $data['allow'][$id] ? 1 : 0;
		}

		self::_save($s);
	}

	private static function _forumList($fids = []) {
		$fids = (array)$fids;
		$where = 'status IN (0,1,2)';
		$forums = DB::fetch_all('SELECT fid, fup, name, type, status FROM %t WHERE %i ORDER BY displayorder', [
			'forum_forum',
			$where
		]);
		$list = $subs = [];
		foreach($forums as $forum) {
			if(!$forum['status']) {
				$forum['name'] .= '('.cplang('forumportal_hidden').')';
			}
			if($forum['type'] == 'group') {
				$list[$forum['fid']]['name'] = $forum['name'];
			} elseif($forum['type'] == 'forum') {
				$list[$forum['fup']]['subs'][$forum['fid']] = $forum['name'];
			} elseif($forum['type'] == 'sub') {
				$subs[$forum['fup']][$forum['fid']] = $forum['name'];
			}
		}
		$s = '';
		foreach($list as $row) {
			$s .= '<optgroup label="'.$row['name'].'">';
			foreach($row['subs'] as $fid => $name) {
				$s .= '<option value="'.$fid.'"'.(in_array($fid, $fids) ? ' selected' : '').'>'.$name.'</option>';
				if(isset($subs[$fid])) {
					foreach($subs[$fid] as $fid => $name) {
						$s .= '<option value="'.$fid.'"'.(in_array($fid, $fids) ? ' selected' : '').'>&nbsp;&nbsp;'.$name.'</option>';
					}
				}
			}
			$s .= '</optgroup>';
		}
		return '<select name="s[forum_fids][]" size="10" multiple="multiple">'.$s.'</select>';
	}

	private static function _save($s) {
		$settings = [
			'forumportal' => $s,
		];
		table_common_setting::t()->update_batch($settings);

		require_once libfile('function/cache');
		updatecache('setting');
	}

}
