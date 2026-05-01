<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('smiliessubmit')) {

	shownav('style', 'smilies_edit');
	showsubmenu('nav_smilies', [
		['smilies_type', 'smilies', 1],
		['smilies_import', 'smilies&operation=import', 0],
	]);
	/*search={"nav_smilies":"action=smilies","smilies_type":"action=smilies"}*/
	showtips('smilies_tips_smileytypes');
	/*search*/
	showformheader('smilies');
	showboxheader();
	showtableheader();
	showsubtitle(['', 'display_order', 'enable', 'smilies_type', 'dir', 'smilies_nums', '']);

	$smtypes = 0;
	$dirfilter = [];
	$emojiExists = false;
	$smilies = table_forum_imagetype::t()->fetch_all_by_type('smiley');
	foreach($smilies as $smiley) {
		if($smiley['directory'] == ':emoji') {
			$emojiExists = true;
		}
	}
	if(!$emojiExists) {
		require_once DISCUZ_ROOT.'./source/data/admincp/emoji.php';
		if(!empty($defaultEmoji)) {
			$id = table_forum_imagetype::t()->insert($row = [
				'available' => 0,
				'name' => 'Emoji',
				'type' => 'smiley',
				'directory' => ':emoji',
				'displayorder' => -1,
			], true);
			$row['typeid'] = $id;
			$i = $_GET['lastDisplayorder'] ?? 0;
			foreach(mb_str_split($defaultEmoji) as $code) {
				table_common_smiley::t()->insert(['type' => 'smiley', 'typeid' => $id, 'displayorder' => ++$i, 'code' => $code, 'url' => '']);
			}
			array_unshift($smilies, $row);
		}
	}

	foreach($smilies as $type) {
		$smiliesnum = table_common_smiley::t()->count_by_type_typeid('smiley', $type['typeid']);
		showtablerow('', ['class="td25"', 'class="td28"'], [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$type['typeid']}\" ".($smiliesnum ? 'disabled' : '').'>',
			"<input type=\"text\" class=\"txt\" name=\"displayordernew[{$type['typeid']}]\" value=\"{$type['displayorder']}\" size=\"2\">",
			"<input class=\"checkbox\" type=\"checkbox\" name=\"availablenew[{$type['typeid']}]\" value=\"1\" ".($type['available'] ? 'checked' : '').'>',
			"<input type=\"text\" class=\"txt\" name=\"namenew[{$type['typeid']}]\" value=\"{$type['name']}\" size=\"15\">",
			$type['directory'] != ':emoji' ? "./static/image/smiley/{$type['directory']}" : '',
			"$smiliesnum<input type=\"hidden\" name=\"smiliesnum[{$type['typeid']}]\" value=\"$smiliesnum\" />",
			($type['directory'] != ':emoji' ? "<a href=\"".ADMINSCRIPT."?action=smilies&operation=update&id={$type['typeid']}\" class=\"act\" onclick=\"return confirm('{$lang['smilies_update_confirm1']}{$type['directory']}{$lang['smilies_update_confirm2']}{$type['name']}{$lang['smilies_update_confirm3']}')\">{$lang['smilies_update']}</a>&nbsp;".
			"<a href=\"".ADMINSCRIPT."?action=smilies&operation=export&id={$type['typeid']}\" class=\"act\">{$lang['export']}</a>&nbsp;" : '').
			"<a href=\"".ADMINSCRIPT."?action=smilies&operation=edit&id={$type['typeid']}\" class=\"act\">{$lang['detail']}</a>"
		]);
		$dirfilter[] = $type['directory'];
		$smtypes++;
	}

	$smdir = DISCUZ_ROOT.'./static/image/smiley';
	$smtypedir = dir($smdir);
	$dirnum = 0;
	while($entry = $smtypedir->read()) {
		if($entry != '.' && $entry != '..' && !in_array($entry, $dirfilter) && preg_match('/^\w+$/', $entry) && strlen($entry) < 30 && is_dir($smdir.'/'.$entry)) {
			$smiliesdir = dir($smdir.'/'.$entry);
			$smnums = 0;
			$smilies = '';
			while($subentry = $smiliesdir->read()) {
				if(in_array(strtolower(fileext($subentry)), $imgextarray) && preg_match('/^[\w\-\.\[\]\(\)\<\> &]+$/', substr($subentry, 0, strrpos($subentry, '.'))) && strlen($subentry) < 30 && is_file($smdir.'/'.$entry.'/'.$subentry)) {
					$smilies .= '<input type="hidden" name="smilies['.$dirnum.']['.$smnums.'][available]" value="1"><input type="hidden" name="smilies['.$dirnum.']['.$smnums.'][displayorder]" value="0"><input type="hidden" name="smilies['.$dirnum.']['.$smnums.'][url]" value="'.$subentry.'">';
					$smnums++;
				}
			}
			showtablerow('', ['class="td25"', 'class="td28"'], [
				($lang['add_new']),
				'<input type="text" class="txt" name="newdisplayorder['.$dirnum.']" value="'.($smtypes + $dirnum + 1).'" size="2" />',
				'<input class="checkbox" type="checkbox" name="newavailable['.$dirnum.']" value="1"'.($smnums ? ' checked="checked"' : ' disabled="disabled"').' />',
				'<input type="text" class="txt" name="newname['.$dirnum.']" value="" size="15" />',
				'./static/image/smiley/'.$entry.'<input type="hidden" name="newdirectory['.$dirnum.']" value="'.$entry.'">',
				"$smnums<input type=\"hidden\" name=\"smnums[$dirnum]\" value=\"$smnums\" />",
				$smilies,
				'',
				''

			]);
			$dirnum++;
		}
	}

	if(!$dirnum) {
		showtablerow('', ['', 'colspan="8"'], [
			cplang('add_new'),
			cplang('smiliesupload_tips')
		]);
	}

	showsubmit('smiliessubmit', 'submit', 'del');
	showtablefooter();
	showboxfooter();
	showformfooter();

} else {

	if(is_array($_GET['namenew'])) {
		foreach($_GET['namenew'] as $id => $val) {
			$_GET['availablenew'][$id] = $_GET['availablenew'][$id] && $_GET['smiliesnum'][$id] > 0 ? 1 : 0;
			table_forum_imagetype::t()->update($id, [
				'available' => $_GET['availablenew'][$id],
				'name' => dhtmlspecialchars(trim($val)),
				'displayorder' => $_GET['displayordernew'][$id]
			]);
		}
	}

	if($_GET['delete']) {
		if(table_common_smiley::t()->count_by_type_typeid('smiley', $_GET['delete'])) {
			cpmsg('smilies_delete_invalid', '', 'error');
		}
		table_forum_imagetype::t()->delete($_GET['delete']);
	}

	if(is_array($_GET['newname'])) {
		foreach($_GET['newname'] as $key => $val) {
			$val = trim($val);
			if($val) {
				$smurl = './static/image/smiley/'.$_GET['newdirectory'][$key];
				$smdir = DISCUZ_ROOT.$smurl;
				if(!is_dir($smdir)) {
					cpmsg('smilies_directory_invalid', '', 'error', ['smurl' => $smurl]);
				}
				$newavailable[$key] = $_GET['newavailable'][$key] && $smnums[$key] > 0 ? 1 : 0;
				$data = [
					'available' => $_GET['newavailable'][$key],
					'name' => dhtmlspecialchars($val),
					'type' => 'smiley',
					'displayorder' => $_GET['newdisplayorder'][$key],
					'directory' => $_GET['newdirectory'][$key],
				];
				$newSmileId = table_forum_imagetype::t()->insert($data, true);

				$smilies = update_smiles($smdir, $newSmileId, $imgextarray);
				if($smilies['smilies']) {
					addsmilies($newSmileId, $smilies['smilies']);
					updatecache(['smilies', 'smileycodes', 'smilies_js']);
				}
			}
		}
	}

	updatecache(['smileytypes', 'smilies', 'smileycodes', 'smilies_js']);
	cpmsg('smilies_edit_succeed', 'action=smilies', 'succeed');

}
	