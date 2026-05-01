<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$edit = $_GET['edit'];
if(!submitcheck('bbcodessubmit') && !$edit) {
	shownav('style', 'setting_editor');

	showsubmenu('setting_editor', [
		['setting_editor_global', 'setting&operation=editor', 0],
		['setting_editor_code', 'misc&operation=bbcode', 1],
		['setting_editor_media', 'misc&operation=mediacode', 0],
		['setting_editor_block', 'editorblock&operation=list', 0]
	]);

	/*search={"setting_editor":"action=setting&operation=editor","setting_editor_code":"action=setting&operation=bbcode"}*/
	showtips('misc_bbcode_edit_tips');
	showformheader('misc&operation=bbcode');
	showtableheader('', 'fixpadding');
	showsubtitle(['', 'misc_bbcode_tag', 'available', 'display', 'display_order', 'misc_bbcode_icon', 'misc_bbcode_icon_file', '']);
	foreach(table_forum_bbcode::t()->fetch_all_by_available_icon() as $bbcode) {
		$bbicon = !empty($bbcode['icon']) ? (preg_match('/^https?:\/\//is', $bbcode['icon']) ? $bbcode['icon'] : STATICURL.'image/common/'.$bbcode['icon']) : '';
		showtablerow('', ['class="td25"', 'class="td21"', 'class="td25"', 'class="td25"', 'class="td28 td24"', 'class="td25"', 'class="td21"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$bbcode['id']}\">",
			"<input type=\"text\" class=\"txt\" size=\"15\" name=\"tagnew[{$bbcode['id']}]\" value=\"{$bbcode['tag']}\">",
			"<input class=\"checkbox\" type=\"checkbox\" name=\"availablenew[{$bbcode['id']}]\" value=\"1\" ".($bbcode['available'] ? 'checked="checked"' : NULL).'>',
			"<input class=\"checkbox\" type=\"checkbox\" name=\"displaynew[{$bbcode['id']}]\" value=\"1\" ".($bbcode['available'] == '2' ? 'checked="checked"' : NULL).'>',
			"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayordernew[{$bbcode['id']}]\" value=\"{$bbcode['displayorder']}\">",
			!empty($bbicon) ? "<em class=\"editor\"><a class=\"customedit\"><img src=\"$bbicon\" border=\"0\"></a></em>" : ' ',
			"<input type=\"text\" class=\"txt\" size=\"25\" name=\"iconnew[{$bbcode['id']}]\" value=\"{$bbcode['icon']}\">",
			"<a href=\"".ADMINSCRIPT."?action=misc&operation=bbcode&edit={$bbcode['id']}\" class=\"act\">{$lang['detail']}</a>"
		]);
	}
	showtablerow('', ['class="td25"', 'class="td25"', 'class="td25"', 'class="td25"', 'class="td28 td24"', 'class="td25"', 'class="td21"'], [
		cplang('add_new'),
		'<input type="text" class="txt" size="15" name="newtag">',
		'',
		'',
		'<input type="text" class="txt" size="2" name="newdisplayorder">',
		'',
		'<input type="text" class="txt" size="25" name="newicon">',
		''
	]);
	showsubmit('bbcodessubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();
	/*search*/

} elseif(submitcheck('bbcodessubmit')) {

	$delete = $_GET['delete'];
	if(is_array($delete)) {
		table_forum_bbcode::t()->delete($delete);
	}

	$tagnew = $_GET['tagnew'];
	$displaynew = $_GET['displaynew'];
	$displayordernew = $_GET['displayordernew'];
	$iconnew = $_GET['iconnew'];
	if(is_array($tagnew)) {
		$custom_ids = [];
		foreach(table_forum_bbcode::t()->fetch_all_by_available_icon() as $bbcode) {
			$custom_ids[] = $bbcode['id'];
		}
		$availablenew = $_GET['availablenew'];
		foreach($tagnew as $id => $val) {
			if(in_array($id, $custom_ids) && (!preg_match('/^[0-9a-z]+$/i', $tagnew[$id]) || strlen($tagnew[$id]) > 20)) {
				cpmsg('dzcode_edit_tag_invalid', '', 'error');
			}
			$availablenew[$id] = in_array($id, $custom_ids) ? $availablenew[$id] : 1;
			$availablenew[$id] = $availablenew[$id] && $displaynew[$id] ? 2 : $availablenew[$id];
			$data = [
				'available' => $availablenew[$id],
				'displayorder' => $displayordernew[$id]
			];
			if(in_array($id, $custom_ids)) {
				$data['tag'] = $tagnew[$id];
				$data['icon'] = $iconnew[$id];
			}
			table_forum_bbcode::t()->update($id, $data);
		}
	}

	$newtag = $_GET['newtag'];
	if($newtag != '') {
		if(!preg_match('/^[0-9a-z]+$/i', $newtag) || strlen($newtag) > 20) {
			cpmsg('dzcode_edit_tag_invalid', '', 'error');
		}
		$data = [
			'tag' => $newtag,
			'icon' => $_GET['newicon'],
			'available' => 0,
			'displayorder' => $_GET['newdisplayorder'],
			'params' => 1,
			'nest' => 1,
		];
		table_forum_bbcode::t()->insert($data);
	}

	updatecache(['bbcodes', 'bbcodes_display']);
	cpmsg('dzcode_edit_succeed', 'action=misc&operation=bbcode', 'succeed');

} elseif($edit) {

	$bbcode = table_forum_bbcode::t()->fetch($edit);
	if(!$bbcode) {
		cpmsg('bbcode_not_found', '', 'error');
	}

	if(!submitcheck('editsubmit')) {

		$bbcode['perm'] = explode("\t", $bbcode['perm']);
		$query = table_common_usergroup::t()->range_orderby_credit();
		$groupselect = [];
		foreach($query as $group) {
			$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
			$groupselect[$group['type']] .= '<option value="'.$group['groupid'].'"'.(is_array($bbcode['perm']) && in_array($group['groupid'], $bbcode['perm']) ? ' selected' : '').'>'.$group['grouptitle'].'</option>';
		}
		$select = '<select name="permnew[]" size="10" multiple="multiple"><option value=""'.(is_array($var['value']) && in_array('', $var['value']) ? ' selected' : '').'>'.cplang('plugins_empty').'</option>'.
			'<optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
			($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
			($groupselect['specialadmin'] ? '<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
			'<optgroup label="'.$lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup></select>';

		$bbcode['prompt'] = str_replace("\t", "\n", $bbcode['prompt']);

		shownav('style', 'nav_posting_bbcode');
		showchildmenu([['setting_editor', 'setting&operation=editor'], ['setting_editor_code', 'misc&operation=bbcode']], $bbcode['tag']);

		showformheader("misc&operation=bbcode&edit=$edit");
		showtableheader();
		showsetting('misc_bbcode_edit_tag', 'tagnew', $bbcode['tag'], 'text');
		showsetting('misc_bbcode_edit_replacement', 'replacementnew', $bbcode['replacement'], 'textarea');
		showsetting('misc_bbcode_edit_example', 'examplenew', $bbcode['example'], 'text');
		showsetting('misc_bbcode_edit_explanation', 'explanationnew', $bbcode['explanation'], 'text');
		showsetting('misc_bbcode_edit_params', 'paramsnew', $bbcode['params'], 'text');
		showsetting('misc_bbcode_edit_prompt', 'promptnew', $bbcode['prompt'], 'textarea');
		showsetting('misc_bbcode_edit_nest', 'nestnew', $bbcode['nest'], 'text');
		showsetting('misc_bbcode_edit_usergroup', '', '', $select);
		showsubmit('editsubmit');
		showtablefooter();
		showformfooter();

	} else {

		$tagnew = trim($_GET['tagnew']);
		$paramsnew = $_GET['paramsnew'];
		$nestnew = $_GET['nestnew'];
		$replacementnew = $_GET['replacementnew'];
		$examplenew = $_GET['examplenew'];
		$explanationnew = $_GET['explanationnew'];
		$promptnew = $_GET['promptnew'];
		$permnew = implode("\t", $_GET['permnew']);

		if(!preg_match('/^[0-9a-z]+$/i', $tagnew)) {
			cpmsg('dzcode_edit_tag_invalid', '', 'error');
		} elseif($paramsnew < 1 || $paramsnew > 3 || $nestnew < 1 || $nestnew > 3) {
			cpmsg('dzcode_edit_range_invalid', '', 'error');
		}
		$promptnew = trim(str_replace(["\t", "\r", "\n"], ['', '', "\t"], $promptnew));

		table_forum_bbcode::t()->update($edit, ['tag' => $tagnew, 'replacement' => $replacementnew, 'example' => $examplenew, 'explanation' => $explanationnew, 'params' => $paramsnew, 'prompt' => $promptnew, 'nest' => $nestnew, 'perm' => $permnew]);
		updatecache(['bbcodes', 'bbcodes_display']);
		cpmsg('dzcode_edit_succeed', 'action=misc&operation=bbcode', 'succeed');

	}
}
	