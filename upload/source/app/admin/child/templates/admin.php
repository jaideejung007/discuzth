<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('tplsubmit')) {

	$templates = '';
	foreach(table_common_template::t()->fetch_all_data() as $tpl) {
		$basedir = basename($tpl['directory']);
		$templates .= showtablerow('', ['class="td25"', '', 'class="td29"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" ".($tpl['templateid'] == 1 ? 'disabled ' : '')."value=\"{$tpl['templateid']}\">",
			"<input type=\"text\" class=\"txt\" size=\"8\" name=\"namenew[{$tpl['templateid']}]\" value=\"{$tpl['name']}\">".
			($basedir != 'default' ? '<a href="'.ADMINSCRIPT.'?action=cloudaddons&frame=no&id='.urlencode($basedir).'.template" target="_blank" title="'.$lang['cloudaddons_linkto'].'">'.$lang['view'].'</a>' : ''),
			"<input type=\"text\" class=\"txt\" size=\"20\" name=\"directorynew[{$tpl['templateid']}]\" value=\"{$tpl['directory']}\">",
			!empty($tpl['copyright']) ?
				$tpl['copyright'] :
				"<input type=\"text\" class=\"txt\" size=\"8\" name=\"copyrightnew[{$tpl['templateid']}]\" value=>"
		], TRUE);
	}

	shownav('template', 'templates_admin');
	showsubmenu('styles_admin', [
		['templates_add', 'templates&operation=add', 0],
		['nav_templates', 'templates&operation=admin', 1],
		['cloudaddons_style_link', 'cloudaddons&frame=no&operation=templates&from=more', 0, 1],
	]);
	showformheader('templates');
	showtableheader();
	showsubtitle(['', 'templates_admin_name', 'dir', 'copyright']);
	echo $templates;
	echo '<tr><td>'.$lang['add_new'].'</td><td><input type="text" class="txt" size="8" name="newname"></td><td class="td29"><input type="text" class="txt" size="20" name="newdirectory"></td><td><input type="text" class="txt" size="25" name="newcopyright"></td><td>&nbsp;</td></tr>';
	showsubmit('tplsubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();

} else {

	if($_GET['newname']) {
		if(!$_GET['newdirectory']) {
			cpmsg('tpl_new_directory_invalid', '', 'error');
		} elseif(!istpldir($_GET['newdirectory'])) {
			$directory = $_GET['newdirectory'];
			cpmsg('tpl_directory_invalid', '', 'error', ['directory' => $directory]);
		}
		table_common_template::t()->insert(['name' => $_GET['newname'], 'directory' => $_GET['newdirectory'], 'copyright' => $_GET['newcopyright']]);
	}

	foreach($_GET['directorynew'] as $id => $directory) {
		if(!$_GET['delete'] || ($_GET['delete'] && !in_array($id, $_GET['delete']))) {
			if(!istpldir($directory)) {
				cpmsg('tpl_directory_invalid', '', 'error', ['directory' => $directory]);
			} elseif($id == 1 && $directory != './template/default') {
				cpmsg('tpl_default_directory_invalid', '', 'error');
			}
			table_common_template::t()->update($id, ['name' => $_GET['namenew'][$id], 'directory' => $_GET['directorynew'][$id]]);
			if(!empty($_GET['copyrightnew'][$id])) {
				$template = table_common_template::t()->fetch($id);
				if(!$template['copyright']) {
					table_common_template::t()->update($id, ['copyright' => $_GET['copyrightnew'][$id]]);
				}
			}
		}
	}

	if(is_array($_GET['delete'])) {
		if(in_array('1', $_GET['delete'])) {
			cpmsg('tpl_delete_invalid', '', 'error');
		}
		if($_GET['delete']) {
			table_common_template::t()->delete_tpl($_GET['delete']);
			table_common_style::t()->update($_GET['delete'], ['templateid' => 1]);
		}
	}

	updatecache('styles');
	cpmsg('tpl_update_succeed', 'action=templates', 'succeed');

}
	