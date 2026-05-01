<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('makehtmlsetting')) {
	/*search={"nav_makehtml":"action=makehtml","setting_functions_makehtml":"action=makehtml&operation=makehtmlsetting"}*/
	$setting = $_G['setting'];
	showformheader('makehtml&operation=makehtmlsetting');
	showtableheader('', 'nobottom', 'id="makehtml"'.($operation != 'makehtmlsetting' ? ' style="display: none"' : ''));
	showsetting('setting_functions_makehtml', 'settingnew[makehtml][flag]', $setting['makehtml']['flag'], 'radio', 0, 1);
	showsetting('setting_functions_makehtml_extendname', 'settingnew[makehtml][extendname]', $setting['makehtml']['extendname'] ? $setting['makehtml']['extendname'] : 'html', 'text');
	showsetting('setting_functions_makehtml_articlehtmldir', 'settingnew[makehtml][articlehtmldir]', $setting['makehtml']['articlehtmldir'], 'text');
	$dirformat = ['settingnew[makehtml][htmldirformat]',
		[[0, dgmdate(TIMESTAMP, '/Ym/')],
			[1, dgmdate(TIMESTAMP, '/Ym/d/')],
			[2, dgmdate(TIMESTAMP, '/Y/m/')],
			[3, dgmdate(TIMESTAMP, '/Y/m/d/')]]
	];
	showsetting('setting_functions_makehtml_htmldirformat', $dirformat, $setting['makehtml']['htmldirformat'], 'select');
	showsetting('setting_functions_makehtml_topichtmldir', 'settingnew[makehtml][topichtmldir]', $setting['makehtml']['topichtmldir'], 'text');
	showsetting('setting_functions_makehtml_indexname', 'settingnew[makehtml][indexname]', $setting['makehtml']['indexname'] ? $setting['makehtml']['indexname'] : 'index', 'text');
	showtagfooter('tbody');
	showsubmit('makehtmlsetting', 'submit');
	showtablefooter();
	showformfooter();
	/*search*/
} else {
	$settingnew = $_GET['settingnew'];
	if(isset($settingnew['makehtml'])) {
		$settingnew['makehtml']['flag'] = intval($settingnew['makehtml']['flag']);
		$settingnew['makehtml']['extendname'] = !$settingnew['makehtml']['extendname'] || !in_array($settingnew['makehtml']['extendname'], ['htm', 'html']) ? 'html' : $settingnew['makehtml']['extendname'];
		if(!$settingnew['makehtml']['indexname']) {
			$settingnew['makehtml']['indexname'] = 'index';
		} else {
			$re = NULL;
			preg_match_all('/[^\w\d\_]/', $settingnew['makehtml']['indexname'], $re);
			if(!empty($re[0]) || str_contains('..', $settingnew['makehtml']['indexname'])) {
				cpmsg(cplang('setting_functions_makehtml_indexname_invalid').','.cplang('return'), NULL, 'error');
			}
		}
		$settingnew['makehtml']['articlehtmldir'] = trim($settingnew['makehtml']['articlehtmldir'], ' /\\');
		$re = NULL;
		preg_match_all('/[^\w\d\_\\\\]/', $settingnew['makehtml']['articlehtmldir'], $re);
		if(!empty($re[0]) || !check_html_dir($settingnew['makehtml']['articlehtmldir'])) {
			cpmsg(cplang('setting_functions_makehtml_articlehtmldir_invalid').','.cplang('return'), NULL, 'error');
		}
		$settingnew['makehtml']['topichtmldir'] = trim($settingnew['makehtml']['topichtmldir'], ' /\\');
		$re = NULL;
		preg_match_all('/[^\w\d\_\\\\]/', $settingnew['makehtml']['topichtmldir'], $re);
		if(!empty($re[0]) || !check_html_dir($settingnew['makehtml']['topichtmldir'])) {
			cpmsg(cplang('setting_functions_makehtml_topichtmldir_invalid').','.cplang('return'), NULL, 'error');
		}
		$topichtmldir = realpath($settingnew['makehtml']['topichtmldir']);

		if($topichtmldir === false) {
			dmkdir($settingnew['makehtml']['topichtmldir'], 777, false);
			$topichtmldir = realpath($settingnew['makehtml']['topichtmldir']);
			rmdir($settingnew['makehtml']['topichtmldir']);
			if($topichtmldir === false) {
				cpmsg(cplang('setting_functions_makehtml_topichtmldir_invalid').','.cplang('return'), NULL, 'error');
			}
		}
		$topichtmldir = str_replace(DISCUZ_ROOT_STATIC, '', $topichtmldir);
		$sysdir = ['api', 'archiver', 'config', 'data/diy', 'data\diy', 'install', 'source', 'static', 'template', 'uc_client', 'uc_server'];
		foreach($sysdir as $_dir) {
			if(stripos($topichtmldir, $_dir) === 0) {
				cpmsg(cplang('setting_functions_makehtml_topichtmldir_invalid').','.cplang('return'), NULL, 'error');
			}
		}
		$settingnew['makehtml']['htmldirformat'] = intval($settingnew['makehtml']['htmldirformat']);
		table_common_setting::t()->update_setting('makehtml', $settingnew['makehtml']);
		updatecache('setting');
	}
	cpmsg('setting_update_succeed', 'action=makehtml&operation=makehtmlsetting', 'succeed');
}
	