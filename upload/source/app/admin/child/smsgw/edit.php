<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($_GET['smsgwid'])) {
	cpmsg('undefined_action');
}

if(!submitcheck('smsgwsubmit')) {

	$smsgwid = $_GET['smsgwid'];
	$smsgw = table_common_smsgw::t()->fetch($smsgwid);
	if(!$smsgw) {
		cpmsg('smsgw_nonexistence', '', 'error');
	}
	$smsgw['parameters'] = dunserialize($smsgw['parameters']);
	$class = $smsgw['class'];

	$etype = explode(':', $class);
	if(count($etype) > 1 && preg_match('/^[\w\_:]+$/', $class)) {
		include_once DISCUZ_PLUGIN($etype[0]).'/smsgw/smsgw_'.$etype[1].'.php';
		$smsgwclass = 'smsgw_'.$etype[1];
	} else {
		require_once libfile('smsgw/'.$class, 'class');
		$smsgwclass = 'smsgw_'.$class;
	}
	$smsgwclass = new $smsgwclass;
	$smsgwsetting = $smsgwclass->getsetting();
	$smsgwname = lang('smsgw/'.$class, $smsgwclass->name).' '.$smsgwclass->customname;
	$returnurl = 'action=smsgw&operation=list';

	shownav('extended', 'smsgw_admin');

	showchildmenu([['smsgw_admin', 'smsgw&operation=list']], $smsgwname);

	showformheader("smsgw&operation=$operation&smsgwid=$smsgwid", 'enctype');
	showhiddenfields(['referer' => $returnurl]);
	showtableheader('', 'fixpadding');

	showsetting('smsgw_edit_name', 'smsgwnew[name]', $smsgw['name'], 'text');
	showsetting('smsgw_edit_order', 'smsgwnew[order]', $smsgw['order'], 'text');
	showsetting('smsgw_edit_sendrule', 'smsgwnew[sendrule]', $smsgw['sendrule'], 'text');
	if(is_array($smsgwsetting)) {
		foreach($smsgwsetting as $settingvar => $setting) {
			if(!empty($setting['value']) && is_array($setting['value'])) {
				foreach($setting['value'] as $k => $v) {
					$setting['value'][$k][1] = lang('smsgw/'.$class, $setting['value'][$k][1]);
				}
			}
			$varname = in_array($setting['type'], ['mradio', 'mcheckbox', 'select', 'mselect']) ?
				($setting['type'] == 'mselect' ? ['parameters['.$settingvar.'][]', $setting['value']] : ['parameters['.$settingvar.']', $setting['value']])
				: 'parameters['.$settingvar.']';
			$value = $smsgw['parameters'][$settingvar] != '' ? $smsgw['parameters'][$settingvar] : $setting['default'];
			$comment = lang('smsgw/'.$class, $setting['title'].'_comment');
			$comment = $comment != $setting['title'].'_comment' ? $comment : '';
			showsetting(lang('smsgw/'.$class, $setting['title']).':', $varname, $value, $setting['type'], '', 0, $comment);
		}
	}

	showsubmit('smsgwsubmit');
	showtablefooter();
	showformfooter();

} else {

	$smsgwid = $_GET['smsgwid'];
	$smsgw = table_common_smsgw::t()->fetch($smsgwid);
	$class = $smsgw['class'];
	$smsgw['parameters'] = dunserialize($smsgw['parameters']);

	$etype = explode(':', $class);
	if(count($etype) > 1 && preg_match('/^[\w\_:]+$/', $class)) {
		include_once DISCUZ_PLUGIN($etype[0]).'/smsgw/smsgw_'.$etype[1].'.php';
		$smsgwclass = 'smsgw_'.$etype[1];
	} else {
		require_once libfile('smsgw/'.$class, 'class');
		$smsgwclass = 'smsgw_'.$class;
	}
	$smsgwclass = new $smsgwclass;

	$smsgwnew = $_GET['smsgwnew'];

	$parameters = !empty($_GET['parameters']) ? $_GET['parameters'] : [];
	$smsgwclass->setsetting($smsgwnew, $parameters);

	if(!$smsgwnew['name']) {
		cpmsg('smsgw_name_invalid', '', 'error');
	} elseif(strlen($smsgwnew['name']) > 255) {
		cpmsg('smsgw_name_more', '', 'error');
	}

	if(!$smsgwnew['sendrule']) {
		cpmsg('smsgw_sendrule_invalid', '', 'error');
	}

	table_common_smsgw::t()->update($smsgwid, [
		'name' => $smsgwnew['name'],
		'order' => (int)$smsgwnew['order'],
		'sendrule' => $smsgwnew['sendrule'],
		'parameters' => serialize($parameters),
	]);

	updatecache('setting');

	cpmsg('smsgw_succeed', 'action=smsgw&operation=edit&smsgwid='.$smsgwid, 'succeed');

}
	