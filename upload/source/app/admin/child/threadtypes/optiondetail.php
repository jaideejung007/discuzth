<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$option = table_forum_typeoption::t()->fetch($_GET['optionid']);
if(!$option) {
	cpmsg('typeoption_not_found', '', 'error');
}

if(!submitcheck('editsubmit')) {


	shownav('forum', 'threadtype_infotypes');
	showchildmenu([['threadtype_infotypes', 'threadtypes'], [$classids[$option['classid']], 'threadtypes&operation=typeoption&classid='.$option['classid']]], $option['title']);

	$typeselect = '<select name="typenew" onchange="var styles, key;styles=new Array(\'number\',\'text\',\'radio\', \'checkbox\', \'textarea\', \'select\', \'image\', \'calendar\', \'range\', \'info\'); for(key in styles) {var obj=$(\'style_\'+styles[key]); if(obj) { obj.style.display=styles[key]==this.options[this.selectedIndex].value?\'\':\'none\';}}">';
	foreach(['number', 'text', 'radio', 'checkbox', 'textarea', 'select', 'calendar', 'email', 'url', 'image', 'range', 'plugin'] as $type) {
		$typeselect .= '<option value="'.$type.'" '.($option['type'] == $type ? 'selected' : '').'>'.$lang['threadtype_edit_vars_type_'.$type].'</option>';
	}
	$typeselect .= '</select>';

	$option['rules'] = dunserialize($option['rules']);
	$option['protect'] = dunserialize($option['protect']);

	$groups = $forums = [];
	foreach(table_common_usergroup::t()->range() as $group) {
		$groups[] = [$group['groupid'], $group['grouptitle']];
	}
	$verifys = [];
	if($_G['setting']['verify']['enabled']) {
		foreach($_G['setting']['verify'] as $key => $verify) {
			if($verify['available'] == 1) {
				$verifys[] = [$key, $verify['title']];
			}
		}
	}

	foreach(table_common_member_profile_setting::t()->fetch_all_by_available_formtype(1, 'text') as $result) {
		$threadtype_profile = !$threadtype_profile ? "<select id='rules[text][profile]' name='rules[text][profile]'><option value=''></option>" : $threadtype_profile."<option value='{$result['fieldid']}' ".($option['rules']['profile'] == $result['fieldid'] ? "selected='selected'" : '').">{$result['title']}</option>";
	}
	$threadtype_profile .= '</select>';

	showformheader("threadtypes&operation=optiondetail&optionid={$_GET['optionid']}");
	showtableheader();
	showtitle('threadtype_infotypes_option_config');
	showsetting('name', 'titlenew', $option['title'], 'text');
	showsetting('threadtype_variable', 'identifiernew', $option['identifier'], 'text');
	showsetting('type', '', '', $typeselect);
	showsetting('threadtype_edit_desc', 'descriptionnew', $option['description'], 'textarea');
	showsetting('threadtype_unit', 'unitnew', $option['unit'], 'text');
	showsetting('threadtype_expiration', 'expirationnew', $option['expiration'], 'radio');
	if(in_array($option['type'], ['calendar', 'number', 'text', 'email', 'textarea'])) {
		showsetting('threadtype_protect', 'protectnew[status]', $option['protect']['status'], 'radio', 0, 1);
		showsetting('threadtype_protect_mode', ['protectnew[mode]', [
			[1, $lang['threadtype_protect_mode_pic']],
			[2, $lang['threadtype_protect_mode_html']]
		]], $option['protect']['mode'], 'mradio');
		showsetting('threadtype_protect_usergroup', ['protectnew[usergroup][]', $groups], explode("\t", $option['protect']['usergroup']), 'mselect');
		$verifys && showsetting('threadtype_protect_verify', ['protectnew[verify][]', $verifys], explode("\t", $option['protect']['verify']), 'mselect');
		showsetting('threadtype_protect_permprompt', 'permpromptnew', $option['permprompt'], 'textarea');
	}

	showtagheader('tbody', 'style_calendar', $option['type'] == 'calendar');
	showtitle('threadtype_edit_vars_type_calendar');
	showsetting('threadtype_edit_inputsize', 'rules[calendar][inputsize]', $option['rules']['inputsize'], 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'style_number', $option['type'] == 'number');
	showtitle('threadtype_edit_vars_type_number');
	showsetting('threadtype_edit_maxnum', 'rules[number][maxnum]', $option['rules']['maxnum'], 'text');
	showsetting('threadtype_edit_minnum', 'rules[number][minnum]', $option['rules']['minnum'], 'text');
	showsetting('threadtype_edit_inputsize', 'rules[number][inputsize]', $option['rules']['inputsize'], 'text');
	showsetting('threadtype_defaultvalue', 'rules[number][defaultvalue]', $option['rules']['defaultvalue'], 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'style_text', $option['type'] == 'text');
	showtitle('threadtype_edit_vars_type_text');
	showsetting('threadtype_edit_textmax', 'rules[text][maxlength]', $option['rules']['maxlength'], 'text');
	showsetting('threadtype_edit_inputsize', 'rules[text][inputsize]', $option['rules']['inputsize'], 'text');
	showsetting('threadtype_edit_profile', '', '', $threadtype_profile);
	showsetting('threadtype_defaultvalue', 'rules[text][defaultvalue]', $option['rules']['defaultvalue'], 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'style_textarea', $option['type'] == 'textarea');
	showtitle('threadtype_edit_vars_type_textarea');
	showsetting('threadtype_edit_textmax', 'rules[textarea][maxlength]', $option['rules']['maxlength'], 'text');
	showsetting('threadtype_edit_colsize', 'rules[textarea][colsize]', $option['rules']['colsize'], 'text');
	showsetting('threadtype_edit_rowsize', 'rules[textarea][rowsize]', $option['rules']['rowsize'], 'text');
	showsetting('threadtype_defaultvalue', 'rules[textarea][defaultvalue]', $option['rules']['defaultvalue'], 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'style_select', $option['type'] == 'select');
	showtitle('threadtype_edit_vars_type_select');
	showsetting('threadtype_edit_select_choices', 'rules[select][choices]', $option['rules']['choices'], 'textarea');
	showsetting('threadtype_edit_inputsize', 'rules[select][inputsize]', $option['rules']['inputsize'], 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'style_radio', $option['type'] == 'radio');
	showtitle('threadtype_edit_vars_type_radio');
	showsetting('threadtype_edit_choices', 'rules[radio][choices]', $option['rules']['choices'], 'textarea');
	showtagfooter('tbody');

	showtagheader('tbody', 'style_checkbox', $option['type'] == 'checkbox');
	showtitle('threadtype_edit_vars_type_checkbox');
	showsetting('threadtype_edit_choices', 'rules[checkbox][choices]', $option['rules']['choices'], 'textarea');
	showtagfooter('tbody');

	showtagheader('tbody', 'style_image', $option['type'] == 'image');
	showtitle('threadtype_edit_vars_type_image');
	showsetting('threadtype_edit_images_weight', 'rules[image][maxwidth]', $option['rules']['maxwidth'], 'text');
	showsetting('threadtype_edit_images_height', 'rules[image][maxheight]', $option['rules']['maxheight'], 'text');
	showsetting('threadtype_edit_inputsize', 'rules[image][inputsize]', $option['rules']['inputsize'], 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'style_range', $option['type'] == 'range');
	showtitle('threadtype_edit_vars_type_range');
	showsetting('threadtype_edit_maxnum', 'rules[range][maxnum]', $option['rules']['maxnum'], 'text');
	showsetting('threadtype_edit_minnum', 'rules[range][minnum]', $option['rules']['minnum'], 'text');
	showsetting('threadtype_edit_inputsize', 'rules[range][inputsize]', $option['rules']['inputsize'], 'text');
	showsetting('threadtype_edit_searchtxt', 'rules[range][searchtxt]', $option['rules']['searchtxt'], 'text');
	showtagfooter('tbody');

	threadtype_sysdata($typeSetting);
	foreach($_G['setting']['plugins']['available'] as $plugin) {
		threadtype_data($plugin, $typeSetting);
	}
	showtagheader('tbody', 'style_plugin', $option['type'] == 'plugin');
	showtitle('threadtype_edit_vars_type_plugin');
	showsetting('threadtype_edit_pluginthreadtype', ['rules[plugin][pluginthreadtype]', $typeSetting], $option['rules']['pluginthreadtype'], 'mradio');
	showsetting('threadtype_edit_pluginthreadtype_param', 'rules[plugin][pluginthreadtype_param]', $option['rules']['pluginthreadtype_param'], 'textarea', comment: $typeSetting[$option['rules']['pluginthreadtype']][2]);
	showtagfooter('tbody');

	showsubmit('editsubmit');
	showtablefooter();
	showformfooter();

} else {

	$titlenew = trim($_GET['titlenew']);
	$_GET['identifiernew'] = trim($_GET['identifiernew']);
	if(!$titlenew || !$_GET['identifiernew']) {
		cpmsg('threadtype_infotypes_option_invalid', '', 'error');
	}

	if(in_array(strtoupper($_GET['identifiernew']), $mysql_keywords)) {
		cpmsg('threadtype_infotypes_optionvariable_iskeyword', '', 'error');
	}

	if(table_forum_typeoption::t()->fetch_all_by_identifier($_GET['identifiernew'], 0, 1, $_GET['optionid']) || strlen($_GET['identifiernew']) > 40 || !ispluginkey($_GET['identifiernew'])) {
		cpmsg('threadtype_infotypes_optionvariable_invalid', '', 'error');
	}

	$_GET['protectnew']['usergroup'] = is_array($_GET['protectnew']['usergroup']) ? implode("\t", $_GET['protectnew']['usergroup']) : '';
	$_GET['protectnew']['verify'] = is_array($_GET['protectnew']['verify']) ? implode("\t", $_GET['protectnew']['verify']) : '';

	table_forum_typeoption::t()->update($_GET['optionid'], [
		'title' => $titlenew,
		'description' => $_GET['descriptionnew'],
		'identifier' => $_GET['identifiernew'],
		'type' => $_GET['typenew'],
		'unit' => $_GET['unitnew'],
		'expiration' => $_GET['expirationnew'],
		'protect' => serialize($_GET['protectnew']),
		'rules' => serialize($_GET['rules'][$_GET['typenew']]),
		'permprompt' => $_GET['permpromptnew'],
	]);

	if($_GET['identifiernew'] != $option['identifier'] && $_GET['typenew'] == $option['type']) {
		if($_GET['typenew'] == 'radio') {
			$type_tableoption_sql = "smallint(6) UNSIGNED NOT NULL DEFAULT '0'";
		} elseif(in_array($_GET['typenew'], ['number', 'range'])) {
			$type_tableoption_sql = "int(10) UNSIGNED NOT NULL DEFAULT '0'";
		} elseif($_GET['typenew'] == 'select') {
			$type_tableoption_sql = 'varchar(50) NOT NULL';
		} else {
			$type_tableoption_sql = 'mediumtext NOT NULL';
		}
		$typevar_list = DB::fetch_all('SELECT sortid FROM %t WHERE optionid=%d', ['forum_typevar', $_GET['optionid']]);
		foreach($typevar_list as $typevar) {
			table_forum_optionvalue::t()->alter($typevar['sortid'], 'change '.$option['identifier'].' '.$_GET['identifiernew'].' '.$type_tableoption_sql);
		}
	}

	updatecache('threadsorts');
	cpmsg('threadtype_infotypes_option_succeed', 'action=threadtypes&operation=typeoption&classid='.$option['classid'], 'succeed');
}