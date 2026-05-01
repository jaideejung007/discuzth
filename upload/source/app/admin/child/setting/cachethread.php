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
	if(isset($settingnew['cachethreaddir']) && isset($settingnew['threadcaches'])) {
		if($settingnew['cachethreaddir'] && !is_writable(DISCUZ_ROOT.'./'.$settingnew['cachethreaddir'])) {
			cpmsg('cachethread_dir_noexists', '', 'error', ['cachethreaddir' => $settingnew['cachethreaddir']]);
		}
		if(!empty($_GET['fids'])) {
			table_forum_forum::t()->update_threadcaches($settingnew['threadcaches'], $_GET['fids']);
		}
	}
} else {
	shownav('global', 'setting_'.$operation);

	$current = [$operation => 1];
	$memorydata = memory('check') ? ['setting_memorydata', 'setting&operation=memorydata', $current['memorydata']] : '';
	showsubmenu('setting_optimize', [
		['setting_cachethread', 'setting&operation=cachethread', $current['cachethread']],
		['setting_serveropti', 'setting&operation=serveropti', $current['serveropti']],
		['setting_memory', 'setting&operation=memory', $current['memory']],
		$memorydata
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	include_once libfile('function/forumlist');
	$forumselect = '<select name="fids[]" multiple="multiple" size="10"><option value="all">'.$lang['all'].'</option><option value="">&nbsp;</option>'.forumselect(FALSE, 0, 0, TRUE).'</select>';
	/*search={"setting_optimize":"action=setting&operation=seo","setting_cachethread":"action=setting&operation=cachethread"}*/
	showtableheader();
	showtitle('setting_cachethread');
	showsetting('setting_cachethread_indexlife', 'settingnew[cacheindexlife]', $setting['cacheindexlife'], 'text');
	showsetting('setting_cachethread_life', 'settingnew[cachethreadlife]', $setting['cachethreadlife'], 'text');
	showsetting('setting_cachethread_dir', 'settingnew[cachethreaddir]', $setting['cachethreaddir'], 'text');

	showtitle('setting_cachethread_coefficient_set');
	showsetting('setting_cachethread_coefficient', 'settingnew[threadcaches]', '', "<input type=\"text\" class=\"txt\" size=\"30\" name=\"settingnew[threadcaches]\" value=\"{$setting['threadcaches']}\">");
	showsetting('setting_cachethread_coefficient_forum', '', '', $forumselect);
	/*search*/

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}