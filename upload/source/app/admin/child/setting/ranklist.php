<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('settingsubmit')) {
	if($_GET['updateranklistcache']) {
		if($_GET['update_ranklist_cache']) {
			foreach($_GET['update_ranklist_cache'] as $var) {
				savecache('ranklist_'.$var, '');
			}
		}
		cpmsg('ranklistcache_update', 'action=setting&operation='.$operation.(!empty($_GET['anchor']) ? '&anchor='.$_GET['anchor'] : '').(!empty($from) ? '&from='.$from : ''), 'succeed');
	}
} else {
	shownav('global', 'setting_'.$operation);

	showsubmenu('setting_'.$operation);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	/*search={"setting_ranklist":"action=setting&operation=ranklist"}*/
	$setting['ranklist'] = dunserialize($setting['ranklist']);
	showtableheader('', 'nobottom', 'id="all"');
	showsetting('setting_ranklist_index_cache_time', 'settingnew[ranklist][cache_time]', $setting['ranklist']['cache_time'], 'text');
	showsetting('setting_ranklist_index_select', ['settingnew[ranklist][index_select]', [['all', cplang('dateline_all')], ['thismonth', cplang('thismonth')], ['thisweek', cplang('thisweek')], ['today', cplang('today')]]], $setting['ranklist']['index_select'], 'select');
	showsetting('setting_ranklist_ignorefid', 'settingnew[ranklist][ignorefid]', $setting['ranklist']['ignorefid'], 'text');
	// 新增 竞价排名开关和公告信息
	showsetting('setting_ranklist_member_show', 'settingnew[ranklist][membershow]', $setting['ranklist']['membershow'], 'radio', 0, 1);
	showsetting('setting_ranklist_member_show_announcement', 'settingnew[ranklist][membershowannouncement]', $setting['ranklist']['membershowannouncement'], 'textarea');
	showtablefooter();

	showtableheader('setting_ranklist_block_set', 'fixpadding nobottom', 'id="other"');
	showsubtitle(['setting_credits_available', 'setting_ranklist_block_name', 'setting_ranklist_cache_time', 'setting_ranklist_show_num'], '');
	$ranklist = ['member', 'thread', 'blog', 'poll', 'activity', 'picture', 'forum', 'group'];

	if(!is_array($setting['ranklist'])) {
		$setting['ranklist'] = [];
	}
	foreach($ranklist as $i) {
		showtablerow('', ['width="60"', 'class="td22"', 'class="td31"', 'class="td28"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"settingnew[ranklist][$i][available]\" value=\"1\" ".($setting['ranklist'][$i]['available'] ? 'checked' : '').' />',
			cplang('setting_ranklist_'.$i),
			"<input type=\"text\" class=\"txt\" size=\"8\" name=\"settingnew[ranklist][$i][cache_time]\" value=\"{$setting['ranklist'][$i]['cache_time']}\">",
			"<input type=\"text\" class=\"txt\" size=\"8\" name=\"settingnew[ranklist][$i][show_num]\" value=\"{$setting['ranklist'][$i]['show_num']}\">"
		]);
	}
	showtablerow('', 'colspan="10" class="lineheight"', $lang['setting_ranklist_block_comment']);
	showtablefooter();

	showtableheader('', 'notop');
	showsubmit('settingsubmit');
	showtablefooter();

	showtableheader('', 'nobottom');
	$ranklistarray = [];
	$ranklistarray[] = ['index', cplang('setting_ranklist_index')];
	foreach($ranklist as $k) {
		$ranklistarray[] = [$k, cplang('setting_ranklist_'.$k)];
	}
	showsetting('setting_ranklist_update_cache_choose', ['update_ranklist_cache', $ranklistarray], '', 'mcheckbox');
	showtablerow('', 'colspan="10" class="lineheight"', $lang['setting_ranklist_cache_comment']);
	showtablefooter();

	showtableheader('', 'notop');
	showhiddenfields(['updateranklistcache' => 0]);
	showsubmit('', '', '<input type="submit" class="btn" name="settingsubmit" value="'.cplang('setting_ranklist_update_cache').'" onclick="this.form.updateranklistcache.value=1">');
	showtablefooter();
	/*search*/

	showformfooter();
}