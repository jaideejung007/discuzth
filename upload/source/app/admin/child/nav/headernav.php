<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!$do) {

	if(!submitcheck('submit')) {

		shownav('style', 'nav_setting_customnav');
		showsubmenu('nav_setting_customnav', $navdata);

		showformheader('nav&operation=headernav');
		showtableheader();
		showsubtitle(['', 'display_order', 'name', 'misc_customnav_subtype', 'url', 'type', 'setindex', 'available', '']);
		showtagheader('tbody', '', true);

		$navlist = $subnavlist = $pluginsubnav = [];
		foreach(table_common_nav::t()->fetch_all_by_navtype(0) as $nav) {
			if($nav['parentid']) {
				$subnavlist[$nav['parentid']][] = $nav;
			} else {
				$navlist[$nav['id']] = $nav;
			}
		}
		foreach(table_common_plugin::t()->fetch_all_data() as $plugin) {
			if($plugin['available']) {
				$plugin['modules'] = dunserialize($plugin['modules']);
				if(is_array($plugin['modules'])) {
					unset($plugin['modules']['extra']);
					foreach($plugin['modules'] as $k => $module) {
						if(isset($module['name'])) {
							switch($module['type']) {
								case 5:
									$module['url'] = $module['url'] ? $module['url'] : 'plugin.php?id='.$plugin['identifier'].':'.$module['name'];
									list($module['menu'], $module['title']) = explode('/', $module['menu']);
									$pluginsubnav[] = ['key' => $k, 'id' => $plugin['pluginid'], 'displayorder' => $module['displayorder'], 'menu' => $module['menu'], 'title' => $module['title'], 'url' => $module['url']];
									break;
							}
						}
					}
				}
			}
		}
		foreach($navlist as $nav) {
			if($nav['available'] < 0) {
				continue;
			}
			$navsubtype = [];
			$navsubtype[$nav['subtype']] = 'selected="selected"';
			$readonly = $nav['type'] == '4' ? ' readonly="readonly"' : '';
			showtablerow('', ['class="td25"', 'class="td25"', '', '', '', ''], [
				($subnavlist[$nav['id']] || $nav['identifier'] == 6 && $nav['type'] == 0 && count($pluginsubnav) ? '<a href="javascript:;" class="right" onclick="toggle_group(\'subnav_'.$nav['id'].'\', this)">[+]</a>' : '').(in_array($nav['type'], ['2', '1']) ? "<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$nav['id']}\">" : '<input type="checkbox" class="checkbox" value="" disabled="disabled" />'),
				"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayordernew[{$nav['id']}]\" value=\"{$nav['displayorder']}\">",
				"<div><input type=\"text\" class=\"txt\" size=\"15\" name=\"namenew[{$nav['id']}]\" value=\"".dhtmlspecialchars($nav['name'])."\"$readonly>".
				($nav['identifier'] == 6 && $nav['type'] == 0 ? '' : "<a href=\"###\" onclick=\"addrowdirect=1;addrow(this, 1, {$nav['id']})\" class=\"addchildboard\">{$lang['misc_customnav_add_submenu']}</a></div>"),
				$nav['identifier'] == 6 && $nav['nav'] == 0 ? $lang['misc_customnav_subtype_menu'] : "<select name=\"subtypenew[{$nav['id']}]\"><option value=\"0\" {$navsubtype[0]}>{$lang['misc_customnav_subtype_menu']}</option><option value=\"1\" {$navsubtype[1]}>{$lang['misc_customnav_subtype_sub']}</option></select>",
				$nav['type'] == '0' || $nav['type'] == '4' || $nav['type'] == '5' ? "<span title='{$nav['url']}'>".$nav['url'].'<span>' : "<input type=\"text\" class=\"txt\" size=\"15\" name=\"urlnew[{$nav['id']}]\" value=\"".dhtmlspecialchars($nav['url'])."\">",
				cplang($nav['type'] == '0' ? 'inbuilt' : ($nav['type'] == '3' ? 'nav_plugin' : ($nav['type'] == '4' ? 'channel' : ($nav['type'] == '5' ? 'forum' : 'custom')))),
				$nav['url'] != '#' ? "<input name=\"defaultindex\" class=\"radio\" type=\"radio\" value=\"{$nav['url']}\"".($_G['setting']['defaultindex'] == $nav['url'] ? ' checked="checked"' : '').' />' : '',
				"<input class=\"checkbox\" type=\"checkbox\" name=\"availablenew[{$nav['id']}]\" value=\"1\" ".($nav['available'] > 0 ? 'checked' : '').'>',
				"<a href=\"".ADMINSCRIPT."?action=nav&operation=headernav&do=edit&id={$nav['id']}\" class=\"act\">{$lang['edit']}</a>"
			]);
			if($nav['identifier'] == 6 && $nav['type'] == 0) {
				showtagheader('tbody', 'subnav_'.$nav['id'], false);
				$subnavnum = count($pluginsubnav);
				foreach($pluginsubnav as $row) {
					$subnavnum--;
					showtablerow('', ['class="td25"', 'class="td25"', '', ''], [
						'',
						'<input type="text" class="txt" size="2" name="plugindisplayordernew['.$row['id'].']['.$row['key'].']" value="'.intval($row['displayorder']).'" />',
						'<div class="'.($subnavnum ? 'board' : 'lastboard').'"><input type="text" class="txt" size="15" name="pluginnamenew['.$row['id'].']['.$row['key'].']" value="'.dhtmlspecialchars($row['menu']).'" /></div>',
						'<input type="hidden" size="15" name="plugintitlenew['.$row['id'].']['.$row['key'].']" value="'.dhtmlspecialchars($row['title']).'" />',
						$row['url'],
						cplang('nav_plugin'),
						'',
						'<input class="checkbox" type="checkbox" checked disabled />',
						'<a href="'.ADMINSCRIPT.'?action=plugins&operation=edit&pluginid='.$row['id'].'&anchor=modules" class="act" target="_blank">'.$lang['edit'].'</a>',
					]);
				}
				showtagfooter('tbody');
			}
			if(!empty($subnavlist[$nav['id']])) {
				showtagheader('tbody', 'subnav_'.$nav['id'], false);
				$subnavnum = count($subnavlist[$nav['id']]);
				foreach($subnavlist[$nav['id']] as $sub) {
					$readonly = $sub['type'] == '4' ? ' readonly="readonly"' : '';
					$subnavnum--;
					showtablerow('', ['class="td25"', 'class="td25"', '', ''], [
						$sub['type'] == '0' || $sub['type'] == '4' ? '' : "<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$sub['id']}\">",
						"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayordernew[{$sub['id']}]\" value=\"{$sub['displayorder']}\">",
						"<div class=\"".($subnavnum ? 'board' : 'lastboard')."\"><input type=\"text\" class=\"txt\" size=\"15\" name=\"namenew[{$sub['id']}]\" value=\"".dhtmlspecialchars($sub['name'])."\"$readonly></div>",
						'',
						$sub['type'] == '0' || $sub['type'] == '4' ? "<span title='{$sub['url']}'>".$sub['url'].'</span>' : "<input type=\"text\" class=\"txt\" size=\"15\" name=\"urlnew[{$sub['id']}]\" value=\"".dhtmlspecialchars($sub['url'])."\">",
						cplang($sub['type'] == '0' ? 'inbuilt' : ($sub['type'] == '3' ? 'nav_plugin' : ($sub['type'] == '4' ? 'channel' : 'custom'))),
						$sub['url'] != '#' ? "<input name=\"defaultindex\" class=\"radio\" type=\"radio\" value=\"{$sub['url']}\"".($_G['setting']['defaultindex'] == $sub['url'] ? ' checked="checked"' : '').' />' : '',
						"<input class=\"checkbox\" type=\"checkbox\" name=\"availablenew[{$sub['id']}]\" value=\"1\" ".($sub['available'] ? 'checked' : '').'>',
						"<a href=\"".ADMINSCRIPT."?action=nav&operation=headernav&do=edit&id={$sub['id']}\" class=\"act\">{$lang['edit']}</a>"
					]);
				}
				showtagfooter('tbody');
			}
		}
		showtagfooter('tbody');
		echo '<tr><td colspan="1"></td><td colspan="8"><div><a href="###" onclick="addrow(this, 0, 0)" class="addtr">'.$lang['misc_customnav_add_menu'].'</a></div></td></tr>';
		showsubmit('submit', 'submit', 'del');
		showtablefooter();
		showformfooter();

		loaducenter();
		$ucapparray = uc_app_ls();

		$applist = '';
		if(count($ucapparray) > 1) {
			$applist = $lang['misc_customnav_add_ucenter'].'<select name="applist" onchange="app(this)"><option value=""></option>';
			foreach($ucapparray as $app) {
				if($app['appid'] != UC_APPID) {
					$applist .= "<option value=\"{$app['url']}\">{$app['name']}</option>";
				}
			}
			$applist .= '</select>';
		}
		$applist = str_replace("'", "\'", $applist);

		echo <<<EOT
<script type="text/JavaScript">
	var rowtypedata = [
		[[1, '', 'td25'], [1,'<input name="newdisplayorder[]" value="" size="3" type="text" class="txt">', 'td25'], [1, '<input name="newname[]" value="" size="15" type="text" class="txt">'],[1,'<select name="newsubtype[]"><option value="0">{$lang['misc_customnav_subtype_menu']}</option><option value="1">{$lang['misc_customnav_subtype_sub']}</option></select>'],[5, '<input name="newurl[]" value="" size="15" type="text" class="txt"> $applist <input type="hidden" name="newparentid[]" value="0" />']],
		[[1, '', 'td25'], [1,'<input name="newdisplayorder[]" value="" size="3" type="text" class="txt">', 'td25'], [1, '<div class=\"board\"><input name="newname[]" value="" size="15" type="text" class="txt"></div>'], [1,'',''], [5, '<input name="newurl[]" value="" size="15" type="text" class="txt"> $applist <input type="hidden" name="newparentid[]" value="{1}" />']]
	];
	function app(obj) {
		var inputs = obj.parentNode.parentNode.getElementsByTagName('input');
		for(var i = 0; i < inputs.length; i++) {
			if(inputs[i].name == 'newname[]') {
				inputs[i].value = obj.options[obj.options.selectedIndex].innerHTML;
			} else if(inputs[i].name == 'newurl[]') {
				inputs[i].value = obj.value;
			}
		}
	}
</script>
EOT;

	} else {

		if($ids = dimplode($_GET['delete'])) {
			table_common_nav::t()->delete_by_navtype_id(0, $_GET['delete']);
			table_common_nav::t()->delete_by_navtype_parentid(0, $_GET['delete']);
		}

		if(is_array($_GET['namenew'])) {
			foreach($_GET['namenew'] as $id => $name) {


				$name = trim(dhtmlspecialchars($name));
				$urlnew = str_replace(['&amp;'], ['&'], dhtmlspecialchars($_GET['urlnew'][$id]));
				$urladd = !empty($_GET['urlnew'][$id]) ? ", url='$urlnew'" : '';
				$availablenew[$id] = $name && (!isset($_GET['urlnew'][$id]) || $_GET['urlnew'][$id]) && $_GET['availablenew'][$id];
				$displayordernew[$id] = intval($_GET['displayordernew'][$id]);
				$data = [
					'displayorder' => $displayordernew[$id],
					'available' => $availablenew[$id],
				];
				if(!empty($_GET['urlnew'][$id])) {
					$data['url'] = $urlnew;
				}
				if(!empty($name)) {
					$data['name'] = $name;
				}
				if(isset($_GET['subtypenew'][$id])) {
					$data['subtype'] = intval($_GET['subtypenew'][$id]);
				}
				table_common_nav::t()->update($id, $data);
			}
		}

		if(is_array($_GET['pluginnamenew'])) {
			foreach($_GET['pluginnamenew'] as $id => $rows) {
				$plugin = table_common_plugin::t()->fetch($id);
				$module = dunserialize($plugin['modules']);
				foreach($rows as $key => $menunew) {
					$module[$key]['menu'] = $menunew.($_GET['plugintitlenew'][$id][$key] ? '/'.$_GET['plugintitlenew'][$id][$key] : '');
					$module[$key]['displayorder'] = $_GET['plugindisplayordernew'][$id][$key];
				}
				table_common_plugin::t()->update($id, ['modules' => serialize($module)]);
			}
		}

		if(is_array($_GET['newname'])) {
			foreach($_GET['newname'] as $k => $v) {
				$v = dhtmlspecialchars(trim($v));
				if(!empty($v)) {
					$newavailable = $v && $_GET['newurl'][$k];
					$newparentid[$k] = intval($_GET['newparentid'][$k]);
					$newdisplayorder[$k] = intval($_GET['newdisplayorder'][$k]);
					$subtype = isset($_GET['newsubtype'][$k]) ? intval($_GET['newsubtype'][$k]) : 0;
					$newurl[$k] = str_replace('&amp;', '&', dhtmlspecialchars($_GET['newurl'][$k]));
					$data = [
						'parentid' => $newparentid[$k],
						'name' => $v,
						'displayorder' => $newdisplayorder[$k],
						'subtype' => $subtype,
						'url' => $newurl[$k],
						'type' => 1,
						'available' => $newavailable,
						'navtype' => 0
					];
					table_common_nav::t()->insert($data);
				}
			}
		}

		if($_GET['defaultindex'] && $_GET['defaultindex'] != '#') {
			table_common_setting::t()->update_setting('defaultindex', $_GET['defaultindex']);
		}

		updatecache('setting');
		// 删除cache_domain.php文件
		$cache_domain_file = DISCUZ_DATA.'./sysdata/cache_domain.php';
		if(file_exists($cache_domain_file)) {
			@unlink($cache_domain_file);
		}
		cpmsg('nav_add_succeed', 'action=nav&operation=headernav', 'succeed');

	}

} elseif($do == 'edit' && ($id = $_GET['id'])) {

	$nav = table_common_nav::t()->fetch_by_id_navtype($id, 0);
	if(!$nav) {
		cpmsg('nav_not_found', '', 'error');
	}

	if(!submitcheck('editsubmit')) {

		$string = sprintf('%02d', $nav['highlight']);

		shownav('style', 'nav_setting_customnav');
		$parentselect = [['0', cplang('misc_customnav_parent_top')]];
		$parentname = '';
		foreach(table_common_nav::t()->fetch_all_by_navtype_parentid(0, 0) as $pnavs) {
			if($pnavs['id'] != $id && !($pnavs['identifier'] == 6 && $pnavs['type'] == 0)) {
				$parentselect[] = [$pnavs['id'], '&nbsp;&nbsp;'.$pnavs['name']];
				if($nav['parentid'] == $pnavs['id']) {
					$parentname = $pnavs['name'].' - ';
				}
			}
		}

		if($nav['logo']) {
			$navlogo = admin\class_attach::getUrl($nav['logo']);
			$logohtml = '<br /><label><input type="checkbox" class="checkbox" name="deletelogo" value="yes" /> '.$lang['delete'].'</label><br /><img src="'.$navlogo.'" />';
		}

		if($nav['icon']) {
			$navicon = admin\class_attach::getUrl($nav['icon']);
			$naviconhtml = '<br /><label><input type="checkbox" class="checkbox" name="deleteicon" value="yes" /> '.$lang['delete'].'</label><br /><img src="'.$navicon.'" width="40" height="40" />';
		}

		showchildmenu([['nav_setting_customnav', 'nav'], ['nav_nav_headernav', 'nav&operation=headernav']], $parentname.$nav['name']);

		showformheader("nav&operation=headernav&do=edit&id=$id", 'enctype');
		showtableheader();
		showsetting('misc_customnav_name', 'namenew', $nav['name'], 'text', $nav['type'] == '4');
		showsetting('misc_customnav_parent', ['parentidnew', $parentselect], $nav['parentid'], 'select');
		showsetting('misc_customnav_title', 'titlenew', $nav['title'], 'text');
		showsetting('misc_customnav_url', 'urlnew', $nav['url'], 'text', ($nav['type'] == '0' || $nav['type'] == '4'));
		showsetting('misc_customnav_style', ['stylenew', [cplang('misc_customnav_style_underline'), cplang('misc_customnav_style_italic'), cplang('misc_customnav_style_bold')]], $string[0], 'binmcheckbox');
		showsetting('misc_customnav_style_color', ['colornew', [
			[0, '<span style="color: '.$_G['style']['tabletext'].';">Default</span>'],
			[1, '<span style="color: Red;">Red</span>'],
			[2, '<span style="color: Orange;">Orange</span>'],
			[3, '<span style="color: Yellow;">Yellow</span>'],
			[4, '<span style="color: Green;">Green</span>'],
			[5, '<span style="color: Cyan;">Cyan</span>'],
			[6, '<span style="color: Blue;">Blue</span>'],
			[7, '<span style="color: Purple;">Purple</span>'],
			[8, '<span style="color: Gray;">Gray</span>'],
		]], $string[1], 'mradio2');
		showsetting('misc_customnav_url_open', ['targetnew', [
			[0, cplang('misc_customnav_url_open_default')],
			[1, cplang('misc_customnav_url_open_blank')]
		], TRUE], $nav['target'], 'mradio');
		if(!$nav['parentid']) {
			showsetting('misc_customnav_logo', 'logonew', $nav['logo'], 'filetext', '', 0, cplang('misc_customnav_logo_comment').$logohtml);
			showsetting('misc_customnav_level', ['levelnew', [
				[0, cplang('nolimit')],
				[1, cplang('member')],
				[2, cplang('usergroups_system_3')],
				[3, cplang('usergroups_system_1')],
			]], $nav['level'], 'select');
			showsetting('misc_customnav_subtype', ['subtypenew', [
				[0, cplang('misc_customnav_subtype_menu'), ['subcols' => 'none']],
				[1, cplang('misc_customnav_subtype_sub'), ['subcols' => '']],
			]], $nav['subtype'], 'mradio');
			showtagheader('tbody', 'subcols', $nav['subtype'], 'sub');
			showsetting('misc_customnav_subcols', 'subcolsnew', $nav['subcols'], 'text');
			showtagfooter('tbody');
		}
		showsetting('misc_customnav_icon', 'iconnew', $nav['icon'], 'filetext', '', 0, cplang('misc_mynav_icon_comment').$naviconhtml);
		showsubmit('editsubmit');
		showtablefooter();
		showformfooter();

	} else {

		$namenew = trim(dhtmlspecialchars($_GET['namenew']));
		$titlenew = trim(dhtmlspecialchars($_GET['titlenew']));
		$urlnew = str_replace(['&amp;'], ['&'], dhtmlspecialchars($_GET['urlnew']));
		$colornew = $_GET['colornew'];
		$parentidnew = $_GET['parentidnew'];
		$subtypenew = $_GET['subtypenew'];
		$stylebin = '';
		for($i = 3; $i >= 1; $i--) {
			$stylebin .= empty($_GET['stylenew'][$i]) ? '0' : '1';
		}
		$stylenew = bindec($stylebin);
		$targetnew = intval($_GET['targetnew']) ? 1 : 0;
		$levelnew = intval($_GET['levelnew']) && $_GET['levelnew'] > 0 && $_GET['levelnew'] < 4 ? intval($_GET['levelnew']) : 0;

		$urladd = $nav['type'] != '0' && $urlnew ? ", url='".$urlnew."'" : '';
		$subcols = ", subcols='".intval($_GET['subcolsnew'])."'";

		$logonew = addslashes($nav['logo']);
		if($_FILES['logonew']) {
			$logonew = admin\class_attach::upload($_FILES['logonew']);
		} else {
			$logonew = $_GET['logonew'];
		}
		if($_GET['deletelogo'] && $nav['logo']) {
			admin\class_attach::delete($nav['logo']);
			$logonew = '';
		}
		$logoadd = ", logo='$logonew'";


		$iconnew = addslashes($nav['icon']);
		if($_FILES['iconnew']) {
			$iconnew = admin\class_attach::upload($_FILES['iconnew']);
		} else {
			$iconnew = $_GET['iconnew'];
		}
		if($_GET['deleteicon'] && $nav['icon']) {
			admin\class_attach::delete($nav['icon']);
			$iconnew = '';
		}
		$iconadd = ", icon='$iconnew'";
		

		$data = [
			'name' => $namenew,
			'parentid' => $parentidnew,
			'title' => $titlenew,
			'highlight' => "$stylenew$colornew",
			'target' => $targetnew,
			'level' => $levelnew,
			'subtype' => $subtypenew,
			'subcols' => intval($_GET['subcolsnew']),
			'logo' => $logonew,
			'icon' => $iconnew
		];
		if($nav['type'] != '0' && $urlnew) {
			$data['url'] = $urlnew;
		}
		table_common_nav::t()->update($id, $data);

		updatecache('setting');
		cpmsg('nav_add_succeed', 'action=nav&operation=headernav', 'succeed');

	}

}
	