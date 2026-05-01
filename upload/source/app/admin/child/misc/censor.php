<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}


$ppp = 30;

$addcensors = isset($_GET['addcensors']) ? trim($_GET['addcensors']) : '';

if($do == 'export') {

	ob_end_clean();
	dheader('Cache-control: max-age=0');
	dheader('Expires: '.gmdate('D, d M Y H:i:s', TIMESTAMP - 31536000).' GMT');
	dheader('Content-Encoding: none');
	dheader('Content-Disposition: attachment; filename=CensorWords.txt');
	dheader('Content-Type: text/plain');
	foreach(table_common_word_type::t()->fetch_all_word_type() as $result) {
		$result['used'] = 0;
		$word_type[$result['id']] = $result;
	}
	foreach(table_common_word::t()->fetch_all_order_type_find() as $censor) {
		$censor['replacement'] = str_replace('*', '', $censor['replacement']) <> '' ? $censor['replacement'] : '';
		if($word_type[$censor['type']]['used'] == 0 && $word_type[$censor['type']]) {
			if($temp_type == 1) {
				echo "[/type]\n";
			}
			echo "\n[type:".$word_type[$censor['type']]['typename']."]\n";
			$temp_type = 1;
			$word_type[$censor['type']]['used'] = 1;
		}
		echo $censor['find'].($censor['replacement'] != '' ? '='.$censor['replacement'] : '')."\n";
	}
	if($temp_type == 1) {
		echo "[/type]\n";
		unset($temp_type);
	}
	define('FOOTERDISABLED', 1);
	exit();

} elseif(submitcheck('addcensorsubmit') && $addcensors != '') {
	$oldwords = [];
	if($_G['adminid'] == 1 && $_GET['overwrite'] == 2) {
		table_common_word::t()->truncate();
	} else {
		foreach(table_common_word::t()->fetch_all_word() as $censor) {
			$oldwords[md5($censor['find'])] = $censor['admin'];
		}
	}
	$typesearch = '\[type\:(.+?)\](.+?)\[\/type\]';
	preg_match_all("/($typesearch)/is", $addcensors, $wordmatch);
	$wordmatch[3][] = preg_replace("/($typesearch)/is", '', $addcensors);
	$updatecount = $newcount = $ignorecount = 0;
	foreach($wordmatch[3] as $key => $val) {
		$word_type = 0;
		if($wordmatch[2][$key] && !$wordtype_used[$key]) {
			$row = table_common_word_type::t()->fetch_by_typename($wordmatch[2][$key]);
			if(empty($row)) {
				$word_type = table_common_word_type::t()->insert(['typename' => $wordmatch[2][$key]], true);
			} else {
				$word_type = $row['id'];
			}
			$wordtype_used[$key] = 1;
		}
		$word_type = $word_type ? $word_type : 0;

		$censorarray = explode("\n", $val);
		foreach($censorarray as $censor) {
			list($newfind, $newreplace) = array_map('trim', explode('=', $censor));
			$newreplace = $newreplace <> '' ? daddslashes(str_replace("\\\'", '\'', $newreplace), 1) : '**';
			if(strlen($newfind) < 3) {
				if($newfind != '') {
					$ignorecount++;
				}
				continue;
			} elseif(isset($oldwords[md5($newfind)])) {
				if($_GET['overwrite'] && ($_G['adminid'] == 1 || $oldwords[md5($newfind)] == $_G['member']['username'])) {
					$updatecount++;
					table_common_word::t()->update_by_find($newfind, [
						'replacement' => $newreplace,
						'type' => ($word_type ? $word_type : (intval($_GET['wordtype_select']) ? intval($_GET['wordtype_select']) : 0))
					]);
				} else {
					$ignorecount++;
				}
			} else {
				$newcount++;
				table_common_word::t()->insert([
					'admin' => $_G['username'],
					'find' => $newfind,
					'replacement' => $newreplace,
					'type' => ($word_type ? $word_type : (intval($_GET['wordtype_select']) ? intval($_GET['wordtype_select']) : 0))
				]);
				$oldwords[md5($newfind)] = $_G['member']['username'];
			}
		}

	}


	updatecache('censor');
	cpmsg('censor_batch_add_succeed', 'action=misc&operation=censor&anchor=import', 'succeed', ['newcount' => $newcount, 'updatecount' => $updatecount, 'ignorecount' => $ignorecount]);

} elseif(submitcheck('wordtypesubmit')) {
	if(is_array($_GET['delete'])) {
		$_GET['delete'] = array_map('intval', (array)$_GET['delete']);
		table_common_word_type::t()->delete($_GET['delete']);
		table_common_word::t()->update_by_type($_GET['delete'], ['type' => 0]);
	}
	if(is_array($_GET['typename'])) {
		foreach($_GET['typename'] as $key => $val) {
			if(!$_GET['delete'][$key] && !empty($val)) {
				DB::update('common_word_type', ['typename' => $val], DB::field('id', $key));
			}
		}
	}
	if($_GET['newtypename']) {
		foreach($_GET['newtypename'] as $key => $val) {
			$val = trim($val);
			if(!empty($val)) {
				table_common_word_type::t()->insert(['typename' => $val]);
			}
		}
	}
	cpmsg('censor_wordtype_edit', 'action=misc&operation=censor&anchor=wordtype', 'succeed');
} elseif(!submitcheck('censorsubmit')) {
	$ftype = $ffind = null;
	if(!empty($_GET['censor_search_type'])) {
		$ftype = $_GET['censor_search_type'];
	}

	$ffind = !empty($_GET['censorkeyword']) ? $_GET['censorkeyword'] : null;
	if($_POST['censorkeyword']) {
		$page = 1;
	}

	$ppp = 50;
	$startlimit = ($page - 1) * $ppp;

	foreach(table_common_word_type::t()->fetch_all_word_type() as $result) {
		$result['typename'] = dhtmlspecialchars($result['typename']);
		$word_type[$result['id']] = $result;
		$word_type_option .= "<option value=\"{$result['id']}\">{$result['typename']}</option>";
		if(!empty($_GET['censor_search_type'])) {
			$word_type_option_search .= "<option value=\"{$result['id']}\"".($_GET['censor_search_type'] == $result['id'] ? 'selected' : '').">{$result['typename']}</option>";
		}
	}

	shownav('topic', 'nav_posting_censor');
	$anchor = in_array($_GET['anchor'], ['list', 'import', 'wordtype', 'showanchor']) ? $_GET['anchor'] : 'list';
	showsubmenuanchors('nav_posting_censor', [
		['admin', 'list', $anchor == 'list'],
		['misc_censor_batch_add', 'import', $anchor == 'import'],
		['misc_censor_wordtype_edit', 'wordtype', $anchor == 'wordtype'],
	]);
	/*search={"nav_posting_censor":"action=misc&operation=censor"}*/
	showtips('misc_censor_tips', 'list_tips', $anchor == 'list');
	showtips('misc_censor_batch_add_tips', 'import_tips', $anchor == 'import');
	showtips('misc_censor_wordtype_tips', 'wordtype_tips', $anchor == 'wordtype');
	/*search*/

	showtagheader('div', 'list', $anchor == 'list');
	showformheader("misc&operation=censor&page=$page", '', 'keywordsearch');
	showtableheader();
	echo '<tr><td>'.$lang['keywords'].': <input type="text" name="censorkeyword" value="'.$_GET['censorkeyword'].'" /> &nbsp; <select name="censor_search_type"><option value = "">'.cplang('misc_censor_wordtype_search').'</option><option value="0">'.cplang('misc_censor_word_default_typename').'</option>'.($word_type_option_search ? $word_type_option_search : $word_type_option).'</select> &nbsp;<input type="submit" name="censor_search" value="'.$lang['search'].'" class="btn" /></td></tr>';
	showtablefooter();
	showformfooter();

	showformheader("misc&operation=censor&page=$page", '', 'listform');
	showtableheader('', 'fixpadding');
	showsubtitle(['', 'misc_censor_word', 'misc_censor_replacement', 'misc_censor_type', 'operator']);

	$multipage = '';
	$totalcount = table_common_word::t()->count_by_type_find($ftype, $ffind);
	if($totalcount) {
		$multipage = multi($totalcount, $ppp, $page, ADMINSCRIPT.'?action=misc&operation=censor'.($ffind ? '&censorkeyword='.$ffind : '').($_GET['censor_search_type'] ? '&censor_search_type='.$_GET['censor_search_type'] : ''));
		foreach(table_common_word::t()->fetch_all_by_type_find($ftype, $ffind, $startlimit, $ppp) as $censor) {
			$censor['replacement'] = dhtmlspecialchars($censor['replacement']);
			$censor['find'] = dhtmlspecialchars($censor['find']);
			$disabled = $_G['adminid'] != 1 && $censor['admin'] != $_G['member']['username'] ? 'disabled' : NULL;
			if(in_array($censor['replacement'], ['{BANNED}', '{MOD}'])) {
				$replacedisplay = 'style="display:none"';
				$optionselected = [];
				foreach(['{BANNED}', '{MOD}'] as $option) {
					$optionselected[$option] = $censor['replacement'] == $option ? 'selected' : '';
				}
			} else {
				$optionselected['{REPLACE}'] = 'selected';
				$replacedisplay = '';
			}
			$word_type_tmp = "<select name='wordtype_select[{$censor['id']}]' id='wordtype_select'><option value='0'>".cplang('misc_censor_word_default_typename').'</option>';
			foreach($word_type as $key => $val) {
				if($censor['type'] == $val['id']) {
					$word_type_tmp .= "<option value='{$val['id']}' selected>{$val['typename']}</option>";
				} else {
					$word_type_tmp .= "<option value='{$val['id']}'>{$val['typename']}</option>";
				}
			}
			$word_type_tmp .= '</select>';
			showtablerow('', ['class="td25"', '', '', 'class="td26"'], [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$censor['id']}\" $disabled>",
				"<input type=\"text\" class=\"txt\" size=\"30\" name=\"find[{$censor['id']}]\" value=\"{$censor['find']}\" $disabled>",
				'<select name="replace['.$censor['id'].']" onchange="if(this.options[this.options.selectedIndex].value==\'{REPLACE}\'){$(\'divbanned'.$censor['id'].'\').style.display=\'\';$(\'divbanned'.$censor['id'].'\').value=\'\';}else{$(\'divbanned'.$censor['id'].'\').style.display=\'none\';}" '.$disabled.'>
					<option value="{BANNED}" '.$optionselected['{BANNED}'].'>'.cplang('misc_censor_word_banned').'</option><option value="{MOD}" '.$optionselected['{MOD}'].'>'.cplang('misc_censor_word_moderated').'</option><option value="{REPLACE}" '.$optionselected['{REPLACE}'].'>'.cplang('misc_censor_word_replaced').'</option></select>
					<input class="txt" type="text" size="10" name="replacecontent['.$censor['id'].']" value="'.$censor['replacement'].'" id="divbanned'.$censor['id'].'" '.$replacedisplay.' '.$disabled.'>',
				$word_type_tmp,
				$censor['admin']
			]);
		}
	}
	$misc_censor_word_banned = cplang('misc_censor_word_banned');
	$misc_censor_word_moderated = cplang('misc_censor_word_moderated');
	$misc_censor_word_replaced = cplang('misc_censor_word_replaced');
	$misc_censor_word_newtypename = cplang('misc_censor_word_newtypename');
	$misc_censor_word_default_typename = cplang('misc_censor_word_default_typename');
	echo <<<EOT
<script type="text/JavaScript">
	var rowtypedata = [
		[
			[1,''],
			[1,'<input type="text" class="txt" size="30" name="newfind[]">'], [1, ' <select onchange="if(this.options[this.options.selectedIndex].value==\'{REPLACE}\'){this.nextSibling.style.display=\'\';}else{this.nextSibling.style.display=\'none\';}" name="newreplace[]" ><option value="{BANNED}">$misc_censor_word_banned</option><option value="{MOD}">$misc_censor_word_moderated</option><option value="{REPLACE}">$misc_censor_word_replaced</option></select><input class="txt" type="text" size="15" name="newreplacecontent[]" style="display:none;">']
EOT;
	if($word_type_option) {
		echo ", [1,' <select onchange=\"if(this.options[this.options.selectedIndex].value==\'0\'){this.nextSibling.style.display=\'\';}else{this.nextSibling.style.display=\'none\';}\" name=\"newwordtype[]\" id=\"newwordtype[]\"><option value=\"0\" selected>{$misc_censor_word_default_typename}</option>{$word_type_option}</select><input class=\"txt\" type=\"text\" size=\"10\" name=\"newtypename[]\" >']";
	}
	echo <<<EOT
			, [1,'']
		],
		[
			[1,''],
			[1,'<input type="text" class="txt" size="30" name="newtypename[]">']
		]
	];
	</script>
EOT;
	echo '<tr><td></td><td colspan="4"><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['add_new'].'</a></div></td></tr>';

	showsubmit('censorsubmit', 'submit', 'del', '', $multipage, false);
	showtablefooter();
	showformfooter();
	showtagfooter('div');

	showtagheader('div', 'import', $anchor == 'import');
	showformheader("misc&operation=censor&page=$page", 'fixpadding');
	showtableheader('', 'fixpadding', 'importform');
	showtablerow('', 'class="vtop rowform"', "<select name=\"wordtype_select\"><option value='0'>".cplang('misc_censor_word_default_typename')."</option>$word_type_option</select>");
	showtablerow('', 'class="vtop rowform"', '<br /><textarea name="addcensors" class="tarea" rows="10" cols="80" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)"></textarea><br /><br />'.mradio('overwrite', [
			0 => cplang('misc_censor_batch_add_no_overwrite'),
			1 => cplang('misc_censor_batch_add_overwrite'),
			2 => cplang('misc_censor_batch_add_clear')
		], '', FALSE));

	showsubmit('addcensorsubmit');
	showtablefooter();
	showformfooter();
	showtagfooter('div');


	showtagheader('div', 'wordtype', $anchor == 'wordtype');
	showformheader('misc&operation=censor', 'fixpadding');
	showtableheader('', 'fixpadding', 'wordtypeform');
	showsubtitle(['', 'misc_censor_wordtype_name']);
	if($wordtypecount = table_common_word_type::t()->count()) {
		foreach(table_common_word_type::t()->fetch_all_word_type() as $result) {
			showtablerow('', ['class="td25"', ''], ["<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$result['id']}\" >", "<input type=\"text\" class=\"txt\" size=\"10\" name=\"typename[{$result['id']}]\" value=\"{$result['typename']}\">"]);
		}
	}

	echo '<tr><td></td><td colspan="2"><div><a href="###" onclick="addrow(this, 1)" class="addtr">'.$lang['add_new'].'</a></div></td></tr>';
	showsubmit('wordtypesubmit', 'submit', 'del', '', '', false);
	showtablefooter();
	showformfooter();
	showtagfooter('div');

} else {

	if($ids = dimplode($_GET['delete'])) {
		DB::delete('common_word', "id IN ($ids) AND ('{$_G['adminid']}'='1' OR `admin`='{$_G['username']}')");
	}

	if(is_array($_GET['find'])) {
		foreach($_GET['find'] as $id => $val) {
			$_GET['find'][$id] = $val = trim(str_replace('=', '', $_GET['find'][$id]));
			if(strlen($val) < 3) {
				cpmsg('censor_keywords_tooshort', '', 'error');
			}
			$_GET['replace'][$id] = $_GET['replace'][$id] == '{REPLACE}' ? $_GET['replacecontent'][$id] : $_GET['replace'][$id];
			$_GET['replace'][$id] = daddslashes(str_replace("\\\'", '\'', $_GET['replace'][$id]), 1);
			DB::update('common_word', [
				'find' => $_GET['find'][$id],
				'replacement' => $_GET['replace'][$id],
				'type' => $_GET['wordtype_select'][$id],
			], DB::field('id', $id)." AND ('{$_G['adminid']}'='1' OR `admin`='{$_G['username']}')");
		}
	}

	$newfind_array = !empty($_GET['newfind']) ? $_GET['newfind'] : [];
	$newreplace_array = !empty($_GET['newreplace']) ? $_GET['newreplace'] : [];
	$newreplacecontent_array = !empty($_GET['newreplacecontent']) ? $_GET['newreplacecontent'] : [];
	$newwordtype = !empty($_GET['newwordtype']) ? $_GET['newwordtype'] : [];
	$newtypename = !empty($_GET['newtypename']) ? $_GET['newtypename'] : [];

	foreach($newfind_array as $key => $value) {
		$newfind = trim(str_replace('=', '', $newfind_array[$key]));
		$newreplace = trim($newreplace_array[$key]);

		if($newfind != '') {
			if(strlen($newfind) < 3) {
				cpmsg('censor_keywords_tooshort', '', 'error');
			}
			if($newreplace == '{REPLACE}') {
				$newreplace = daddslashes(str_replace("\\\'", '\'', $newreplacecontent_array[$key]), 1);
			}

			if($newtypename) {
				$newtypename = daddslashes($newtypename);
			}

			if($newwordtype) {
				$newwordtype[$key] = intval($newwordtype[$key]);
			}

			if($newwordtype[$key] == 0) {
				if(!empty($newtypename[$key])) {
					$newwordtype[$key] = table_common_word_type::t()->insert(['typename' => $newtypename[$key]], true);
				}
			}
			if($oldcenser = table_common_word::t()->fetch_by_find($newfind)) {
				cpmsg('censor_keywords_existence', '', 'error');
			} else {
				table_common_word::t()->insert([
					'admin' => $_G['username'],
					'find' => $newfind,
					'replacement' => $newreplace,
					'type' => $newwordtype[$key],
				]);
			}
		}
	}

	updatecache('censor');
	cpmsg('censor_succeed', "action=misc&operation=censor&page=$page", 'succeed');

}
	