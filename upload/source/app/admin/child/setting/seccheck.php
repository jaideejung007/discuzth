<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('settingsubmit')) {
	if(is_array($_GET['delete'])) {
		table_common_secquestion::t()->delete($_GET['delete']);
	}

	if(is_array($_GET['question'])) {
		foreach($_GET['question'] as $key => $q) {
			$q = trim($q);
			$a = cutstr(dhtmlspecialchars(trim($_GET['answer'][$key])), 50);
			if($q !== '' && $a !== '') {
				table_common_secquestion::t()->update($key, ['question' => $q, 'answer' => $a]);
			}
		}
	}
	table_common_secquestion::t()->delete_by_type(1);
	if(is_array($_GET['secqaaext'])) {
		foreach($_GET['secqaaext'] as $ext) {
			if(preg_match('/^[\w\_:]+$/', $ext)) {
				DB::insert('common_secquestion', ['type' => '1', 'question' => $ext]);
			}
		}
	}

	if(is_array($_GET['newquestion']) && is_array($_GET['newanswer'])) {
		foreach($_GET['newquestion'] as $key => $q) {
			$q = trim($q);
			$a = cutstr(dhtmlspecialchars(trim($_GET['newanswer'][$key])), 50);
			if($q !== '' && $a !== '') {
				DB::insert('common_secquestion', ['question' => $q, 'answer' => $a]);
			}
		}
	}

	$setting['secqaa'] = dunserialize($setting['secqaa']);
	$setting['secqaa']['statuses'] = $settingnew['secqaa']['statuses'];
	$setting['secqaa']['minposts'] = intval($settingnew['secqaa']['minposts']);
	$setting['secqaa']['allowcode'] = intval($settingnew['secqaa']['allowcode']);
	$setting['secqaa']['allowqa'] = intval($settingnew['secqaa']['allowqa']);
	$_G['setting']['secqaa'] = $setting['secqaa'];
	$settingnew['secqaa'] = serialize($setting['secqaa']);
	updatecache('secqaa');
} else {

	$setting['seccodedata'] = dunserialize($setting['seccodedata']);
	$setting['secqaa'] = dunserialize($setting['secqaa']);

	if(!isset($setting['secqaa']['statuses'])) {
		$setting['secqaa']['statuses'] = [];
		$setting['secqaa']['status'] & 1 && $setting['secqaa']['statuses'][] = 'register';
		$setting['secqaa']['status'] & 2 && $setting['secqaa']['statuses'][] = 'post';
		$setting['secqaa']['status'] & 8 && $setting['secqaa']['statuses'][] = 'login';
		$setting['secqaa']['status'] & 16 && $setting['secqaa']['statuses'][] = 'card';

		$setting['seccodedata']['rule']['register']['allow'] == 2 && $setting['secqaa']['rule']['register']['auto'] = 1;
		$setting['seccodedata']['rule']['login']['allow'] == 2 && $setting['secqaa']['rule']['login']['auto'] = 1;
		$setting['seccodedata']['rule']['post']['allow'] == 2 && $setting['secqaa']['rule']['post']['auto'] = 1;

		table_common_setting::t()->update_setting('secqaa', $setting['secqaa']);
		updatecache('setting');
	}

	shownav('safe', 'setting_seccheck');

	showchildmenu([], cplang('setting_seccheck'));

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	echo <<<EOT
	<script type="text/JavaScript">
		var rowtypedata = [
			[[1,'<input class="checkbox" type="checkbox" disabled /> <input name="newquestion[]" type="text" class="txt">','td26'], [1, '<input name="newanswer[]" type="text" class="txt">','td24']],
		];
		</script>
	EOT;
	/*search={"setting_seccheck":"action=setting&operation=sec","setting_sec_secqaa":"action=setting&operation=sec"}*/
	showtips('setting_sec_qaa_tips', 'secqaa_tips');
	showtableheader('', 'nobottom', 'style="width: 500px"');

	showsubtitle(['setting_sec_scene_name', 'setting_sec_seccode_rule_setting', '']);
	$sectypes = [
		'register' => [true, cplang('setting_sec_seccode_status_register')],
		'post' => [true, cplang('setting_sec_seccode_status_post')],
		'login' => [true, cplang('setting_sec_seccode_status_login')],
		'card' => [false, cplang('setting_sec_seccode_status_card')],
	];
	$sectypes += getsecchecks();
	foreach($sectypes as $sectype => $value) {
		list($haverule, $name, $url, $copyright) = $value;
		$url = $url ? ADMINSCRIPT.'?'.$url : ADMINSCRIPT.'?action=setting&operation=seccheck_rule&do='.$sectype;
		$checked = in_array($sectype, (array)$setting['secqaa']['statuses']) ? ' checked' : '';
		showtablerow('class="hover"', ['class="td26"'], [
			'<label><input class="checkbox" type="checkbox" name="settingnew[secqaa][statuses][]" value="'.$sectype.'"'.$checked.'> '.$name.'</label>',
			$haverule ? '<a href="'.$url.'" class="operation">'.cplang('edit').'</a>' : '',
			$copyright ? $copyright : '',
		]);
	}
	showtablefooter();

	showtableheader('', 'nobottom');
	showsetting('setting_sec_secqaa_minposts', 'settingnew[secqaa][minposts]', $setting['secqaa']['minposts'], 'text');
	showtablefooter();

	showtableheader('setting_sec_secqaa_qaa', 'nobottom', 'style="margin-bottom: 0; width: 60%"');
	showsubtitle(['name', '']);

	$allowcode = !empty($setting['secqaa']['allowcode']) ? 'checked' : '';
	echo showtablerow('class="hover"', [], [
		'<a style="float:right" href="'.ADMINSCRIPT.'?action=setting&operation=seccheck_code" target="_blank">'.cplang('edit').'</a>'.
		'<label><input class="checkbox" type="checkbox" value="1" name="settingnew[secqaa][allowcode]" '.$allowcode.' /> '.cplang('setting_sec_seccode').'</label>',
		'',
	], true);

	$qaaext = [];
	$items = table_common_secquestion::t()->fetch_all_secquestion();
	foreach($items as $item) {
		if($item['type']) {
			$qaaext[] = $item['question'];
		}
	}
	echo getsecqaas($qaaext);
	$allowqa = !empty($setting['secqaa']['allowqa']) ? 'checked' : '';

	echo showtablerow('class="hover"', ['class="td26"'], [
		'<label><input class="checkbox" type="checkbox" value="1" onclick="$(\'qalist\').style.display = this.checked ? \'\' : \'none\'" name="settingnew[secqaa][allowqa]" '.$allowqa.' /><b>'.cplang('setting_sec_secqaa_question').'</b></label>',
		'',
	], true);
	showtablefooter();

	showtableheader('', 'nobottom', 'id="qalist" style="width: 55%; margin-bottom: 0; margin-left: 20px; display: '.($allowqa ? '' : 'none').'"');
	showsubtitle(['<input type="checkbox" name="chkall" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'delete\')" title="'.cplang('del').'"> '.cplang('setting_sec_secqaa_q'), 'setting_sec_secqaa_answer']);
	foreach($items as $item) {
		if(!$item['type']) {
			showtablerow('class="hover"', ['class="td26"', 'class="td24"'], [
				'<input class="checkbox" type="checkbox" name="delete[]" value="'.$item['id'].'"> '.
				'<input type="text" class="txt" name="question['.$item['id'].']" value="'.dhtmlspecialchars($item['question']).'" class="txtnobd" onblur="this.className=\'txtnobd\'" onfocus="this.className=\'txt\'">',
				'<input type="text" class="txt" name="answer['.$item['id'].']" value="'.$item['answer'].'" class="txtnobd" onblur="this.className=\'txtnobd\'" onfocus="this.className=\'txt\'">'
			]);
		}
	}
	echo '<tr><td><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['setting_sec_secqaa_add'].'</a></div></td><td></td></tr>';
	/*search*/
	showtablefooter();

	showtableheader();
	showsubmit('settingsubmit', 'submit');
	showtablefooter();
	showformfooter();
}