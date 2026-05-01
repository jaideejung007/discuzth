<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('magicsubmit')) {

	shownav('extended', 'magics', 'admin');
	showsubmenu('nav_magics', [
		['admin', 'magics&operation=admin', 1],
		['nav_magics_confer', 'members&operation=confermagic', 0]
	]);
	/*search={"nav_magics":"action=magics"}*/
	showtips('magics_tips');

	$settings = table_common_setting::t()->fetch_all_setting(['magicdiscount']);
	showformheader('magics&operation=admin');
	showtableheader();
	showsetting('magics_config_discount', 'settingsnew[magicdiscount]', $settings['magicdiscount'], 'text');
	showtablefooter();
	/*search*/

	showtableheader('magics_list', 'fixpadding');
	$newmagics = getmagics();
	showsubtitle(['', 'display_order', '<input type="checkbox" onclick="checkAll(\'prefix\', this.form, \'available\', \'availablechk\')" class="checkbox" id="availablechk" name="availablechk">'.cplang('available'), 'name', $lang['price'], $lang['magics_num'], 'weight', '']);

	foreach(table_common_magic::t()->fetch_all_data() as $magic) {
		$magic['credit'] = $magic['credit'] ? $magic['credit'] : $_G['setting']['creditstransextra'][3];
		$credits = '<select name="credit['.$magic['magicid'].']">';
		foreach($_G['setting']['extcredits'] as $i => $extcredit) {
			$credits .= '<option value="'.$i.'" '.($i == $magic['credit'] ? 'selected' : '').'>'.$extcredit['title'].'</option>';
		}
		$credits .= '</select>';
		$magictype = $lang['magics_type_'.$magic['type']];
		$eidentifier = explode(':', $magic['identifier']);

		showtablerow('', ['class="td25"', 'class="td25"', 'class="td25"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', '', ''], [
			"<input type=\"checkbox\" class=\"checkbox\" name=\"delete[]\" value=\"{$magic['magicid']}\">",
			"<input type=\"text\" class=\"txt\" name=\"displayorder[{$magic['magicid']}]\" value=\"{$magic['displayorder']}\">",
			"<input type=\"checkbox\" class=\"checkbox\" name=\"available[{$magic['magicid']}]\" value=\"1\" ".($magic['available'] ? 'checked' : '').'>',
			"<input type=\"text\" class=\"txt\" style=\"width:80px\" name=\"name[{$magic['magicid']}]\" value=\"{$magic['name']}\">".
			(count($eidentifier) > 1 ? (file_exists(DISCUZ_PLUGIN($eidentifier[0]).'/magic/magic_'.$eidentifier[1].'.small.gif') ? '<img class="vmiddle" src="source/plugin/'.$eidentifier[0].'/magic/magic_'.$eidentifier[1].'.small.gif" />' : '')
				: (file_exists(DISCUZ_ROOT.'./static/image/magic/'.$magic['identifier'].'.small.gif') ? '<img class="vmiddle" src="static/image/magic/'.$magic['identifier'].'.small.gif" />' : '')),
			"<input type=\"text\" class=\"txt\" name=\"price[{$magic['magicid']}]\" value=\"{$magic['price']}\">".$credits,
			"<input type=\"text\" class=\"txt\" name=\"num[{$magic['magicid']}]\" value=\"{$magic['num']}\">".
			($magic['supplytype'] ? '/ '.$magic['supplynum'].' / '.$lang['magic_suppytype_'.$magic['supplytype']] : ''),
			"<input type=\"text\" class=\"txt\" name=\"weight[{$magic['magicid']}]\" value=\"{$magic['weight']}\"><input type=\"hidden\" name=\"identifier[{$magic['magicid']}]\" value=\"{$magic['identifier']}\">",
			"<a href=\"".ADMINSCRIPT."?action=magics&operation=edit&magicid={$magic['magicid']}\" class=\"act\">{$lang['detail']}</a>"
		]);
		unset($newmagics[$magic['identifier']]);
	}
	foreach($newmagics as $newmagic) {
		$credits = '<select name="newcredit['.$newmagic['class'].']}">';
		foreach($_G['setting']['extcredits'] as $i => $extcredit) {
			$credits .= '<option value="'.$i.'">'.$extcredit['title'].'</option>';
		}
		$credits .= '</select>';
		$eclass = explode(':', $newmagic['class']);
		showtablerow('', ['class="td25"', 'class="td25"', 'class="td25"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', '', ''], [
			'',
			"<input type=\"text\" class=\"txt\" name=\"newdisplayorder[{$newmagic['class']}]\" value=\"0\">",
			"<input type=\"checkbox\" class=\"checkbox\" name=\"newavailable[{$newmagic['class']}]\" value=\"1\">",
			"<input type=\"text\" class=\"txt\" style=\"width:80px\" name=\"newname[{$newmagic['class']}]\" value=\"{$newmagic['name']}\">".
			(count($eclass) > 1 ? (file_exists(DISCUZ_PLUGIN($eclass[0]).'/magic/magic_'.$eclass[1].'.small.gif') ? '<img class="vmiddle" src="source/plugin/'.$eclass[0].'/magic/magic_'.$eclass[1].'.small.gif" />' : '')
				: (file_exists(DISCUZ_ROOT.'./static/image/magic/'.$newmagic['class'].'.small.gif') ? '<img class="vmiddle" src="static/image/magic/'.$newmagic['class'].'.small.gif" />' : '')).
			"<input type=\"hidden\" name=\"newdesc[{$newmagic['class']}]\" value=\"{$newmagic['desc']}\" />".
			"<input type=\"hidden\" name=\"newuseevent[{$newmagic['class']}]\" value=\"{$newmagic['useevent']}\" />",
			"<input type=\"text\" class=\"txt\" name=\"newprice[{$newmagic['class']}]\" value=\"{$newmagic['price']}\">".$credits,
			"<input type=\"text\" class=\"txt\" name=\"newnum[{$newmagic['class']}}]\" value=\"0\">",
			"<input type=\"text\" class=\"txt\" name=\"newweight[{$newmagic['class']}]\" value=\"{$newmagic['weight']}\">",
			'<font color="#F00">New!</font>'
		]);
	}
	showsubmit('magicsubmit', 'submit', 'del', '&nbsp;&nbsp;<input type="checkbox" onclick="checkAll(\'prefix\', this.form, \'available\', \'availablechk1\')" class="checkbox" id="availablechk1" name="availablechk1">'.cplang('available'));
	showtablefooter();
	showformfooter();

} else {
	if(is_array($_GET['settingsnew'])) {
		table_common_setting::t()->update_batch(['magicdiscount' => $_GET['settingsnew']['magicdiscount']]);
	}

	if($ids = dimplode($_GET['delete'])) {
		table_common_magic::t()->delete($_GET['delete']);
		table_common_member_magic::t()->delete_magic('', $_GET['delete']);
		table_common_magiclog::t()->delete_by_magicid($_GET['delete']);

	}

	if(is_array($_GET['name'])) {
		foreach($_GET['name'] as $id => $val) {
			if(!is_array($_GET['identifier']) ||
				!is_array($_GET['displayorder']) || !is_array($_GET['credit']) ||
				!is_array($_GET['price']) || !is_array($_GET['num']) ||
				!is_array($_GET['weight']) || !preg_match('/^[\w:]+$/', $_GET['identifier'][$id])) {
				continue;
			}
			table_common_magic::t()->update($id, [
				'available' => $_GET['available'][$id] ?? 0,
				'name' => $val,
				'identifier' => $_GET['identifier'][$id],
				'displayorder' => $_GET['displayorder'][$id],
				'credit' => $_GET['credit'][$id],
				'price' => $_GET['price'][$id],
				'num' => $_GET['num'][$id] ?? 0,
				'weight' => $_GET['weight'][$id]
			]);
		}
	}

	if(is_array($_GET['newname'])) {

		foreach($_GET['newname'] as $identifier => $name) {
			$data = [
				'name' => $name,
				'useevent' => $_GET['newuseevent'][$identifier],
				'identifier' => $identifier,
				'available' => $_GET['newavailable'][$identifier] ?? 0,
				'description' => $_GET['newdesc'][$identifier],
				'displayorder' => $_GET['newdisplayorder'][$identifier],
				'credit' => $_GET['newcredit'][$identifier],
				'price' => $_GET['newprice'][$identifier],
				'num' => $_GET['newnum'][$identifier] ?? 0,
				'weight' => $_GET['newweight'][$identifier],
			];
			table_common_magic::t()->insert($data);
		}
	}

	updatecache(['setting', 'magics']);
	cpmsg('magics_data_succeed', 'action=magics&operation=admin', 'succeed');

}
	