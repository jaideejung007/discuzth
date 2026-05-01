<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('stampsubmit')) {

	$anchor = in_array($_GET['anchor'], ['list', 'llist', 'add']) ? $_GET['anchor'] : 'list';
	shownav('style', 'nav_thread_stamp');
	showsubmenuanchors('nav_thread_stamp', [
		['misc_stamp_thread', 'list', $anchor == 'list'],
		['misc_stamp_list', 'llist', $anchor == 'llist'],
		['add', 'add', $anchor == 'add']
	]);

	showtagheader('div', 'list', $anchor == 'list');
	/*search={"nav_thread_stamp":"action=misc&operation=stamp","misc_stamp_thread":"action=misc&operation=stamp&anchor=list"}*/
	showtips('misc_stamp_listtips');
	/*search*/
	showformheader('misc&operation=stamp');
	showhiddenfields(['anchor' => 'list']);
	showtableheader();
	showsubtitle(['', 'misc_stamp_id', 'misc_stamp_name', 'smilies_edit_image', 'smilies_edit_filename', 'misc_stamp_icon', 'misc_stamp_option']);

	$imgfilter = $stamplist = $stamplistfiles = $stampicons = [];
	foreach(table_common_smiley::t()->fetch_all_by_type('stamplist') as $smiley) {
		$stamplistfiles[$smiley['url']] = $smiley['id'];
		$stampicons[$smiley['url']] = $smiley['typeid'];
		$stamplist[] = $smiley;
	}
	$tselect = '<select><option value="0">'.cplang('none').'</option><option value="1">'.cplang('misc_stamp_option_stick').'</option><option value="2">'.cplang('misc_stamp_option_digest').'</option><option value="3">'.cplang('misc_stamp_option_recommend').'</option><option value="4">'.cplang('misc_stamp_option_recommendto').'</option></select>';
	foreach(table_common_smiley::t()->fetch_all_by_type('stamp') as $smiley) {
		$s = $r = [];
		$s[] = '<select>';
		$r[] = '<select name="typeidnew['.$smiley['id'].']">';
		if($smiley['typeid']) {
			$s[] = '<option value="'.$smiley['typeid'].'">';
			$r[] = '<option value="'.$smiley['typeid'].'" selected="selected">';
			$s[] = '<option value="0">';
			$r[] = '<option value="-1">';
		}
		$tselectrow = str_replace($s, $r, $tselect);
		$dot = strrpos($smiley['url'], '.');
		$fn = substr($smiley['url'], 0, $dot);
		$ext = substr($smiley['url'], $dot + 1);
		$stampicon = $fn.'.small.'.$ext;
		$small = array_key_exists($stampicon, $stamplistfiles);
		showtablerow('', ['class="td25"', 'class="td25"', 'class="td23"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$smiley['id']}\">",
			"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayorder[{$smiley['id']}]\" value=\"{$smiley['displayorder']}\">",
			"<input type=\"text\" class=\"txt\" size=\"2\" name=\"code[{$smiley['id']}]\" value=\"{$smiley['code']}\">",
			"<img src=\"static/image/stamp/{$smiley['url']}\">",
			$smiley['url'],
			($small ? '<input class="checkbox" type="checkbox" name="stampicon['.$smiley['id'].']"'.($smiley['id'] == $stampicons[$stampicon] ? ' checked="checked"' : '').' value="'.$stamplistfiles[$stampicon].'" /><img class="vmiddle" src="static/image/stamp/'.$stampicon.'">' : ''),
			$tselectrow,
		]);
		$imgfilter[] = $smiley['url'];
	}

	showsubmit('stampsubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();
	showtagfooter('div');

	showtagheader('div', 'llist', $anchor == 'llist');
	/*search={"nav_thread_stamp":"action=misc&operation=stamp","misc_stamp_list":"action=misc&operation=stamp&anchor=llist"}*/
	showtips('misc_stamp_listtips');
	/*search*/
	showformheader('misc&operation=stamp&type=list');
	showhiddenfields(['anchor' => 'llist']);
	showtableheader();
	showsubtitle(['', 'misc_stamp_id', 'misc_stamp_listname', 'smilies_edit_image', 'smilies_edit_filename']);

	foreach($stamplist as $smiley) {
		showtablerow('', ['class="td25"', 'class="td25"', 'class="td23"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$smiley['id']}\">",
			"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayorder[{$smiley['id']}]\" value=\"{$smiley['displayorder']}\">",
			"<input type=\"text\" class=\"txt\" size=\"2\" name=\"code[{$smiley['id']}]\" value=\"{$smiley['code']}\">",
			"<img src=\"static/image/stamp/{$smiley['url']}\">",
			$smiley['url']
		]);
		$imgfilter[] = $smiley['url'];
	}

	showsubmit('stampsubmit', 'submit', 'del');
	showtablefooter();
	showformfooter();
	showtagfooter('div');

	showtagheader('div', 'add', $anchor == 'add');
	showformheader('misc&operation=stamp');
	/*search={"nav_thread_stamp":"action=misc&operation=stamp","add":"action=misc&operation=stamp&anchor=add"}*/
	showtips('misc_stamp_addtips');
	/*search*/
	showtableheader();
	showsubtitle(['add', 'misc_stamp_type', 'misc_stamp_id', 'misc_stamp_imagename', 'smilies_edit_image', 'smilies_edit_filename']);

	$newid = 0;
	$imgextarray = ['png', 'gif'];
	$stampsdir = dir(DISCUZ_ROOT.'./static/image/stamp');
	while($entry = $stampsdir->read()) {
		if(in_array(strtolower(fileext($entry)), $imgextarray) && !in_array($entry, $imgfilter) && is_file(DISCUZ_ROOT.'./static/image/stamp/'.$entry)) {
			showtablerow('', ['class="td25"', 'class="td28 td24 rowform"', 'class="td23"'], [
				"<input type=\"checkbox\" name=\"addcheck[$newid]\" id=\"addcheck_$newid\" class=\"checkbox\">",
				"<ul onmouseover=\"altStyle(this);\">".
				"<li class=\"checked\"><input type=\"radio\" name=\"addtype[$newid]\" value=\"0\" checked=\"checked\" class=\"radio\">".cplang('misc_stamp_thread').'</li>'.
				"<li><input type=\"radio\" name=\"addtype[$newid]\" value=\"1\" class=\"radio\" onclick=\"$('addcheck_$newid').checked='true'\">".cplang('misc_stamp_list').'</li>'.
				'</ul>',
				"<input type=\"text\" class=\"txt\" size=\"2\" name=\"adddisplayorder[$newid]\" value=\"0\">",
				"<input type=\"text\" class=\"txt\" size=\"2\" name=\"addcode[$newid]\" value=\"\">",
				"<img src=\"static/image/stamp/$entry\" />",
				"<input type=\"hidden\" class=\"txt\" size=\"35\" name=\"addurl[$newid]\" value=\"$entry\">$entry"
			]);
			$newid++;
		}
	}
	$stampsdir->close();
	if(!$newid) {
		showtablerow('', ['class="td25"', 'colspan="3"'], ['', cplang('misc_stamp_tips')]);
	} else {
		showsubmit('stampsubmit', 'submit', '<input type="checkbox" class="checkbox" name="chkall2" id="chkall2" onclick="checkAll(\'prefix\', this.form, \'addcheck\', \'chkall2\')"><label for="chkall2">'.cplang('select_all').'</label>');
	}

	showtablefooter();
	showformfooter();
	showtagfooter('div');

} else {

	if($_GET['delete']) {
		table_common_smiley::t()->delete($_GET['delete']);
	}

	if(is_array($_GET['displayorder'])) {
		$typeidset = [];
		foreach($_GET['displayorder'] as $id => $val) {
			$_GET['displayorder'][$id] = intval($_GET['displayorder'][$id]);
			if($_GET['displayorder'][$id] >= 0 && $_GET['displayorder'][$id] < 100) {
				$typeidadd = '';
				if($_GET['typeidnew'][$id]) {
					if(!isset($typeidset[$_GET['typeidnew'][$id]])) {
						$_GET['typeidnew'][$id] = $_GET['typeidnew'][$id] > 0 ? $_GET['typeidnew'][$id] : 0;
						$typeidadd = ",typeid='{$_GET['typeidnew'][$id]}'";
						$typeidset[$_GET['typeidnew'][$id]] = TRUE;
					} else {
						$_GET['typeidnew'][$id] = 0;
					}
				}
				table_common_smiley::t()->update($id, [
					'displayorder' => $_GET['displayorder'][$id],
					'code' => $_GET['code'][$id],
					'typeid' => $_GET['typeidnew'][$id],
				]);
			}
		}
	}

	if(is_array($_GET['addurl'])) {
		$count = table_common_smiley::t()->count_by_type(['stamp', 'stamplist']);
		if($count < 100) {
			foreach($_GET['addurl'] as $k => $v) {
				if($_GET['addcheck'][$k] && $_GET['addcode'][$k]) {
					$count++;

					table_common_smiley::t()->insert([
						'displayorder' => '0',
						'type' => (!$_GET['addtype'][$k] ? 'stamp' : 'stamplist'),
						'url' => $_GET['addurl'][$k],
						'code' => $_GET['addcode'][$k],
					]);
				}
			}
		}
	}

	table_common_smiley::t()->update_by_type('stamplist', ['typeid' => 0]);
	if(is_array($_GET['stampicon'])) {
		foreach($_GET['stampicon'] as $k => $v) {
			if($_GET['typeidnew'][$k]) {
				$k = 0;
			}
			table_common_smiley::t()->update_by_id_type($v, 'stamplist', ['typeid' => $k]);
		}
	}

	updatecache('stamps');
	updatecache('stamptypeid');

	cpmsg('thread_stamp_succeed', "action=misc&operation=stamp&anchor={$_GET['anchor']}", 'succeed');
}
	