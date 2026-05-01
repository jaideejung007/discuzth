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

if(empty($pluginid)) {
	$pluginlist = '<select name="pluginid">';
	foreach(table_common_plugin::t()->fetch_all_data() as $plugin) {
		$pluginlist .= '<option value="'.$plugin['pluginid'].'">'.$plugin['name'].'</option>';
	}
	$pluginlist .= '</select>';
	$highlight = getgpc('highlight');
	$highlight = !empty($highlight) ? dhtmlspecialchars($highlight, ENT_QUOTES) : '';
	cpmsg('plugins_nonexistence', 'action=plugins&operation=edit'.(!empty($highlight) ? "&highlight=$highlight" : ''), 'form', $pluginlist);
} else {
	$condition = !empty($uid) ? "uid='$uid'" : "username='$username'";
}

$plugin = table_common_plugin::t()->fetch($pluginid);
if(!$plugin) {
	cpmsg('plugin_not_found', '', 'error');
}

$plugin['modules'] = dunserialize($plugin['modules']);

if($plugin['modules']['system']) {
	cpmsg('plugin_donot_edit', '', 'error');
}

if(!submitcheck('editsubmit')) {

	$adminidselect = [$plugin['adminid'] => 'selected'];

	shownav('plugin');
	$anchor = in_array($_GET['anchor'], ['config', 'modules', 'vars']) ? $_GET['anchor'] : 'config';
	showchildmenu([['nav_plugins', 'plugins'], [$plugin['name'].($plugin['available'] ? cplang('plugins_edit_available') : ' '), '']], cplang('plugins_editlink'), [
		['config', 'config', $anchor == 'config'],
		['plugins_config_module', 'modules', $anchor == 'modules'],
		['plugins_config_vars', 'vars', $anchor == 'vars'],
		['export', 'plugins&operation=export&pluginid='.$plugin['pluginid'], 0, 1],
	], '', true);

	showtips('plugins_edit_tips');

	showtagheader('div', 'config', $anchor == 'config');
	showformheader("plugins&operation=edit&type=common&pluginid=$pluginid", '', 'configform');
	showtableheader();
	showsetting('plugins_edit_name', 'namenew', $plugin['name'], 'text');
	showsetting('plugins_edit_version', 'versionnew', $plugin['version'], 'text');
	if(!$plugin['copyright']) {
		showsetting('plugins_edit_copyright', 'copyrightnew', $plugin['copyright'], 'text');
	}
	showsetting('plugins_edit_identifier', 'identifiernew', $plugin['identifier'], 'text');
	showsetting('plugins_edit_directory', 'directorynew', $plugin['directory'], 'text');
	showsetting('plugins_edit_description', 'descriptionnew', $plugin['description'], 'textarea');
	showsetting('plugins_edit_langexists', 'langexists', $plugin['modules']['extra']['langexists'], 'radio');
	showsubmit('editsubmit');
	showtablefooter();
	showformfooter();
	showtagfooter('div');

	showtagheader('div', 'modules', $anchor == 'modules');
	showformheader("plugins&operation=edit&type=modules&pluginid=$pluginid", '', 'modulesform');
	showtableheader('plugins_edit_modules');
	showsubtitle(['', 'plugins_edit_modules_type', 'plugins_edit_modules_name', 'plugins_edit_modules_menu', 'plugins_edit_modules_menu_url', 'plugins_edit_modules_adminid', 'display_order']);

	$moduleids = [];
	if(is_array($plugin['modules'])) {
		foreach($plugin['modules'] as $moduleid => $module) {
			if($moduleid === 'extra' || $moduleid === 'system' || !isset($module['type'])) {
				continue;
			}
			$module = dhtmlspecialchars($module);
			$adminidselect = [$module['adminid'] => 'selected'];
			$includecheck = empty($val['include']) ? $lang['no'] : $lang['yes'];

			$typeselect = '<optgroup label="'.cplang('plugins_edit_modules_type_g1').'">'.
				'<option h="1100100" e="inc" value="1"'.($module['type'] == 1 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_1').'</option>'.
				'<option h="1111" e="inc" value="5"'.($module['type'] == 5 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_5').'</option>'.
				'<option h="1100100" e="inc" value="27"'.($module['type'] == 27 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_27').'</option>'.
				'<option h="1100100" e="inc" value="23"'.($module['type'] == 23 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_23').'</option>'.
				'<option h="1100110" e="inc" value="25"'.($module['type'] == 25 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_25').'</option>'.
				'<option h="1100111" e="inc" value="24"'.($module['type'] == 24 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_24').'</option>'.
				'<option h="1100000" e="inc" value="30"'.($module['type'] == 30 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_30').'</option>'.
				'</optgroup>'.
				'<optgroup label="'.cplang('plugins_edit_modules_type_g3').'">'.
				'<option h="1111" e="inc" value="7"'.($module['type'] == 7 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_7').'</option>'.
				'<option h="1111" e="inc" value="17"'.($module['type'] == 17 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_17').'</option>'.
				'<option h="1111" e="inc" value="19"'.($module['type'] == 19 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_19').'</option>'.
				'<option h="1001" e="inc" value="14"'.($module['type'] == 14 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_14').'</option>'.
				'<option h="1111" e="inc" value="26"'.($module['type'] == 26 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_26').'</option>'.
				'<option h="1111" e="inc" value="21"'.($module['type'] == 21 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_21').'</option>'.
				'<option h="1001" e="inc" value="15"'.($module['type'] == 15 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_15').'</option>'.
				'<option h="1001" e="inc" value="16"'.($module['type'] == 16 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_16').'</option>'.
				'<option h="1001" e="inc" value="3"'.($module['type'] == 3 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_3').'</option>'.
				'<option h="1100" e="inc" value="29"'.($module['type'] == 29 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_29').'</option>'.
				'</optgroup>'.
				'<optgroup label="'.cplang('plugins_edit_modules_type_g2').'">'.
				'<option h="0011" e="class" value="11"'.($module['type'] == 11 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_11').'</option>'.
				'<option h="0011" e="class" value="28"'.($module['type'] == 28 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_28').'</option>'.
				'<option h="0001" e="class" value="12"'.($module['type'] == 12 ? ' selected="selected"' : '').'>'.cplang('plugins_edit_modules_type_12').'</option>'.
				'</optgroup>';
			showtablerow('', ['class="td25"', 'class="td28"'], [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[$moduleid]\">",
				"<select id=\"s_$moduleid\" onchange=\"shide(this, '$moduleid')\" name=\"typenew[$moduleid]\">$typeselect</select>".
				' <a href="javascript:;" onclick="window.open(\''.ADMINSCRIPT.'?action=plugins&mod=attachment&operation=sample&pluginid='.$pluginid.'&frame=no&typeid=\'+$(\'s_'.$moduleid.'\').value+\'&module=\'+$(\'en_'.$moduleid.'\').value+\'&fn=\'+$(\'e_'.$moduleid.'\').innerHTML)">'.cplang('plugins_module_sample').'</a>',
				"<input type=\"text\" class=\"txt\" size=\"15\" id=\"en_$moduleid\" name=\"namenew[$moduleid]\" value=\"{$module['name']}\"><span id=\"e_$moduleid\"></span>",
				"<span id=\"m_$moduleid\"><input type=\"text\" class=\"txt\" size=\"15\" name=\"menunew[$moduleid]\" value=\"{$module['menu']}\"></span>",
				"<span id=\"u_$moduleid\"><input type=\"text\" class=\"txt\" size=\"15\" id=\"url_$moduleid\" onchange=\"shide($('s_$moduleid'), '$moduleid')\" name=\"urlnew[$moduleid]\" value=\"".dhtmlspecialchars($module['url'])."\"></span>",
				"<span id=\"a_$moduleid\"><select name=\"adminidnew[$moduleid]\">\n".
				"<option value=\"0\" $adminidselect[0]>{$lang['usergroups_system_0']}</option>\n".
				"<option value=\"1\" $adminidselect[1]>{$lang['usergroups_system_1']}</option>\n".
				"<option value=\"2\" $adminidselect[2]>{$lang['usergroups_system_2']}</option>\n".
				"<option value=\"3\" $adminidselect[3]>{$lang['usergroups_system_3']}</option>\n".
				'</select></span>',
				"<span id=\"o_$moduleid\"><input type=\"text\" class=\"txt\" style=\"width:50px\" name=\"ordernew[$moduleid]\" value=\"{$module['displayorder']}\"></span>"
			]);
			showtagheader('tbody', 'n_'.$moduleid);
			showtablerow('class="noborder"', ['', 'colspan="6"'], [
				'',
				'&nbsp;&nbsp;&nbsp;<span id="nt_'.$moduleid.'">'.$lang['plugins_edit_modules_navtitle'].':<input type="text" class="txt" size="15" name="navtitlenew['.$moduleid.']" value="'.$module['navtitle'].'"></span>
					<span id="ni_'.$moduleid.'">'.$lang['plugins_edit_modules_navicon'].':<input type="text" class="txt" name="naviconnew['.$moduleid.']" value="'.$module['navicon'].'"></span>
					<span id="nsn_'.$moduleid.'">'.$lang['plugins_edit_modules_navsubname'].':<input type="text" class="txt" name="navsubnamenew['.$moduleid.']" value="'.$module['navsubname'].'"></span>
					<span id="nsu_'.$moduleid.'">'.$lang['plugins_edit_modules_navsuburl'].':<input type="text" class="txt" name="navsuburlnew['.$moduleid.']" value="'.$module['navsuburl'].'"></span>
					',
			]);
			showtagfooter('tbody');
			showtagheader('tbody', 'n2_'.$moduleid);
			showtablerow('class="noborder"', ['', 'colspan="6"'], [
				'',
				'&nbsp;&nbsp;&nbsp;<span id="nsp_'.$moduleid.'">'.$lang['plugins_edit_modules_param'].':<input type="text" class="txt" name="paramnew['.$moduleid.']" value="'.$module['param'].'"></span>',
			]);
			showtagfooter('tbody');

			$moduleids[] = $moduleid;
		}
	}
	showtablerow('', ['class="td25"', 'class="td28"'], [
		cplang('add_new'),
		'<select id="s_n" onchange="shide(this, \'n\')" name="newtype">'.
		'<optgroup label="'.cplang('plugins_edit_modules_type_g1').'">'.
		'<option h="1100100" e="inc" value="1">'.cplang('plugins_edit_modules_type_1').'</option>'.
		'<option h="1111" e="inc" value="5">'.cplang('plugins_edit_modules_type_5').'</option>'.
		'<option h="1100100" e="inc" value="27">'.cplang('plugins_edit_modules_type_27').'</option>'.
		'<option h="1100100" e="inc" value="23">'.cplang('plugins_edit_modules_type_23').'</option>'.
		'<option h="1100110" e="inc" value="25">'.cplang('plugins_edit_modules_type_25').'</option>'.
		'<option h="1100111" e="inc" value="24">'.cplang('plugins_edit_modules_type_24').'</option>'.
		'<option h="1100000" e="inc" value="30">'.cplang('plugins_edit_modules_type_30').'</option>'.
		'</optgroup>'.
		'<optgroup label="'.cplang('plugins_edit_modules_type_g3').'">'.
		'<option h="1111" e="inc" value="7">'.cplang('plugins_edit_modules_type_7').'</option>'.
		'<option h="1111" e="inc" value="17">'.cplang('plugins_edit_modules_type_17').'</option>'.
		'<option h="1111" e="inc" value="19">'.cplang('plugins_edit_modules_type_19').'</option>'.
		'<option h="1001" e="inc" value="14">'.cplang('plugins_edit_modules_type_14').'</option>'.
		'<option h="1001" e="inc" value="26">'.cplang('plugins_edit_modules_type_26').'</option>'.
		'<option h="1001" e="inc" value="21">'.cplang('plugins_edit_modules_type_21').'</option>'.
		'<option h="1001" e="inc" value="15">'.cplang('plugins_edit_modules_type_15').'</option>'.
		'<option h="1001" e="inc" value="16">'.cplang('plugins_edit_modules_type_16').'</option>'.
		'<option h="1101" e="inc" value="3">'.cplang('plugins_edit_modules_type_3').'</option>'.
		'<option h="1100" e="inc" value="29">'.cplang('plugins_edit_modules_type_29').'</option>'.
		'</optgroup>'.
		'<optgroup label="'.cplang('plugins_edit_modules_type_g2').'">'.
		'<option h="0011" e="class" value="11">'.cplang('plugins_edit_modules_type_11').'</option>'.
		'<option h="0011" e="class" value="28">'.cplang('plugins_edit_modules_type_28').'</option>'.
		'<option h="0001" e="class" value="12">'.cplang('plugins_edit_modules_type_12').'</option>'.
		'</optgroup>'.
		'</select>',
		'<input type="text" class="txt" size="15" name="newname"><span id="e_n"></span>',
		'<span id="m_n"><input type="text" class="txt" size="15" name="newmenu"></span>',
		'<span id="u_n"><input type="text" class="txt" size="15" id="url_n" onchange="shide($(\'s_n\'), \'n\')" name="newurl"></span>',
		'<span id="a_n"><select name="newadminid">'.
		'<option value="0" selected>'.cplang('usergroups_system_0').'</option>'.
		'<option value="1">'.cplang('usergroups_system_1').'</option>'.
		'<option value="2">'.cplang('usergroups_system_2').'</option>'.
		'<option value="3">'.cplang('usergroups_system_3').'</option>'.
		'</select></span>',
		'<span id="o_n"><input type="text" class="txt" style="width:50px"  name="neworder"></span>',
	]);
	showtagheader('tbody', 'n_n');
	showtablerow('class="noborder"', ['', 'colspan="7"'], [
		'',
		'&nbsp;&nbsp;&nbsp;<span id="nt_n">'.$lang['plugins_edit_modules_navtitle'].':<input type="text" class="txt" name="newnavtitle"></span>
			<span id="ni_n">'.$lang['plugins_edit_modules_navicon'].':<input type="text" class="txt" name="newnavicon"></span>
			<span id="nsn_n">'.$lang['plugins_edit_modules_navsubname'].':<input type="text" class="txt" name="newnavsubname"></span>
			<span id="nsu_n">'.$lang['plugins_edit_modules_navsuburl'].':<input type="text" class="txt" name="newnavsuburl"></span>
			',
	]);
	showtagfooter('tbody');
	showtagheader('tbody', 'n2_n');
	showtablerow('class="noborder"', ['', 'colspan="6"'], [
		'',
		'&nbsp;&nbsp;&nbsp;<span id="nsp_n">'.$lang['plugins_edit_modules_param'].':<input type="text" class="txt" name="newparam"></span>',
	]);
	showtagfooter('tbody');
	showsubmit('editsubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();
	showtagfooter('div');
	$shideinit = '';
	foreach($moduleids as $moduleid) {
		$shideinit .= 'shide($("s_'.$moduleid.'"), \''.$moduleid.'\');';
	}
	echo '<script type="text/JavaScript">
			function shide(obj, id) {
				v = obj.options[obj.selectedIndex].getAttribute("h");
				$("m_" + id).style.display = v.substr(0,1) == "1" ? "" : "none";
				$("u_" + id).style.display = v.substr(1,1) == "1" ? "" : "none";
				$("a_" + id).style.display = v.substr(2,1) == "1" ? "" : "none";
				$("o_" + id).style.display = v.substr(3,1) == "1" ? "" : "none";
				if(v.substr(4,1)) {
					$("n_" + id).style.display = v.substr(4,1) == "1" ? "" : "none";
					$("nt_" + id).style.display = v.substr(4,1) == "1" ? "" : "none";
					$("ni_" + id).style.display = v.substr(5,1) == "1" ? "" : "none";
					$("nsn_" + id).style.display = v.substr(6,1) == "1" ? "" : "none";
					$("nsu_" + id).style.display = v.substr(6,1) == "1" ? "" : "none";
				} else {
					$("n_" + id).style.display = "none";
				}
				if(obj.value == 3) {
					$("n2_" + id).style.display = "";
					$("nsp_" + id).style.display = "";
				} else {
					$("n2_" + id).style.display = "none";
					$("nsp_" + id).style.display = "none";
				}
				e = obj.options[obj.selectedIndex].getAttribute("e");
				$("e_" + id).innerHTML = e && ($("url_" + id).value == \'\' || $("u_" + id).style.display == "none") ? "." + e + ".php" : "";
			}
			shide($("s_n"), "n");'.$shideinit.'
		</script>';

	showtagheader('div', 'vars', $anchor == 'vars');
	showformheader("plugins&operation=edit&type=vars&pluginid=$pluginid", '', 'varsform');
	showtableheader('plugins_edit_vars');
	showsubtitle(['', 'display_order', 'plugins_vars_title', 'plugins_vars_variable', 'plugins_vars_type', '']);
	foreach(table_common_pluginvar::t()->fetch_all_by_pluginid($plugin['pluginid']) as $var) {
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
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$var['pluginvarid']}\">",
			"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayordernew[{$var['pluginvarid']}]\" value=\"{$var['displayorder']}\">",
			$var['title'],
			$var['variable'],
			$var['typename'],
			"<a href=\"".ADMINSCRIPT."?action=plugins&operation=vars&pluginid={$plugin['pluginid']}&pluginvarid={$var['pluginvarid']}\" class=\"act\">{$lang['detail']}</a>"
		]);
	}
	showtablerow('', ['class="td25"', 'class="td28"'], [
		cplang('add_new'),
		'<input type="text" class="txt" size="2" name="newdisplayorder" value="0">',
		'<input type="text" class="txt" size="15" name="newtitle">',
		'<input type="text" class="txt" size="15" name="newvariable">',
		'<select name="newtype">'.admin\class_component::get_optgroup().'</seletc>',
		''
	]);
	showsubmit('editsubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();
	showtagfooter('div');

} else {

	$type = $_GET['type'];
	$anchor = $_GET['anchor'];
	if($type == 'common') {

		$namenew = dhtmlspecialchars(trim($_GET['namenew']));
		$versionnew = strip_tags(trim($_GET['versionnew']));
		$directorynew = dhtmlspecialchars($_GET['directorynew']);
		$identifiernew = trim($_GET['identifiernew']);
		$descriptionnew = dhtmlspecialchars($_GET['descriptionnew']);
		$copyrightnew = $plugin['copyright'] ? addslashes($plugin['copyright']) : dhtmlspecialchars($_GET['copyrightnew']);
		$adminidnew = ($_GET['adminidnew'] > 0 && $_GET['adminidnew'] <= 3) ? $_GET['adminidnew'] : 1;

		if(!$namenew) {
			cpmsg('plugins_edit_name_invalid', '', 'error');
		} elseif(!isplugindir($directorynew)) {
			cpmsg('plugins_edit_directory_invalid', '', 'error');
		} elseif($identifiernew != $plugin['identifier']) {
			$plugin = table_common_plugin::t()->fetch_by_identifier($identifiernew);
			if($plugin || !ispluginkey($identifiernew)) {
				cpmsg('plugins_edit_identifier_invalid', '', 'error');
			}
		}
		if($_GET['langexists'] && !file_exists($langfile = DISCUZ_DATA.'./plugindata/'.$identifiernew.'.lang.php')) {
			cpmsg('plugins_edit_language_invalid', '', 'error', ['langfile' => $langfile]);
		}
		$plugin['modules']['extra']['langexists'] = $_GET['langexists'];
		table_common_plugin::t()->update($pluginid, [
			'adminid' => $adminidnew,
			'version' => $versionnew,
			'name' => $namenew,
			'modules' => serialize($plugin['modules']),
			'identifier' => $identifiernew,
			'description' => $descriptionnew,
			'directory' => $directorynew,
			'copyright' => $copyrightnew
		]);

	} elseif($type == 'modules') {

		$modulesnew = [];
		$newname = trim($_GET['newname']);
		$updatenav = false;
		if(is_array($plugin['modules'])) {
			foreach($plugin['modules'] as $moduleid => $module) {
				if(!isset($_GET['delete'][$moduleid])) {
					if($moduleid === 'extra' || $moduleid === 'system' || !isset($module['type'])) {
						continue;
					}
					$modulesnew[] = [
						'name' => $_GET['namenew'][$moduleid],
						'param' => $_GET['paramnew'][$moduleid],
						'menu' => $_GET['menunew'][$moduleid],
						'url' => $_GET['urlnew'][$moduleid],
						'type' => $_GET['typenew'][$moduleid],
						'adminid' => ($_GET['adminidnew'][$moduleid] >= 0 && $_GET['adminidnew'][$moduleid] <= 3) ? $_GET['adminidnew'][$moduleid] : $module['adminid'],
						'displayorder' => intval($_GET['ordernew'][$moduleid]),
						'navtitle' => $_GET['navtitlenew'][$moduleid],
						'navicon' => $_GET['naviconnew'][$moduleid],
						'navsubname' => $_GET['navsubnamenew'][$moduleid],
						'navsuburl' => $_GET['navsuburlnew'][$moduleid],
					];
					if(in_array($_GET['typenew'][$moduleid], [1, 23, 24, 25])) {
						$updatenav = true;
					}
				} elseif(in_array($_GET['typenew'][$moduleid], [1, 23, 24, 25])) {
					$updatenav = true;
				}
			}
		}

		if($updatenav) {
			table_common_nav::t()->delete_by_type_identifier(3, $plugin['identifier']);
		}

		$modulenew = [];
		if(!empty($_GET['newname'])) {
			$modulesnew[] = [
				'name' => $_GET['newname'],
				'param' => $_GET['newparam'],
				'menu' => $_GET['newmenu'],
				'url' => $_GET['newurl'],
				'type' => $_GET['newtype'],
				'adminid' => $_GET['newadminid'],
				'displayorder' => intval($_GET['neworder']),
				'navtitle' => $_GET['newnavtitle'],
				'navicon' => $_GET['newnavicon'],
				'navsubname' => $_GET['newnavsubname'],
				'navsuburl' => $_GET['newnavsuburl'],
			];
		}

		usort($modulesnew, 'modulecmp');

		$namesarray = [];
		foreach($modulesnew as $key => $module) {
			$namekey = in_array($module['type'], [11, 12]) ? 1 : 0;
			if(!ispluginkey($module['name'])) {
				cpmsg('plugins_edit_modules_name_invalid', '', 'error');
			} elseif(is_array($namesarray[$namekey]) && in_array($module['name'].'?'.$module['param'], $namesarray[$namekey])) {
				cpmsg('plugins_edit_modules_duplicated', '', 'error');
			}
			$namesarray[$namekey][] = $module['name'].'?'.$module['param'];

			$module['menu'] = trim($module['menu']);
			$module['url'] = trim($module['url']);
			$module['adminid'] = $module['adminid'] >= 0 && $module['adminid'] <= 3 ? $module['adminid'] : 1;

			$modulesnew[$key] = $module;
		}
		if(!empty($plugin['modules']['extra'])) {
			$modulesnew['extra'] = $plugin['modules']['extra'];
		}

		if(!empty($plugin['modules']['system'])) {
			$modulesnew['system'] = $plugin['modules']['system'];
		}

		table_common_plugin::t()->update($pluginid, ['modules' => serialize($modulesnew)]);

	} elseif($type == 'vars') {

		if($_GET['delete']) {
			table_common_pluginvar::t()->delete($_GET['delete']);
		}

		if(is_array($_GET['displayordernew'])) {
			foreach($_GET['displayordernew'] as $id => $displayorder) {
				table_common_pluginvar::t()->update($id, ['displayorder' => $displayorder]);
			}
		}

		if(table_common_pluginvar::t()->count_by_pluginid_page($pluginid)) {
			$first = table_common_pluginvar::t()->fetch_first_by_pluginid($pluginid);
			if($first['type'] != 'stylePage') {
				cpmsg('plugins_edit_stylePage_first_invalid', '', 'error');
			}
		}

		$newtitle = dhtmlspecialchars(trim($_GET['newtitle']));
		$newvariable = trim($_GET['newvariable']);
		if($newtitle && $newvariable) {
			if(strlen($newvariable) > 40 || !ispluginkey($newvariable) || table_common_pluginvar::t()->check_variable($pluginid, $newvariable)) {
				cpmsg('plugins_edit_var_invalid', '', 'error');
			}
			$data = [
				'pluginid' => $pluginid,
				'displayorder' => $_GET['newdisplayorder'],
				'title' => $newtitle,
				'variable' => $newvariable,
				'type' => $_GET['newtype'],
			];
			table_common_pluginvar::t()->insert($data);
		}

	}

	updatecache(['plugin', 'setting', 'styles']);
	cleartemplatecache();
	updatemenu('plugin');
	cpmsg('plugins_edit_succeed', "action=plugins&operation=edit&pluginid=$pluginid&anchor=$anchor", 'succeed');

}
	