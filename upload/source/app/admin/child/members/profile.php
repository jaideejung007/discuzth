<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$fieldid = $_GET['fieldid'] ? $_GET['fieldid'] : '';
shownav('user', 'nav_members_profile');
if($fieldid) {
	$_G['setting']['privacy'] = !empty($_G['setting']['privacy']) ? $_G['setting']['privacy'] : [];
	$_G['setting']['privacy'] = is_array($_G['setting']['privacy']) ? $_G['setting']['privacy'] : dunserialize($_G['setting']['privacy']);

	$field = table_common_member_profile_setting::t()->fetch($fieldid);
	$fixedfields1 = ['uid', 'constellation', 'zodiac'];
	$fixedfields2 = ['gender', 'birthday', 'birthcity', 'residecity'];
	$field['isfixed1'] = in_array($fieldid, $fixedfields1);
	$field['isfixed2'] = $field['isfixed1'] || in_array($fieldid, $fixedfields2);
	$profilegroup = table_common_setting::t()->fetch_setting('profilegroup', true);
	$profilevalidate = [];
	include childfile('profilevalidate', 'home/spacecp');
	$field['validate'] = $field['validate'] ? $field['validate'] : ($profilevalidate[$fieldid] ? $profilevalidate[$fieldid] : '');
	if(!submitcheck('editsubmit')) {

		showchildmenu([['members_profile', 'members&operation=profile']], $field['title']);

		showformheader('members&operation=profile&fieldid='.$fieldid);
		showtableheader();
		showsetting('members_profile_edit_name', 'title', $field['title'], 'text');
		showsetting('members_profile_edit_desc', 'description', $field['description'], 'text');
		if(!$field['isfixed2']) {
			if($field['fieldid'] == 'realname') {
				showsetting('members_profile_edit_form_type', ['formtype', [
					['text', $lang['members_profile_edit_text'], ['valuenumber' => '', 'fieldchoices' => 'none', 'fieldvalidate' => '']]
				]], $field['formtype'], 'mradio');
			} elseif($field['fieldid'] == 'fields') {
				showsetting('members_profile_edit_form_type', ['formtype', [
					['json', $lang['members_profile_edit_json'], ['valuenumber' => 'none', 'fieldchoices' => '', 'fieldvalidate' => 'none']]
				]], $field['formtype'], 'mradio');
			} else {
				showsetting('members_profile_edit_form_type', ['formtype', [
					['text', $lang['members_profile_edit_text'], ['valuenumber' => '', 'fieldchoices' => 'none', 'fieldvalidate' => '']],
					['textarea', $lang['members_profile_edit_textarea'], ['valuenumber' => '', 'fieldchoices' => 'none', 'fieldvalidate' => '']],
					['radio', $lang['members_profile_edit_radio'], ['valuenumber' => 'none', 'fieldchoices' => '', 'fieldvalidate' => 'none']],
					['checkbox', $lang['members_profile_edit_checkbox'], ['valuenumber' => '', 'fieldchoices' => '', 'fieldvalidate' => 'none']],
					['select', $lang['members_profile_edit_select'], ['valuenumber' => 'none', 'fieldchoices' => '', 'fieldvalidate' => 'none']],
					['list', $lang['members_profile_edit_list'], ['valuenumber' => '', 'fieldchoices' => '']],
					['file', $lang['members_profile_edit_file'], ['valuenumber' => '', 'fieldchoices' => 'none', 'fieldvalidate' => 'none']]
				]], $field['formtype'], 'mradio');
			}
			showtagheader('tbody', 'valuenumber', !in_array($field['formtype'], ['radio', 'select', 'json']), 'sub');
			showsetting('members_profile_edit_value_number', 'size', $field['size'], 'text');
			showtagfooter('tbody');

			showtagheader('tbody', 'fieldchoices', !in_array($field['formtype'], ['file', 'text', 'textarea']), 'sub');
			showsetting('members_profile_edit_choices', 'choices', $field['choices'], 'textarea');
			showtagfooter('tbody');

			showtagheader('tbody', 'fieldvalidate', in_array($field['formtype'], ['text', 'textarea']), 'sub');
			showsetting('members_profile_edit_validate', 'validate', $field['validate'], 'text');
			showtagfooter('tbody');
		}
		if(!$field['isfixed1']) {
			showsetting('members_profile_edit_available', 'available', $field['available'], 'radio');
			showsetting('members_profile_edit_unchangeable', 'unchangeable', $field['unchangeable'], 'radio');
			showsetting('members_profile_edit_needverify', 'needverify', $field['needverify'], 'radio');
			showsetting('members_profile_edit_required', 'required', $field['required'], 'radio');
		}
		showsetting('members_profile_edit_invisible', 'invisible', $field['invisible'], 'radio');
		$privacyselect = [
			['0', cplang('members_profile_edit_privacy_public')],
			['1', cplang('members_profile_edit_privacy_friend')],
			['3', cplang('members_profile_edit_privacy_secret')]
		];
		showsetting('members_profile_edit_default_privacy', ['privacy', $privacyselect], $_G['setting']['privacy']['profile'][$fieldid], 'select');
		showsetting('members_profile_edit_showincard', 'showincard', $field['showincard'], 'radio');
		showsetting('members_profile_edit_showinregister', 'showinregister', $field['showinregister'], 'radio');
		showsetting('members_profile_edit_allowsearch', 'allowsearch', $field['allowsearch'], 'radio');
		if(!in_array($field['fieldid'], ['gender', 'birthyear', 'birthmonth', 'birthday'])){
			$encryptselect = [
				['0', cplang('members_profile_edit_encrypt_none')],
				['1', cplang('members_profile_edit_encrypt_numeric')],
				['2', cplang('members_profile_edit_encrypt_string')]
			];
			showsetting('members_profile_edit_encrypt', ['encrypt', $encryptselect], $field['encrypt'], 'select');
		}
		if(!empty($profilegroup)) {
			$groupstr = '';
			foreach($profilegroup as $key => $value) {
				if($value['available']) {
					if(in_array($fieldid, $value['field'])) {
						$checked = ' checked="checked" ';
						$class = ' class="checked" ';
					} else {
						$class = $checked = '';
					}
					$groupstr .= "<li $class style=\"float: left; width: 10%;\"><input type=\"checkbox\" value=\"$key\" name=\"profilegroup[$key]\" class=\"checkbox\" $checked>&nbsp;{$value['title']}</li>";
				}
			}
			if(!empty($groupstr)) {
				print <<<EOF
						<tr>
							<td class="td27" colspan="2">{$lang['setting_profile_group']}:</td>
						</tr>
						<tr>
							<td colspan="2">
								<ul class="dblist" onmouseover="altStyle(this);">
									<li style="width: 100%;"><input type="checkbox" name="chkall" onclick="checkAll('prefix', this.form, 'profilegroup')" class="checkbox">&nbsp;{$lang['select_all']}</li>
									$groupstr
								</ul>
							</td>
						</tr>
EOF;
			}
		}

		showsetting('members_profile_edit_display_order', 'displayorder', $field['displayorder'], 'text');
		showsubmit('editsubmit');
		showtablefooter();
		showformfooter();

	} else {

		$setarr = [
			'invisible' => intval($_POST['invisible']),
			'showincard' => intval($_POST['showincard']),
			'showinregister' => intval($_POST['showinregister']),
			'allowsearch' => intval($_POST['allowsearch']),
			'displayorder' => intval($_POST['displayorder']),
			'encrypt' => intval($_POST['encrypt'])
		];
		$_POST['title'] = dhtmlspecialchars(trim($_POST['title']));
		if(empty($_POST['title'])) {
			cpmsg('members_profile_edit_title_empty_error', 'action=members&operation=profile&fieldid='.$fieldid, 'error');
		}
		$setarr['title'] = $_POST['title'];
		$setarr['description'] = dhtmlspecialchars(trim($_POST['description']));
		if(!$field['isfixed1']) {
			$setarr['required'] = intval($_POST['required']);
			$setarr['available'] = intval($_POST['available']);
			$setarr['unchangeable'] = intval($_POST['unchangeable']);
			$setarr['needverify'] = intval($_POST['needverify']);
		}
		if(!$field['isfixed2']) {
			$setarr['formtype'] = $fieldid == 'realname' ? 'text' : strtolower(trim($_POST['formtype']));
			$setarr['size'] = intval($_POST['size']);
			if($_POST['choices']) {
				$_POST['choices'] = trim($_POST['choices']);
				if($fieldid != 'fields') {
					$ops = explode("\n", $_POST['choices']);
					$parts = [];
					foreach($ops as $op) {
						$parts[] = dhtmlspecialchars(trim($op));
					}
					$_POST['choices'] = implode("\n", $parts);
				}
			}
			$setarr['choices'] = $_POST['choices'];
			if($_POST['validate'] && $_POST['validate'] != $field['validate']) {
				$setarr['validate'] = $_POST['validate'];
			} elseif(empty($_POST['validate'])) {
				$setarr['validate'] = '';
			}
		}
		table_common_member_profile_setting::t()->update($fieldid, $setarr);
		if($_GET['fieldid'] == 'birthday') {
			table_common_member_profile_setting::t()->update('birthmonth', $setarr);
			table_common_member_profile_setting::t()->update('birthyear', $setarr);
		} elseif($_GET['fieldid'] == 'birthcity') {
			table_common_member_profile_setting::t()->update('birthcountry', $setarr);
			table_common_member_profile_setting::t()->update('birthprovince', $setarr);
			$setarr['required'] = 0;
			table_common_member_profile_setting::t()->update('birthdist', $setarr);
			table_common_member_profile_setting::t()->update('birthcommunity', $setarr);
		} elseif($_GET['fieldid'] == 'residecity') {
			table_common_member_profile_setting::t()->update('residecountry', $setarr);
			table_common_member_profile_setting::t()->update('resideprovince', $setarr);
			$setarr['required'] = 0;
			table_common_member_profile_setting::t()->update('residedist', $setarr);
			table_common_member_profile_setting::t()->update('residecommunity', $setarr);
		} elseif($_GET['fieldid'] == 'idcard') {
			table_common_member_profile_setting::t()->update('idcardtype', $setarr);
		}

		foreach($profilegroup as $type => $pgroup) {
			if(is_array($_GET['profilegroup']) && in_array($type, $_GET['profilegroup'])) {
				$profilegroup[$type]['field'][$fieldid] = $fieldid;
			} else {
				unset($profilegroup[$type]['field'][$fieldid]);
			}
		}
		table_common_setting::t()->update_setting('profilegroup', $profilegroup);
		require_once libfile('function/cache');
		if(!isset($_G['setting']['privacy']['profile']) || $_G['setting']['privacy']['profile'][$fieldid] != $_POST['privacy']) {
			$_G['setting']['privacy']['profile'][$fieldid] = $_POST['privacy'];
			table_common_setting::t()->update_setting('privacy', $_G['setting']['privacy']);
		}
		updatecache(['profilesetting', 'fields_required', 'fields_optional', 'fields_register', 'setting']);
		include_once libfile('function/block');
		loadcache('profilesetting', true);
		blockclass_cache();
		cpmsg('members_profile_edit_succeed', 'action=members&operation=profile', 'succeed');
	}
} else {

	$list = [];
	foreach(table_common_member_profile_setting::t()->range_setting() as $fieldid => $value) {
		$list[$fieldid] = [
			'title' => $value['title'],
			'displayorder' => $value['displayorder'],
			'available' => $value['available'],
			'invisible' => $value['invisible'],
			'showincard' => $value['showincard'],
			'showinregister' => $value['showinregister'],
			'encrypt' => $value['encrypt']];
	}

	unset($list['birthyear']);
	unset($list['birthmonth']);
	unset($list['birthcountry']);
	unset($list['birthprovince']);
	unset($list['birthdist']);
	unset($list['birthcommunity']);
	unset($list['residecountry']);
	unset($list['resideprovince']);
	unset($list['residedist']);
	unset($list['residecommunity']);

	if(!submitcheck('ordersubmit')) {
		$_GET['anchor'] = in_array($_GET['action'], ['members', 'setting']) ? $_GET['action'] : 'members';
		$current = [$_GET['anchor'] => 1];
		$profilenav = [
			['members_profile_list', 'members&operation=profile', $current['members']],
			['members_profile_group', 'setting&operation=profile', $current['setting']],
		];
		showsubmenu($lang['members_profile'], $profilenav);
		showtips('members_profile_tips');
		showformheader('members&operation=profile');
		showtableheader('', '', 'id="profiletable_header"');
		$tdstyle = ['class="td22"', 'class="td28" width="100"', 'class="td28" width="100"', 'class="td28" width="100"', 'class="td28" width="100"', 'class="td28" width="100"', 'class="td28"', 'class="td28"'];
		showsubtitle(['members_profile_edit_name', 'members_profile_edit_display_order', 'members_profile_edit_available', 'members_profile_edit_profile_view', 'members_profile_edit_card_view', 'members_profile_edit_reg_view', 'members_profile_edit_encrypt_title'], 'header tbm', $tdstyle);
		showtablefooter();
		showtableheader('members_profile', '', 'id="porfiletable"');
		showsubtitle(['members_profile_edit_name', 'members_profile_edit_display_order',
			'members_profile_edit_available', 'members_profile_edit_profile_view', 'members_profile_edit_card_view',
			'members_profile_edit_reg_view', 'members_profile_edit_encrypt_title'], 'header', $tdstyle);
		foreach($list as $fieldid => $value) {
			$value['available'] = '<input type="checkbox" class="checkbox" name="available['.$fieldid.']" '.($value['available'] ? 'checked="checked" ' : '').'value="1">';
			$value['invisible'] = '<input type="checkbox" class="checkbox" name="invisible['.$fieldid.']" '.(!$value['invisible'] ? 'checked="checked" ' : '').'value="1">';
			$value['showincard'] = '<input type="checkbox" class="checkbox" name="showincard['.$fieldid.']" '.($value['showincard'] ? 'checked="checked" ' : '').'value="1">';
			$value['showinregister'] = '<input type="checkbox" class="checkbox" name="showinregister['.$fieldid.']" '.($value['showinregister'] ? 'checked="checked" ' : '').'value="1">';
			$value['displayorder'] = '<input type="text" name="displayorder['.$fieldid.']" value="'.$value['displayorder'].'" size="5">';
			$value['edit'] = '<a href="'.ADMINSCRIPT.'?action=members&operation=profile&fieldid='.$fieldid.'" title="" class="act">'.$lang['edit'].'</a>';
			$value['encrypt'] = $value['encrypt'] ? cplang('members_profile_edit_encrypt_exists') : '';
			$value['title'] = $value['title'].'<br/><span class="lightfont">('.$fieldid.')</span>';
			showtablerow('', [], $value);
		}
		showsubmit('ordersubmit');
		showtablefooter();
		showformfooter();
		echo '<script type="text/javascript">floatbottom(\'profiletable_header\');$(\'profiletable_header\').style.width = $(\'porfiletable\').offsetWidth + \'px\';</script>';
	} else {
		foreach($_GET['displayorder'] as $fieldid => $value) {
			$setarr = [
				'displayorder' => intval($value),
				'invisible' => intval($_GET['invisible'][$fieldid]) ? 0 : 1,
				'available' => intval($_GET['available'][$fieldid]),
				'showincard' => intval($_GET['showincard'][$fieldid]),
				'showinregister' => intval($_GET['showinregister'][$fieldid]),
			];
			table_common_member_profile_setting::t()->update($fieldid, $setarr);

			if($fieldid == 'birthday') {
				table_common_member_profile_setting::t()->update('birthmonth', $setarr);
				table_common_member_profile_setting::t()->update('birthyear', $setarr);
			} elseif($fieldid == 'birthcity') {
				table_common_member_profile_setting::t()->update('birthcountry', $setarr);
				table_common_member_profile_setting::t()->update('birthprovince', $setarr);
				$setarr['required'] = 0;
				table_common_member_profile_setting::t()->update('birthdist', $setarr);
				table_common_member_profile_setting::t()->update('birthcommunity', $setarr);
			} elseif($fieldid == 'residecity') {
				table_common_member_profile_setting::t()->update('residecountry', $setarr);
				table_common_member_profile_setting::t()->update('resideprovince', $setarr);
				$setarr['required'] = 0;
				table_common_member_profile_setting::t()->update('residedist', $setarr);
				table_common_member_profile_setting::t()->update('residecommunity', $setarr);
			} elseif($fieldid == 'idcard') {
				table_common_member_profile_setting::t()->update('idcardtype', $setarr);
			}

		}
		require_once libfile('function/cache');
		updatecache(['profilesetting', 'fields_required', 'fields_optional', 'fields_register', 'setting']);
		include_once libfile('function/block');
		loadcache('profilesetting', true);
		blockclass_cache();
		cpmsg('members_profile_edit_succeed', 'action=members&operation=profile', 'succeed');
	}
}
	