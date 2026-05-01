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

		showformheader('nav&operation=mynav');
		showtableheader();
		showsubtitle(['', 'display_order', 'name', 'url', 'type', 'available', '']);

		$navlist = [];
		foreach(table_common_nav::t()->fetch_all_by_navtype(3) as $nav) {
			if($nav['available'] < 0) {
				continue;
			}
			$navlist[$nav['id']] = $nav;
		}

		foreach($navlist as $nav) {
			if($nav['icon']) {
				$navicon = admin\class_attach::getUrl($nav['icon']);
			}
			showtablerow('', ['class="td25"', 'class="td25"', '', ''], [
				in_array($nav['type'], ['2', '1']) ? "<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$nav['id']}\">" : '<input type="checkbox" class="checkbox" value="" disabled="disabled" />',
				"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayordernew[{$nav['id']}]\" value=\"{$nav['displayorder']}\">",
				"<input type=\"text\" class=\"txt\" size=\"15\" name=\"namenew[{$nav['id']}]\" value=\"".dhtmlspecialchars($nav['name'])."\">".
				($nav['icon'] ? '<img src="'.$navicon.'" width="40" height="40" class="vmiddle" />' : ''),
				$nav['type'] == '0' ? $nav['url'] : "<input type=\"text\" class=\"txt\" size=\"15\" name=\"urlnew[{$nav['id']}]\" value=\"".dhtmlspecialchars($nav['url'])."\">",
				cplang($nav['type'] == '0' ? 'inbuilt' : ($nav['type'] == '3' ? 'nav_plugin' : ($nav['type'] == '4' ? 'channel' : 'custom'))),
				"<input class=\"checkbox\" type=\"checkbox\" name=\"availablenew[{$nav['id']}]\" value=\"1\" ".($nav['available'] ? 'checked' : '').'>',
				"<a href=\"".ADMINSCRIPT."?action=nav&operation=mynav&do=edit&id={$nav['id']}\" class=\"act\">{$lang['edit']}</a>"
			]);
		}
		echo '<tr><td colspan="1"></td><td colspan="7"><div><a href="###" onclick="addrow(this, 0, 0)" class="addtr">'.$lang['nav_mynav_add'].'</a></div></td></tr>';
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

		if($_GET['delete']) {
			table_common_nav::t()->delete_by_navtype_id(3, $_GET['delete']);
		}

		if(is_array($_GET['namenew'])) {
			foreach($_GET['namenew'] as $id => $name) {
				$name = trim(dhtmlspecialchars($name));
				$urlnew = str_replace(['&amp;'], ['&'], dhtmlspecialchars($_GET['urlnew'][$id]));
				$urladd = !empty($_GET['urlnew'][$id]) ? ", url='$urlnew'" : '';
				$availablenew[$id] = $name && (!isset($_GET['urlnew'][$id]) || $_GET['urlnew'][$id]) && $_GET['availablenew'][$id];
				$displayordernew[$id] = intval($_GET['displayordernew'][$id]);
				$nameadd = !empty($name) ? ", name='$name'" : '';
				$data = [
					'displayorder' => $displayordernew[$id],
					'available' => $availablenew[$id]
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
						'navtype' => 3
					];
					table_common_nav::t()->insert($data);
				}
			}
		}

		updatecache('setting');
		cpmsg('nav_add_succeed', 'action=nav&operation=mynav', 'succeed');

	}

} elseif($do == 'edit' && ($id = $_GET['id'])) {

	$nav = table_common_nav::t()->fetch_by_id_navtype($id, 3);
	if(!$nav) {
		cpmsg('nav_not_found', '', 'error');
	}

	if(!submitcheck('editsubmit')) {

		$nav['allowsubnew'] = 1;
		if(str_starts_with($nav['subname'], "\t")) {
			$nav['allowsubnew'] = 0;
			$nav['subname'] = substr($nav['subname'], 1);
		}
		if($nav['icon']) {
			$navicon = admin\class_attach::getUrl($nav['icon']);
			$naviconhtml = '<br /><label><input type="checkbox" class="checkbox" name="deleteicon" value="yes" /> '.$lang['delete'].'</label><br /><img src="'.$navicon.'" width="40" height="40" />';
		}
		shownav('style', 'nav_setting_customnav');
		showchildmenu([['nav_setting_customnav', 'nav'], ['nav_nav_mynav', 'nav&operation=mynav']], $nav['name']);

		showformheader("nav&operation=mynav&do=edit&id=$id", 'enctype');
		showtableheader();
		showsetting('misc_customnav_name', 'namenew', $nav['name'], 'text');
		showsetting('misc_customnav_title', 'titlenew', $nav['title'], 'text');
		showsetting('misc_customnav_url', 'urlnew', $nav['url'], 'text', $nav['type'] == '0');
		showsetting('misc_customnav_icon', 'iconnew', $nav['icon'], 'filetext', '', 0, cplang('misc_mynav_icon_comment').$naviconhtml);
		showsetting('misc_customnav_url_open', ['targetnew', [
			[0, cplang('misc_customnav_url_open_default')],
			[1, cplang('misc_customnav_url_open_blank')]
		], TRUE], $nav['target'], 'mradio');
		showsetting('misc_customnav_level', ['levelnew', [
			[0, cplang('nolimit')],
			[1, cplang('member')],
			[2, cplang('usergroups_system_3')],
			[3, cplang('usergroups_system_1')],
		]], $nav['level'], 'select');
		showtagfooter('tbody');
		showsubmit('editsubmit');
		showtablefooter();
		showformfooter();

	} else {

		$namenew = trim(dhtmlspecialchars($_GET['namenew']));
		$titlenew = trim(dhtmlspecialchars($_GET['titlenew']));
		$urlnew = str_replace(['&amp;'], ['&'], dhtmlspecialchars($_GET['urlnew']));
		$targetnew = intval($_GET['targetnew']) ? 1 : 0;
		$levelnew = intval($_GET['levelnew']) && $_GET['levelnew'] > 0 && $_GET['levelnew'] < 4 ? intval($_GET['levelnew']) : 0;
		$urladd = $nav['type'] != '0' && $urlnew ? ", url='$urlnew'" : '';

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
			'title' => $titlenew,
			'target' => $targetnew,
			'level' => $levelnew,
			'icon' => $iconnew
		];
		if($nav['type'] != '0' && $urlnew) {
			$data['url'] = $urlnew;
		}
		table_common_nav::t()->update($id, $data);

		updatecache('setting');
		cpmsg('nav_add_succeed', 'action=nav&operation=mynav', 'succeed');

	}

}
	