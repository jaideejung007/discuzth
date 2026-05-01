<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($id)) {
	cpmsg('undefined_action');
}

$type = table_forum_imagetype::t()->fetch($id);
if($type['directory'] != ':emoji') {
	$smurl = './static/image/smiley/'.$type['directory'];
	$smdir = DISCUZ_ROOT.$smurl;
	if(!is_dir($smdir)) {
		cpmsg('smilies_directory_invalid', '', 'error', ['smurl' => $smurl]);
	}
}
$fastsmiley = table_common_setting::t()->fetch_setting('fastsmiley', true);

if(!$do) {

	if(!submitcheck('editsubmit')) {

		$smiliesperpage = 100;
		$start_limit = ($page - 1) * $smiliesperpage;

		$num = table_common_smiley::t()->count_by_type_typeid('smiley', $id);
		$multipage = multi($num, $smiliesperpage, $page, ADMINSCRIPT.'?action=smilies&operation=edit&id='.$id);

		$smileynum = 1;
		$smilies = '';
		foreach(table_common_smiley::t()->fetch_all_by_typeid_type($id, 'smiley', $start_limit, $smiliesperpage) as $smiley) {
			if($type['directory'] != ':emoji') {
				$smilies .= showtablerow('', ['class="td25"', 'class="td28 td24"', 'class="td25"', 'class="td23"', 'class="td23"', 'class="td24"'], [
					"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$smiley['id']}\">",
					"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayorder[{$smiley['id']}]\" value=\"{$smiley['displayorder']}\">",
					"<input class=\"checkbox\" type=\"checkbox\" name=\"fast[]\" ".(is_array($fastsmiley[$id]) && in_array($smiley['id'], $fastsmiley[$id]) ? 'checked="checked"' : '')." value=\"{$smiley['id']}\">",
					"<img src=\"$smurl/{$smiley['url']}\" border=\"0\" onload=\"if(this.height>30) {this.resized=true; this.height=30;}\" onmouseover=\"if(this.resized) this.style.cursor='pointer';\" onclick=\"if(!this.resized) {return false;} else {window.open(this.src);}\">",
					$smiley['id'],
					"<input type=\"text\" class=\"txt\" size=\"25\" name=\"code[{$smiley['id']}]\" value=\"".dhtmlspecialchars($smiley['code'])."\" id=\"code_$smileynum\" smileyid=\"{$smiley['id']}\" />",
					"<input type=\"hidden\" value=\"{$smiley['url']}\" id=\"url_$smileynum\">{$smiley['url']}"
				], TRUE);
			} else {
				$smilies .= showtablerow('', ['class="td25"', 'class="td28 td24"', 'class="td25"', 'class="td23"', ''], [
					"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$smiley['id']}\">",
					"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayorder[{$smiley['id']}]\" value=\"{$smiley['displayorder']}\">",
					"<input class=\"checkbox\" type=\"checkbox\" name=\"fast[]\" ".(is_array($fastsmiley[$id]) && in_array($smiley['id'], $fastsmiley[$id]) ? 'checked="checked"' : '')." value=\"{$smiley['id']}\">",
					$smiley['id'],
					"<input type=\"text\" class=\"txt\" size=\"25\" name=\"code[{$smiley['id']}]\" value=\"".dhtmlspecialchars($smiley['code'])."\" id=\"code_$smileynum\" smileyid=\"{$smiley['id']}\" />",
				], TRUE);
				$lastDisplayorder = max($lastDisplayorder, $smiley['displayorder']);
			}
			$imgfilter[] = $smiley['url'];
			$smileynum++;
		}

		echo <<<EOT
<script type="text/JavaScript">
	function addsmileycodes(smiliesnum, pre) {
		smiliesnum = parseInt(smiliesnum);
		if(smiliesnum > 1) {
			for(var i = 1; i < smiliesnum; i++) {
				var prefix = trim($(pre + 'prefix').value);
				var suffix = trim($(pre + 'suffix').value);
				var page = parseInt('$page');
				var middle = $(pre + 'middle').value == 1 ? $(pre + 'url_' + i).value.substr(0,$(pre + 'url_' + i).value.lastIndexOf('.')) : ($(pre + 'middle').value == 2 ? i + page * 10 : $(pre + 'code_'+ i).attributes['smileyid'].nodeValue);
				if(!prefix || prefix == '{$lang['smilies_prefix']}' || !suffix || suffix == '{$lang['smilies_suffix']}') {
					alert('{$lang['smilies_prefix_tips']}');
					return;
				}
				suffix = !suffix || suffix == '{$lang['smilies_suffix']}' ? '' : suffix;
				$(pre + 'code_' + i).value = prefix + middle + suffix;
			}
		}
	}
	function autoaddsmileycodes(smiliesnum) {
		smiliesnum = parseInt(smiliesnum);
		if(smiliesnum > 1) {
			for(var i = 1; i < smiliesnum; i++) {
				$('code_' + i).value = '{:' + '$id' + '_' + $('code_'+ i).attributes['smileyid'].nodeValue + ':}';
			}
		}

	}
	function clearinput(obj, defaultval) {
		if(obj.value == defaultval) {
			obj.value = '';
		}
	}
</script>
EOT;

		shownav('style', 'nav_smilies');
		showchildmenu([['menu_posting_smilies', 'smilies']], $type['name'], [
			['admin', "smilies&operation=edit&id=$id", !$do],
			$type['directory'] != ':emoji' ? ['add', "smilies&operation=edit&do=add&id=$id", $do == 'add'] : '',
		]);
		showformheader("smilies&operation=edit&id=$id");
		showhiddenfields(['page' => $_GET['page']]);
		showtableheader('', 'nobottom');
		if($type['directory'] != ':emoji') {
			showsubtitle(['', 'display_order', 'smilies_fast', 'smilies_edit_image', 'smilies_id', 'smilies_edit_code', 'smilies_edit_filename']);
		} else {
			showsubtitle(['', 'display_order', 'smilies_fast', 'smilies_id', 'smilies_edit_code']);
		}
		echo $smilies;
		if($type['directory'] != ':emoji') {
			showtablerow('', ['', 'colspan="5"'], [
				'',
				$lang['smilies_edit_add_code'].' <input type="text" class="txt" style="margin-right:0;width:40px;" size="2" value="{:" title="'.$lang['smilies_prefix'].'" id="prefix" onclick="clearinput(this, \''.$lang['smilies_prefix'].'\')" /> + <select id="middle"><option value="1">'.$lang['smilies_edit_order_file'].'</option><option value="2">'.$lang['smilies_edit_order_radom'].'</option><option value="3">'.$lang['smilies_id'].'</option></select> + <input type="text" class="txt" style="margin-right:0;width:40px;" size="2" value=":}" title="'.$lang['smilies_suffix'].'" id="suffix" onclick="clearinput(this, \''.$lang['smilies_suffix'].'\')" /> <input type="button" class="btn" onclick="addsmileycodes(\''.$smileynum.'\', \'\');" value="'.$lang['apply'].'" /> &nbsp;&nbsp; <input type="button" class="btn" onclick="autoaddsmileycodes(\''.$smileynum.'\');" value="'.$lang['smilies_edit_addcode_auto'].'" />'
			]);
		} else {
			showtablerow('', ['', 'colspan="4"'], [
				cplang('add'),
				'<input type="text" class="txt" name="addNew" style="width: 200px" value="" /><input type="hidden" name="lastDisplayorder" value="'.$lastDisplayorder.'" />'.
				cplang('smilies_emoij_tips'),
			]);
		}
		showsubmit('editsubmit', 'submit', 'del', '', $multipage);
		showtablefooter();
		showformfooter();

	} else {

		if($_GET['delete']) {
			table_common_smiley::t()->delete($_GET['delete']);
		}

		if(!empty($_GET['addNew']) && $type['directory'] == ':emoji') {
			$i = $_GET['lastDisplayorder'] ?? 0;
			foreach(mb_str_split($_GET['addNew']) as $code) {
				table_common_smiley::t()->insert(['type' => 'smiley', 'typeid' => $id, 'displayorder' => ++$i, 'code' => $code, 'url' => '']);
			}
		}

		$unsfast = [];
		if(is_array($_GET['displayorder'])) {
			foreach($_GET['displayorder'] as $key => $val) {
				if(empty($_GET['fast']) || (is_array($_GET['fast']) && !in_array($key, $_GET['fast']))) {
					$unsfast[] = $key;
				}
				$_GET['displayorder'][$key] = intval($_GET['displayorder'][$key]);
				$_GET['code'][$key] = trim($_GET['code'][$key]);
				$data = ['displayorder' => $_GET['displayorder'][$key]];
				if(!empty($_GET['code'][$key])) {
					$data['code'] = $_GET['code'][$key];
				}
				table_common_smiley::t()->update($key, $data);
			}
		}

		$fastsmiley[$id] = array_diff(array_unique(array_merge((array)$fastsmiley[$id], (array)$_GET['fast'])), $unsfast);
		table_common_setting::t()->update_setting('fastsmiley', $fastsmiley);
		updatecache(['smilies', 'smileycodes', 'smilies_js']);
		cpmsg('smilies_edit_succeed', "action=smilies&operation=edit&id=$id&page={$_GET['page']}", 'succeed');

	}

} elseif($do == 'add') {

	if(!submitcheck('editsubmit')) {

		shownav('style', 'nav_smilies');
		showchildmenu([['menu_posting_smilies', 'smilies']], $type['name'], [
			['admin', "smilies&operation=edit&id=$id", 0],
			['add', "smilies&operation=edit&do=add&id=$id", $do == 'add']
		]);
		showtips('smilies_tips');
		showtagheader('div', 'addsmilies', TRUE);
		showtableheader('smilies_add', 'notop fixpadding');
		showtablerow('', '', "<span class=\"bold marginright\">{$lang['smilies_type']}:</span>{$type['name']}");
		showtablerow('', '', "<span class=\"bold marginright\">{$lang['dir']}:</span>$smurl {$lang['smilies_add_search']}");
		showtablerow('', '', '<input type="button" class="btn" value="'.$lang['search'].'" onclick="ajaxget(\''.ADMINSCRIPT.'?action=smilies&operation=edit&do=add&id='.$id.'&search=yes\', \'addsmilies\', \'addsmilies\', \'auto\');doane(event);">');
		showtablefooter();
		showtagfooter('div');
		if($_GET['search']) {

			$newid = 1;
			$newimages = '';
			$imgfilter = [];
			foreach(table_common_smiley::t()->fetch_all_by_typeid_type($id, 'smiley') as $smiley) {
				$imgfilter[] = $img['url'];
			}
			$smiliesdir = dir($smdir);
			while($entry = $smiliesdir->read()) {
				if(in_array(strtolower(fileext($entry)), $imgextarray) && !in_array($entry, $imgfilter) && preg_match('/^[\w\-\.\[\]\(\)\<\> &]+$/', substr($entry, 0, strrpos($entry, '.'))) && strlen($entry) < 30 && is_file($smdir.'/'.$entry)) {
					$newimages .= showtablerow('', ['class="td25"', 'class="td28 td24"', 'class="td23"'], [
						"<input class=\"checkbox\" type=\"checkbox\" name=\"smilies[$newid][available]\" value=\"1\" checked=\"checked\">",
						"<input type=\"text\" class=\"txt\" size=\"2\" name=\"smilies[$newid][displayorder]\" value=\"0\">",
						"<img src=\"$smurl/$entry\" border=\"0\" onload=\"if(this.height>30) {this.resized=true; this.height=30;}\" onmouseover=\"if(this.resized) this.style.cursor='pointer';\" onclick=\"if(!this.resized) {return false;} else {window.open(this.src);}\">",
						"<input type=\"hidden\" size=\"25\" name=\"smilies[$newid][url]\" value=\"$entry\" id=\"addurl_$newid\">$entry"
					], TRUE);
					$newid++;
				}
			}
			$smiliesdir->close();

			ajaxshowheader();

			if($newimages) {

				showformheader("smilies&operation=edit&do=add&id=$id");
				showtableheader('smilies_add', 'notop fixpadding');
				showsubtitle(['', 'display_order', 'smilies_edit_image', 'smilies_edit_filename']);
				echo $newimages;
				showtablerow('', ['class="td25"', 'colspan="3"'], [
					'<input type="checkbox" name="chkall" onclick="checkAll(\'prefix\', this.form, \'available\')" class="checkbox" checked="checked">'.$lang['enable'],
					'<input type="submit" class="btn" name="editsubmit" value="'.$lang['submit'].'"> &nbsp; <input type="button" class="btn" value="'.$lang['research'].'" onclick="ajaxget(\''.ADMINSCRIPT.'?action=smilies&operation=edit&do=add&id='.$id.'&search=yes\', \'addsmilies\', \'addsmilies\', \'auto\');doane(event);">'
				]);
				showtablefooter();
				showformfooter();

			} else {

				showtableheader('smilies_add', 'notop');
				showtablerow('', 'class="lineheight"', cplang('smilies_edit_add_tips', ['smurl' => $smurl]));
				showtablerow('', '', '<input type="button" class="btn" value="'.$lang['research'].'" onclick="ajaxget(\''.ADMINSCRIPT.'?action=smilies&operation=edit&do=add&id='.$id.'&search=yes\', \'addsmilies\', \'addsmilies\', \'auto\');doane(event);">');
				showtablefooter();

			}

			ajaxshowfooter();
		}

	} else {

		if(is_array($_GET['smilies'])) {
			addsmilies($id, $_GET['smilies']);
		}

		updatecache(['smilies', 'smileycodes', 'smilies_js']);
		cpmsg('smilies_edit_succeed', "action=smilies&operation=edit&id=$id", 'succeed');
	}
}
	