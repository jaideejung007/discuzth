<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($_GET['blockid'])) {
	cpmsg('undefined_action');
}

if(!submitcheck('editorblocksubmit')) {

	$blockid = $_GET['blockid'];
	$editorblock = table_common_editorblock::t()->fetch($blockid);
	if(!$editorblock) {
		cpmsg('editorblock_nonexistence', '', 'error');
	}
	$editorblock['parameters'] = dunserialize($editorblock['parameters']);
	$class = $editorblock['class'];

	if(!empty($editorblock['plugin'])) {
		include_once DISCUZ_PLUGIN($editorblock['plugin']).'/editorblock/editorblock_'.$editorblock['class'].'.php';
		$editorblockclass = 'editorblock_'.$class;
	} else {
		require_once libfile('editorblock/'.$editorblock['class'], 'class');
		$editorblockclass = 'editorblock_'.$class;
	}
	$editorblockclass = new $editorblockclass;
	$editorblocksetting = $editorblockclass->getsetting();
	$editorblockname = lang('editorblock/'.$class, $editorblockclass->name).' '.$editorblockclass->customname;
	$returnurl = 'action=editorblock&operation=list';

	$return = '<a href="'.ADMINSCRIPT.'?'.$returnurl.'">'.cplang('editorblock_admin_list').(empty($_GET['from']) ? ' - '.$editorblockname : '').'</a>';
	shownav('extended', 'editorblock_admin');
	showsubmenu($root.' &raquo; '.$return.' &raquo; '.cplang('editorblock_edit'));

	showformheader("editorblock&operation=$operation&blockid=$blockid", 'enctype');
	showhiddenfields(['referer' => $returnurl]);
	showtableheader(cplang('editorblock_edit').' - '.lang('editorblock/'.$class, $editorblockclass->name), 'fixpadding');

	showsetting('editorblock_edit_name', 'editorblocknew[name]', $editorblock['name'], 'text');
	showsetting('editorblock_edit_sort', 'editorblocknew[sort]', $editorblock['sort'], 'text');
	showsetting('editorblock_edit_sendrule', 'editorblocknew[sendrule]', $editorblock['sendrule'], 'text');
	if(is_array($editorblocksetting)) {
		foreach($editorblocksetting as $settingvar => $setting) {
			if(!empty($setting['value']) && is_array($setting['value'])) {
				foreach($setting['value'] as $k => $v) {
					$setting['value'][$k][1] = lang('editorblock/'.$class, $setting['value'][$k][1]);
				}
			}
			$varname = in_array($setting['type'], ['mradio', 'mcheckbox', 'select', 'mselect']) ?
				($setting['type'] == 'mselect' ? ['parameters['.$settingvar.'][]', $setting['value']] : ['parameters['.$settingvar.']', $setting['value']])
				: 'parameters['.$settingvar.']';
			$value = $editorblock['parameters'][$settingvar] != '' ? $editorblock['parameters'][$settingvar] : $setting['default'];
			$comment = lang('editorblock/'.$class, $setting['title'].'_comment');
			$comment = $comment != $setting['title'].'_comment' ? $comment : '';
			showsetting(lang('editorblock/'.$class, $setting['title']).':', $varname, $value, $setting['type'], '', 0, $comment);
		}
	}

	showsubmit('editorblocksubmit');
	showtablefooter();
	showformfooter();

} else {

	$blockid = $_GET['blockid'];
	$editorblock = table_common_editorblock::t()->fetch($blockid);
	$class = $editorblock['class'];
	$editorblock['parameters'] = dunserialize($editorblock['parameters']);

	if(!empty($editorblock['plugin'])) {
		include_once DISCUZ_PLUGIN($editorblock['plugin']).'/editorblock/editorblock_'.$editorblock['class'].'.php';
		$editorblockclass = 'editorblock_'.$class;
	} else {
		require_once libfile('editorblock/'.$editorblock['class'], 'class');
		$editorblockclass = 'editorblock_'.$class;
	}
	$editorblockclass = new $editorblockclass;

	$editorblocknew = $_GET['editorblocknew'];

	$parameters = !empty($_GET['parameters']) ? $_GET['parameters'] : [];
	$editorblockclass->setsetting($editorblocknew, $parameters);

	if(!$editorblocknew['name']) {
		cpmsg('editorblock_name_invalid', '', 'error');
	} elseif(strlen($editorblocknew['name']) > 255) {
		cpmsg('editorblock_name_more', '', 'error');
	}

	if(!$editorblocknew['sendrule']) {
		cpmsg('editorblock_sendrule_invalid', '', 'error');
	}

	table_common_editorblock::t()->update($blockid, [
		'name' => $editorblocknew['name'],
		'sort' => (int)$editorblocknew['sort'],
		'sendrule' => $editorblocknew['sendrule'],
		'parameters' => serialize($parameters),
	]);

	updatecache('setting');

	cpmsg('editorblock_succeed', 'action=editorblock&operation=edit&blockid='.$blockid, 'succeed');

}
	