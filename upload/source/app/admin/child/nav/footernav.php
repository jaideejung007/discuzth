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

		showformheader('nav&operation=footernav');
		showtableheader();
		showsubtitle(['', 'display_order', 'name', 'url', 'type', 'available', '']);

		$navlist = [];
		foreach(table_common_nav::t()->fetch_all_by_navtype(1) as $nav) {
			$navlist[$nav['id']] = $nav;
		}

		foreach($navlist as $nav) {
			showtablerow('', ['class="td25"', 'class="td25"', '', ''], [
				in_array($nav['type'], ['2', '1']) ? "<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$nav['id']}\">" : '<input type="checkbox" class="checkbox" value="" disabled="disabled" />',
				"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayordernew[{$nav['id']}]\" value=\"{$nav['displayorder']}\">",
				"<div><input type=\"text\" class=\"txt\" size=\"15\" name=\"namenew[{$nav['id']}]\" value=\"".dhtmlspecialchars($nav['name'])."\">",
				$nav['type'] == '0' ? $nav['url'] : "<input type=\"text\" class=\"txt\" size=\"15\" name=\"urlnew[{$nav['id']}]\" value=\"".dhtmlspecialchars($nav['url'])."\">",
				cplang($nav['type'] == '0' ? 'inbuilt' : ($nav['type'] == '3' ? 'nav_plugin' : ($nav['type'] == '4' ? 'channel' : 'custom'))),
				"<input class=\"checkbox\" type=\"checkbox\" name=\"availablenew[{$nav['id']}]\" value=\"1\" ".($nav['available'] ? 'checked' : '').'>',
				"<a href=\"".ADMINSCRIPT."?action=nav&operation=footernav&do=edit&id={$nav['id']}\" class=\"act\">{$lang['edit']}</a>"
			]);
		}
		echo '<tr><td colspan="1"></td><td colspan="7"><div><a href="###" onclick="addrow(this, 0, 0)" class="addtr">'.$lang['nav_footernav_add'].'</a></div></td></tr>';
		showsubmit('submit', 'submit', 'del');
		showtablefooter();
		showformfooter();

		echo <<<EOT
<script type="text/JavaScript">
	var rowtypedata = [
		[[1, '', 'td25'], [1,'<input name="newdisplayorder[]" value="" size="3" type="text" class="txt">', 'td25'], [1, '<input name="newname[]" value="" size="15" type="text" class="txt">'], [4, '<input name="newurl[]" value="" size="15" type="text" class="txt">']],
	];
</script>
EOT;

	} else {

		if($ids = dimplode($_GET['delete'])) {
			table_common_nav::t()->delete_by_navtype_id(1, $_GET['delete']);
		}

		if(is_array($_GET['namenew'])) {
			foreach($_GET['namenew'] as $id => $name) {
				$name = trim(dhtmlspecialchars($name));
				$urlnew = str_replace(['&amp;'], ['&'], dhtmlspecialchars($_GET['urlnew'][$id]));
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
				table_common_nav::t()->update($id, $data);
			}
		}

		if(is_array($_GET['newname'])) {
			foreach($_GET['newname'] as $k => $v) {
				$v = dhtmlspecialchars(trim($v));
				if(!empty($v)) {
					$newavailable = $v && $_GET['newurl'][$k];
					$newdisplayorder[$k] = intval($_GET['newdisplayorder'][$k]);
					$newurl[$k] = str_replace('&amp;', '&', dhtmlspecialchars($_GET['newurl'][$k]));
					$data = [
						'name' => $v,
						'displayorder' => $newdisplayorder[$k],
						'url' => $newurl[$k],
						'type' => 1,
						'available' => $newavailable,
						'navtype' => 1
					];
					table_common_nav::t()->insert($data);
				}
			}
		}

		updatecache('setting');
		cpmsg('nav_add_succeed', 'action=nav&operation=footernav', 'succeed');

	}

} elseif($do == 'edit' && ($id = $_GET['id'])) {

	$nav = table_common_nav::t()->fetch_by_id_navtype($id, 1);
	if(!$nav) {
		cpmsg('nav_not_found', '', 'error');
	}

	if(!submitcheck('editsubmit')) {

		$string = sprintf('%02d', $nav['highlight']);

		shownav('style', 'nav_setting_customnav');
		showchildmenu([['nav_setting_customnav', 'nav'], ['nav_nav_footernav', 'nav&operation=footernav']], $nav['name']);

		showformheader("nav&operation=footernav&do=edit&id=$id");
		showtableheader();
		showsetting('misc_customnav_name', 'namenew', $nav['name'], 'text');
		showsetting('misc_customnav_title', 'titlenew', $nav['title'], 'text');
		showsetting('misc_customnav_url', 'urlnew', $nav['url'], 'text', $nav['type'] == '0');
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
		if($nav['type']) {
			showsetting('misc_customnav_level', ['levelnew', [
				[0, cplang('nolimit')],
				[1, cplang('member')],
				[2, cplang('usergroups_system_3')],
				[3, cplang('usergroups_system_1')],
			]], $nav['level'], 'select');
		}
		showtagfooter('tbody');
		showsubmit('editsubmit');
		showtablefooter();
		showformfooter();

	} else {

		$namenew = trim(dhtmlspecialchars($_GET['namenew']));
		$titlenew = trim(dhtmlspecialchars($_GET['titlenew']));
		$urlnew = str_replace(['&amp;'], ['&'], dhtmlspecialchars($_GET['urlnew']));
		$colornew = $_GET['colornew'];
		$stylebin = '';
		for($i = 3; $i >= 1; $i--) {
			$stylebin .= empty($_GET['stylenew'][$i]) ? '0' : '1';
		}
		$stylenew = bindec($stylebin);
		$targetnew = intval($_GET['targetnew']) ? 1 : 0;
		$levelnew = $nav['type'] ? (intval($_GET['levelnew']) && $_GET['levelnew'] > 0 && $_GET['levelnew'] < 4 ? intval($_GET['levelnew']) : 0) : 0;
		$urladd = $nav['type'] != '0' && $urlnew ? ", url='".$urlnew."'" : '';

		$data = [
			'name' => $namenew,
			'title' => $titlenew,
			'highlight' => "$stylenew$colornew",
			'target' => $targetnew,
			'level' => $levelnew
		];
		if($nav['type'] != '0' && $urlnew) {
			$data['url'] = $urlnew;
		}
		table_common_nav::t()->update($id, $data);

		updatecache('setting');
		cpmsg('nav_add_succeed', 'action=nav&operation=footernav', 'succeed');

	}

}
	