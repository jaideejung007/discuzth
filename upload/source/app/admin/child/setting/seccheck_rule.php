<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$setting['seccodedata'] = dunserialize($setting['seccodedata']);
$setting['secqaa'] = dunserialize($setting['secqaa']);

if(submitcheck('settingsubmit')) {

	$setting['seccodedata']['rule'][$do] = $settingnew['seccodedata']['rule'][$do];
	$setting['secqaa']['rule'][$do] = $settingnew['secqaa']['rule'][$do];

	table_common_setting::t()->update_setting('secqaa', $setting['secqaa']);
	table_common_setting::t()->update_setting('seccodedata', $setting['seccodedata']);

	updatecache('setting');

	cpmsg('setting_update_succeed', 'action='.(!empty($action) ? $action : 'setting').'&operation='.$operation.'&do='.$do, 'succeed');

} else {

	if(!in_array($do, ['register', 'login', 'post'])) {
		cpmsg('undefined_action');
	}
	showchildmenu([['setting_seccheck', 'setting&operation=seccheck']], cplang('setting_sec_seccode_status_'.$do));

	showformheader('setting&edit=yes');
	showhiddenfields(['operation' => $operation, 'do' => $do]);

	if($setting['seccodedata']['rule'][$do]['allow'] == 1) {
		$setting['seccodedata']['rule'][$do]['allow'] = 0;
	}

	if($do == 'register') {
		showtableheader('');
		showsetting('setting_sec_seccode_rule_register_auto', ['settingnew[seccodedata][rule][register][allow]', [
			[2, cplang('yes'), ['auto' => '']],
			[0, cplang('no'), ['auto' => 'none']],
		]], intval($setting['seccodedata']['rule']['register']['allow']), 'mradio', '', 1);
		showtagheader('tbody', 'auto', $setting['seccodedata']['rule']['register']['allow']);
		showsetting('setting_sec_seccode_rule_register_numlimit', 'settingnew[seccodedata][rule][register][numlimit]', $setting['seccodedata']['rule']['register']['numlimit'], 'text');
		showsetting('setting_sec_seccode_rule_register_timelimit', ['settingnew[seccodedata][rule][register][timelimit]', [
			[60, '1 '.cplang('setting_sec_seccode_rule_min')],
			[180, '3'.cplang('setting_sec_seccode_rule_min')],
			[300, '5'.cplang('setting_sec_seccode_rule_min')],
			[900, '15'.cplang('setting_sec_seccode_rule_min')],
			[1800, '30'.cplang('setting_sec_seccode_rule_min')],
			[3600, '1'.cplang('setting_sec_seccode_rule_hour')],
		]], $setting['seccodedata']['rule']['register']['timelimit'], 'select', 'noborder');
		showtagfooter('tbody');
	} elseif($do == 'login') {
		showtableheader('');
		showsetting('setting_sec_seccode_rule_login_auto', ['settingnew[seccodedata][rule][login][allow]', [
			[2, cplang('yes'), ['auto' => '']],
			[0, cplang('no'), ['auto' => 'none']],
		]], intval($setting['seccodedata']['rule']['login']['allow']), 'mradio', '', 1);
		showtagheader('tbody', 'auto', $setting['seccodedata']['rule']['login']['allow']);
		showsetting('setting_sec_seccode_rule_login_nolocal', 'settingnew[seccodedata][rule][login][nolocal]', $setting['seccodedata']['rule']['login']['nolocal'], 'radio');
		showsetting('setting_sec_seccode_rule_login_pwsimple', 'settingnew[seccodedata][rule][login][pwsimple]', $setting['seccodedata']['rule']['login']['pwsimple'], 'radio');
		showsetting('setting_sec_seccode_rule_login_pwerror', 'settingnew[seccodedata][rule][login][pwerror]', $setting['seccodedata']['rule']['login']['pwerror'], 'radio');
		showsetting('setting_sec_seccode_rule_login_outofday', 'settingnew[seccodedata][rule][login][outofday]', $setting['seccodedata']['rule']['login']['outofday'], 'text');
		showsetting('setting_sec_seccode_rule_login_numiptry', 'settingnew[seccodedata][rule][login][numiptry]', $setting['seccodedata']['rule']['login']['numiptry'], 'text');
		showsetting('setting_sec_seccode_rule_login_timeiptry', ['settingnew[seccodedata][rule][login][timeiptry]', [
			[60, '1 '.cplang('setting_sec_seccode_rule_min')],
			[180, '3'.cplang('setting_sec_seccode_rule_min')],
			[300, '5'.cplang('setting_sec_seccode_rule_min')],
			[900, '15'.cplang('setting_sec_seccode_rule_min')],
			[1800, '30'.cplang('setting_sec_seccode_rule_min')],
			[3600, '1'.cplang('setting_sec_seccode_rule_hour')],
		]], $setting['seccodedata']['rule']['login']['timeiptry'], 'select', 'noborder');
		showtagfooter('tbody');
	} elseif($do == 'post') {
		showtableheader('');
		showsetting('setting_sec_seccode_rule_register_auto', ['settingnew[seccodedata][rule][post][allow]', [
			[2, cplang('yes'), ['auto' => '']],
			[0, cplang('no'), ['auto' => 'none']],
		]], intval($setting['seccodedata']['rule']['post']['allow']), 'mradio', '', 1);
		showtagheader('tbody', 'auto', $setting['seccodedata']['rule']['post']['allow']);
		showsetting('setting_sec_seccode_rule_post_numlimit', 'settingnew[seccodedata][rule][post][numlimit]', $setting['seccodedata']['rule']['post']['numlimit'], 'text');
		showsetting('setting_sec_seccode_rule_post_timelimit', ['settingnew[seccodedata][rule][post][timelimit]', [
			[60, '1 '.cplang('setting_sec_seccode_rule_min')],
			[180, '3'.cplang('setting_sec_seccode_rule_min')],
			[300, '5'.cplang('setting_sec_seccode_rule_min')],
			[900, '15'.cplang('setting_sec_seccode_rule_min')],
			[1800, '30'.cplang('setting_sec_seccode_rule_min')],
			[3600, '1'.cplang('setting_sec_seccode_rule_hour')],
		]], $setting['seccodedata']['rule']['post']['timelimit'], 'select', 'noborder');
		showsetting('setting_sec_seccode_rule_post_nplimit', 'settingnew[seccodedata][rule][post][nplimit]', $setting['seccodedata']['rule']['post']['nplimit'], 'text');
		showsetting('setting_sec_seccode_rule_post_vplimit', 'settingnew[seccodedata][rule][post][vplimit]', $setting['seccodedata']['rule']['post']['vplimit'], 'text');
		showtagfooter('tbody');
	}
	showtagfooter('tbody');
	showsubmit('settingsubmit');
	showtablefooter();

}