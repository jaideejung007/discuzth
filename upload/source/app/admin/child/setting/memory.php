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
	if(!empty($settingnew['memory'])) {
		$memory = [];
		foreach($settingnew['memory'] as $k => $v) {
			if(!empty($settingnew['memory'][$k]['enable'])) {
				$memory[$k] = intval($settingnew['memory'][$k]['ttl']);
			}
		}
		if(isset($memory['common_member'])) {
			$memory['common_member_count'] = $memory['common_member_status'] = $memory['common_member_profile'] = $memory['common_member_field_home'] = $memory['common_member_field_forum'] = $memory['common_member_verify'] = $memory['common_member'];
		} else {
			unset($memory['common_member_count'], $memory['common_member_status'], $memory['common_member_profile'], $memory['common_member_field_home'], $memory['common_member_field_forum'], $memory['common_member_verify']);
		}
		$settingnew['memory'] = $memory;
	}
} else {
	shownav('global', 'setting_optimize');

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

	/*search={"setting_optimize":"action=setting&operation=seo","setting_memory":"action=setting&operation=memory"}*/
	showtips('setting_memory_tips');
	showtableheader('setting_memory_status', 'fixpadding');
	showsubtitle(['setting_memory_state_interface', 'setting_memory_state_extension', 'setting_memory_state_config', 'setting_memory_clear', '']);

	$do_clear_ok = $do == 'clear' ? cplang('setting_memory_do_clear') : '';
	$do_clear_link = '<a href="'.ADMINSCRIPT.'?action=setting&operation=memory&do=clear">'.cplang('setting_memory_clear').'</a>'.$do_clear_ok;

	$cache_extension = C::memory()->extension;
	$cache_config = C::memory()->config;
	$cache_type = C::memory()->type;

	$dir = DISCUZ_ROOT.'./source/class/memory';
	$qaadir = dir($dir);
	$cachelist = [];
	while($entry = $qaadir->read()) {
		if(!in_array($entry, ['.', '..']) && preg_match('/^memory\_driver\_[\w\.]+$/', $entry) && str_ends_with($entry, '.php') && strlen($entry) < 30 && is_file($dir.'/'.$entry)) {
			$cache = str_replace(['.php', 'memory_driver_'], '', $entry);
			$class_name = 'memory_driver_'.$cache;
			$memory = new $class_name();
			$available = is_array($cache_config[$cache]) ? !empty($cache_config[$cache]['server']) : !empty($cache_config[$cache]);
			$cachelist[] = [$memory->cacheName,
				$memory->env($config) ? cplang('setting_memory_php_enable') : cplang('setting_memory_php_disable'),
				$available ? cplang('open') : cplang('closed'),
				$cache_type == $memory->cacheName ? $do_clear_link : '--'
			];
		}
	}

	foreach($cachelist as $cache) {
		showtablerow('', ['width="100"', 'width="120"', 'width="120"'], $cache);
	}
	showtablefooter();

	if(!isset($setting['memory'])) {
		table_common_setting::t()->update_setting('memory', '');
		$setting['memory'] = '';
	}

	if($do == 'clear') {
		C::memory()->clear();
	}

	$setting['memory'] = dunserialize($setting['memory']);
	showtableheader('setting_memory_function', 'fixpadding');
	showsubtitle(['setting_memory_func', 'setting_memory_func_enable', 'setting_memory_func_ttl', '']);

	foreach(getmemorycachekeys() as $skey) {
		$ttl = isset($setting['memory'][$skey]) ? intval($setting['memory'][$skey]) : '';
		showtablerow('', ['width="120"', 'width="120"', 'width="120"', ''], [
			cplang('setting_memory_func_'.$skey),
			'<input type="checkbox" class="checkbox" name="settingnew[memory]['.$skey.'][enable]" '.($ttl !== '' ? 'checked' : '').' value="1">',
			'<input type="text" class="txt" name="settingnew[memory]['.$skey.'][ttl]" value="'.$ttl.'">', cplang('setting_memory_func_'.$skey.'_comment'),
		]);
	}
	/*search*/

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}