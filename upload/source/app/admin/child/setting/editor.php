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
	if(isset($settingnew['smthumb'])) {
		$settingnew['smthumb'] = intval($settingnew['smthumb']) >= 20 && intval($settingnew['smthumb']) <= 40 ? intval($settingnew['smthumb']) : 20;
	}

	if(isset($settingnew['defaulteditormode']) && isset($settingnew['allowswitcheditor'])) {
		$settingnew['editoroptions'] = bindec(decbin($settingnew['defaulteditormode']).$settingnew['allowswitcheditor'].$settingnew['simplemode']);
	}

	table_forum_forum::t()->update_allowhtml($settingnew['editorfids'], 1);

	if(isset($settingnew['smcols'])) {
		$settingnew['smcols'] = $settingnew['smcols'] >= 8 && $settingnew['smcols'] <= 12 ? $settingnew['smcols'] : 8;
	}
	$settingnew['editormodetype'] = $settingnew['defaulteditormode'] == 2;

	if($settingnew['defaulteditormode'] == 2) {
		$action = 'editorblock';
		$operation = 'list';
		$_GET['anchor'] = '';
		$from = '';
	}
} else {
	shownav('style', 'setting_editor');

	showsubmenu('setting_editor', [
		['setting_editor_global', 'setting&operation=editor', 1],
		['setting_editor_code', 'misc&operation=bbcode', 0],
		['setting_editor_media', 'misc&operation=mediacode', 0],
		['setting_editor_block', 'editorblock&operation=list', 0],
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	$_G['setting']['editoroptions'] = str_pad(decbin($setting['editoroptions']), 4, 0, STR_PAD_LEFT);
	$setting['defaulteditormode'] = bindec($_G['setting']['editoroptions'][0].$_G['setting']['editoroptions'][1]);
	$setting['allowswitcheditor'] = $_G['setting']['editoroptions'][2];
	$setting['simplemode'] = $_G['setting']['editoroptions'][3];

	/*search={"setting_editor":"action=setting&operation=editor","setting_editor_global":"action=setting&operation=editor"}*/
	showtableheader();

	showtitle('setting_editor_mode_type_default');
	showsetting('setting_editor_mode_default', ['settingnew[defaulteditormode]', [
		[0, $lang['setting_editor_mode_discuzcode']],
		[1, $lang['setting_editor_mode_wysiwyg']],
		[2, $lang['setting_editor_mode_json']],
	]], $setting['defaulteditormode'], 'mradio');

	showtitle('setting_editor_mode_type_wysiwyg');
	showsetting('setting_editor_swtich_enable', 'settingnew[allowswitcheditor]', $setting['allowswitcheditor'], 'radio');
	showsetting('setting_editor_simplemode', ['settingnew[simplemode]', [
		[1, $lang['setting_editor_simplemode_1']],
		[0, $lang['setting_editor_simplemode_0']]], 1], $setting['simplemode'], 'mradio');
	showsetting('setting_editor_smthumb', 'settingnew[smthumb]', $setting['smthumb'], 'text');
	showsetting('setting_editor_smcols', 'settingnew[smcols]', $setting['smcols'], 'text');
	showsetting('setting_editor_smrows', 'settingnew[smrows]', $setting['smrows'], 'text');

	showtitle('setting_editor_mode_type_json');
	// showsetting('setting_editor_json_independence', 'settingnew[json_independence]', $setting['json_independence'], 'radio');
	showsetting('setting_editor_anchorparse', 'settingnew[anchorparse]', $setting['anchorparse'], 'textarea');
	/*search*/

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}