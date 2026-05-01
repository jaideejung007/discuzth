<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

/*search={"setting_optimize":"action=setting&operation=seo","setting_memorydata":"action=setting&operation=memorydata"}*/
$cache_keys = getmemorycachekeys();
if(submitcheck('memorydatasubmit')) {
	$flag = 0;
	foreach($cache_keys as $k) {
		if(($id = $_GET[$k.'_id'])) {
			if($k == 'common_member') {
				$uid = intval($id);
				table_common_member::t()->clear_cache($uid);
				table_common_member_status::t()->clear_cache($uid);
				table_common_member_count::t()->clear_cache($uid);
				table_common_member_profile::t()->clear_cache($uid);
				table_common_member_field_home::t()->clear_cache($uid);
				table_common_member_field_forum::t()->clear_cache($uid);
				table_common_member_verify::t()->clear_cache($uid);
				helper_forumperm::clear_cache($uid);
			} elseif($k == 'forum_thread_forumdisplay') {
				memory('rm', $id, 'forumdisplay_');
			} elseif($k == 'forumindex') {
				memory('rm', 'forum_index_page_'.$id);
			} elseif($k == 'diyblock' || $k == 'diyblockoutput') {
				table_common_block::t()->clear_blockcache($id);
			} else {
				C::t($k)->clear_cache($id);
			}
			$flag = 1;
		}
	}
	if($flag) {
		cpmsg('setting_memory_rm_succeed', 'action=setting&operation=memorydata', 'succeed', '', FALSE);
	} else {
		cpmsg('setting_memory_rm_error', 'action=setting&operation=memorydata', 'error', '', FALSE);
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

	$setting['memory'] = dunserialize($setting['memory']);
	showtableheader('setting_memorydata', 'fixpadding');
	showsubtitle(['setting_memory_func', 'setting_memorydata_rm_cache_key', '', '']);

	foreach($cache_keys as $skey) {
		if(isset($setting['memory'][$skey])) {
			showtablerow('', ['width="120"', 'width="120"', '', ''], [
				cplang('setting_memory_func_'.$skey),
				'<input type="text" class="txt" name="'.$skey.'_id" id="'.$skey.'_id" value="">',
				cplang('setting_memory_data_'.$skey.'_comment'),
			]);
		}
	}
	showsubmit('memorydatasubmit');
	showtablefooter();
	showtagfooter('div');
	/*search*/
	showformfooter();

}