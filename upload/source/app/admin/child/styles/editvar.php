<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!$isplugindeveloper) {
	cpmsg('undefined_action', '', 'error');
}

$style = table_common_style::t()->fetch_by_styleid($id);
if(!$style) {
	cpmsg('style_not_found', '', 'error');
}

shownav('template', 'styles_edit');

$stylevarid = !empty($_GET['stylevarid']) ? $_GET['stylevarid'] : 0;

if(!$stylevarid) {
	if(!submitcheck('editsubmit')) {

		showchildmenu([['styles_admin', 'styles'], [$style['name'].' ', '']], cplang('plugins_editlink'), [
			['plugins_config_vars', 'styles&operation=editvar&id='.$id, 1],
			['export', 'styles&operation=export&id='.$id, 0],
		]);

		showformheader("styles&operation=editvar&id=$id");
		showtableheader('styles_vars');
		showsubtitle(['', 'display_order', 'plugins_vars_title', 'plugins_vars_variable', 'plugins_vars_type', '']);
		foreach(table_common_stylevar_extra::t()->fetch_all_by_styleid($id) as $var) {
			$newdisplayorder = max($var['displayorder'], $newdisplayorder ?? 0);
			$var['typename'] = admin\class_component::get_name($var['type']);
			$var['title'] .= isset($lang[$var['title']]) ? '<br />'.$lang[$var['title']] : '';
			if($var['type'] == 'stylePage') {
				$trstyle = 'class="header"';
			}elseif($var['type'] == 'styleTitle') {
				$trstyle = 'class="header"';
				$var['title'] = '<div class="board">'.$var['title'].'</div>';
			}else{
				$trstyle = '';
				$var['title'] = '<div class="childboard">'.$var['title'].'</div>';
			}
			showtablerow($trstyle, ['class="td25"', 'class="td28"'], [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$var['stylevarid']}\">",
				"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayordernew[{$var['stylevarid']}]\" value=\"{$var['displayorder']}\">",
				$var['title'],
				$var['variable'],
				$var['typename'],
				"<a href=\"".ADMINSCRIPT."?action=styles&operation=editvar&id=$id&stylevarid={$var['stylevarid']}\" class=\"act\">{$lang['detail']}</a>"
			]);
		}
		showtablerow('', ['class="td25"', 'class="td28"'], [
			cplang('add_new'),
			'<input type="text" class="txt" size="2" name="newdisplayorder" value="'.($newdisplayorder + 1).'">',
			'<input type="text" class="txt" size="15" name="newtitle">',
			'<input type="text" class="txt" size="15" name="newvariable">',
			'<select name="newtype">'.admin\class_component::get_optgroup().'</seletc>',
			''
		]);
		showsubmit('editsubmit', 'submit', 'del');
		showtablefooter();
		showformfooter();

	} else {

		if($_GET['delete']) {
			table_common_stylevar_extra::t()->delete($_GET['delete']);
		}

		if(is_array($_GET['displayordernew'])) {
			foreach($_GET['displayordernew'] as $stylevarid => $displayorder) {
				table_common_stylevar_extra::t()->update($stylevarid, ['displayorder' => $displayorder]);
			}
		}

		if(table_common_stylevar_extra::t()->count_by_styleid_page($id)) {
			$first = table_common_stylevar_extra::t()->fetch_first_by_styleid($id);
			if($first['type'] != 'stylePage') {
				cpmsg('plugins_edit_stylePage_first_invalid', '', 'error');
			}
		}

		$newtitle = dhtmlspecialchars(trim($_GET['newtitle']));
		$newvariable = trim($_GET['newvariable']);
		if($newtitle && $newvariable) {
			if(strlen($newvariable) > 40 || !ispluginkey($newvariable) || table_common_stylevar_extra::t()->check_variable($id, $newvariable)) {
				cpmsg('plugins_edit_var_invalid', '', 'error');
			}
			$data = [
				'styleid' => $id,
				'displayorder' => $_GET['newdisplayorder'],
				'title' => $newtitle,
				'variable' => $newvariable,
				'type' => $_GET['newtype'],
			];
			table_common_stylevar_extra::t()->insert($data);
		}

		cpmsg('styles_edit_succeed', "action=styles&operation=editvar&id=$id", 'succeed');
	}
} else {

	$stylevar = table_common_stylevar_extra::t()->fetch($stylevarid);
	if(!$stylevar) {
		cpmsg('stylevar_not_found', '', 'error');
	}

	if(!submitcheck('varsubmit')) {

		showchildmenu([['styles_admin', 'styles'], [$style['name'].' ', ''],
			[cplang('plugins_editlink'), 'styles&operation=editvar&id='.$id]], $stylevar['title']);



		$typeselect = '<select name="typenew" onchange="if(this.value.indexOf(\'select\') != -1) $(\'extra\').style.display=\'\'; else $(\'extra\').style.display=\'none\';">';
		$typeselect .= !str_starts_with($stylevar['type'], 'style') ? admin\class_component::get_optgroup($stylevar['type']) : '<option value="'.$stylevar['type'].'" selected>'.cplang('plugins_edit_vars_type_'.$stylevar['type']).'</option>';
		$typeselect .= '</select>';

		showformheader("styles&operation=editvar&id=$id&stylevarid=$stylevarid");
		showtableheader();
		showtitle('styles_edit_vars');
		showsetting('styles_edit_vars_title', 'titlenew', $stylevar['title'], 'text');
		showsetting('styles_edit_vars_description', 'descriptionnew', $stylevar['description'], 'textarea');
		showsetting('plugins_edit_vars_type', '', '', $typeselect);
		showsetting('styles_edit_vars_variable', 'variablenew', $stylevar['variable'], 'text');
		$iscomponent = !empty($_G['cache']['admin']['component']) && isset($_G['cache']['admin']['component'][$stylevar['type']]);
		showtagheader('tbody', 'extra', $stylevar['type'] == 'select' || $stylevar['type'] == 'selects' || $iscomponent);
		showsetting('plugins_edit_vars_extra', 'extranew', $stylevar['extra'], 'textarea', comment: !$iscomponent ? '' : admin\class_component::get_desc($stylevar['type']));
		showtagfooter('tbody');
		showsubmit('varsubmit');
		showtablefooter();
		showformfooter();

	} else {

		$titlenew = cutstr(trim($_GET['titlenew']), 25);
		$descriptionnew = cutstr(trim($_GET['descriptionnew']), 255);
		$variablenew = trim($_GET['variablenew']);
		$extranew = trim($_GET['extranew']);

		if(!$titlenew) {
			cpmsg('plugins_edit_var_title_invalid', '', 'error');
		} elseif($variablenew != $stylevar['variable']) {
			if(!$variablenew || strlen($variablenew) > 40 || !ispluginkey($variablenew) || table_common_stylevar_extra::t()->check_variable($id, $variablenew)) {
				cpmsg('plugins_edit_vars_invalid', '', 'error');
			}
		}

		table_common_stylevar_extra::t()->update_by_stylevarid($id, $stylevarid, [
			'title' => $titlenew,
			'description' => $descriptionnew,
			'type' => $_GET['typenew'],
			'variable' => $variablenew,
			'extra' => $extranew
		]);

		cpmsg('styles_edit_vars_succeed', "action=styles&operation=editvar&id=$id&stylevarid=$stylevarid", 'succeed');
	}

}
	