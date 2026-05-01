<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('smsgwsubmit')) {

	shownav('extended', 'smsgw_admin');
	showsubmenu('smsgw_admin', [
		['smsgw_admin_setting', 'smsgw&operation=setting', 0],
		['smsgw_admin_list', 'smsgw&operation=list', 1],
		['smsgw_admin_test', 'smsgw&operation=test', 0]
	]);

	showformheader("smsgw&operation=$operation");
	showtableheader('', 'fixpadding');
	showsubtitle(['', 'order', 'available', 'name', 'type', '']);

	$flag = false;

	$classnames = [];
	$avaliablesmsgw = getsmsgws();
	foreach(table_common_smsgw::t()->fetch_all_gw_order_id() as $smsgw) {
		$smsgwfile = '';
		$etype = explode(':', $smsgw['class']);
		if(count($etype) > 1 && preg_match('/^[\w\_:]+$/', $smsgw['class'])) {
			$key = 'smsgw_'.$etype[1].'.php';
			if(array_key_exists($key, $avaliablesmsgw)) {
				$smsgwfile = DISCUZ_PLUGIN($etype[0]).'/smsgw/smsgw_'.$etype[1].'.php';
				$smsgwclass = 'smsgw_'.$etype[1];
				unset($avaliablesmsgw[$key]);
			} else {
				table_common_smsgw::t()->update($smsgw['id'], ['available' => 0]);
				$flag = true;
				continue;
			}
		} else {
			$key = 'smsgw_'.$smsgw['class'].'.php';
			if(array_key_exists($key, $avaliablesmsgw)) {
				$smsgwfile = libfile('smsgw/'.$smsgw['class'], 'class');
				$smsgwclass = 'smsgw_'.$smsgw['class'];
				unset($avaliablesmsgw[$key]);
			} else {
				table_common_smsgw::t()->update($smsgw['id'], ['available' => 0]);
				$flag = true;
				continue;
			}
		}
		if(!isset($classnames[$smsgw['class']])) {
			require_once $smsgwfile;
			if(class_exists($smsgwclass)) {
				$smsgwclassv = new $smsgwclass();
				$classnames[$smsgw['class']] = lang('smsgw/'.$smsgw['class'], $smsgwclassv->name);
			} else {
				$classnames[$smsgw['class']] = $smsgw['class'];
			}
		}
		showtablerow('', ['class="td25"', 'class="td25"', 'class="td25"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$smsgw['smsgwid']}\" disabled=\"disabled\">",
			"<input type=\"text\" class=\"txt\" size=\"2\" name=\"ordernew[{$smsgw['smsgwid']}]\" value=\"{$smsgw['order']}\">",
			"<input class=\"checkbox\" type=\"checkbox\" name=\"availablenew[{$smsgw['smsgwid']}]\" value=\"1\" ".($smsgw['available'] ? 'checked' : '').'>',
			"<input type=\"text\" class=\"txt\" size=\"15\" name=\"namenew[{$smsgw['smsgwid']}]\" value=\"".dhtmlspecialchars($smsgw['name'])."\">",
			$smsgw['type'] == 0 ? cplang('smsgw_type_message') : cplang('smsgw_type_template'),
			"<a href=\"".ADMINSCRIPT."?action=smsgw&operation=edit&smsgwid={$smsgw['smsgwid']}\" class=\"act\">{$lang['edit']}</a>"
		]);
	}
	// 如果有新增加的文件, 需要添加到列表内
	if(count($avaliablesmsgw) > 0) {
		foreach($avaliablesmsgw as $smsgw) {
			$arr = ['type' => $smsgw['type'], 'class' => $smsgw['class'], 'order' => 0, 'name' => $smsgw['name'], 'sendrule' => $smsgw['sendrule']];
			table_common_smsgw::t()->insert($arr);
			$flag = true;
		}
	}
	if($flag) {
		header('Location: '.ADMINSCRIPT."?action=smsgw&operation=$operation");
	}

	showsubmit('smsgwsubmit');

	showtablefooter();
	showformfooter();
} else {

	if($_GET['delete']) {
		table_common_smsgw::t()->delete($_GET['delete']);
	}

	if(is_array($_GET['namenew'])) {
		foreach($_GET['namenew'] as $smsgwid => $title) {
			table_common_smsgw::t()->update($smsgwid, [
				'available' => $_GET['availablenew'][$smsgwid],
				'order' => $_GET['ordernew'][$smsgwid],
				'name' => $_GET['namenew'][$smsgwid]
			]);
		}
	}

	updatecache('setting');

	cpmsg('smsgw_succeed', dreferer(), 'succeed');

}
	