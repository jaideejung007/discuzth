<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('advsubmit')) {

	if($operation == 'edit') {
		$advid = $_GET['advid'];
		$adv = table_common_advertisement::t()->fetch($advid);
		if(!$adv) {
			cpmsg('advertisement_nonexistence', '', 'error');
		}
		$adv['parameters'] = dunserialize($adv['parameters']);
		$type = $adv['type'];
	} else {
		$adv['parameters']['style'] = 'code';
		$type = $_GET['type'];
	}

	$etype = explode(':', $type);
	if(count($etype) > 1 && preg_match('/^[\w\_:]+$/', $type)) {
		include_once DISCUZ_PLUGIN($etype[0]).'/adv/adv_'.$etype[1].'.php';
		$advclass = 'adv_'.$etype[1];
	} else {
		require_once libfile('adv/'.$type, 'class');
		$advclass = 'adv_'.$type;
	}
	$advclass = new $advclass;
	$advsetting = $advclass->getsetting();
	$advtitle = lang('adv/'.$type, $advclass->name).($type != 'custom' ? '' : ' '.$advclass->customname);
	$returnurl = 'adv&operation=ad'.(empty($_GET['from']) ? '&type='.$type.($type != 'custom' ? '' : '&customid='.$_GET['customid']) : '');

	shownav('extended', 'adv_admin');
	showchildmenu([['adv_admin', 'adv'], [!empty($_GET['from']) ? cplang('adv_admin_listall') : $advtitle.' ', $returnurl]], ($operation == 'edit' ? $adv['title'] : cplang('adv_add')));

	echo '<br />';

	$targets = [];
	foreach($advclass->targets as $target) {
		if($target != 'custom') {
			$targets[] = [$target, $lang['adv_edit_targets_'.$target]];
		} else {
			$ets = explode("\t", $adv['targets']);
			$customv = [];
			foreach($ets as $et) {
				if(!in_array($et, $advclass->targets)) {
					$customv[] = $et;
				}
			}
			$targets[] = [$target, '<input title="'.cplang('adv_custom_target').'" name="advnew[targetcustom]" value="'.implode(',', $customv).'" />'];
		}
	}
	$imagesizes = '';
	if(!empty($advclass->imagesizes)) {
		foreach($advclass->imagesizes as $size) {
			$imagesizes .= '<option value="'.$size.'">'.$size.'</option>';
		}
	}

	$adv['starttime'] = $adv['starttime'] ? dgmdate($adv['starttime'], 'Y-n-j') : '';
	$adv['endtime'] = $adv['endtime'] ? dgmdate($adv['endtime'], 'Y-n-j') : '';

	echo '<script type="text/javascript" src="'.STATICURL.'js/calendar.js"></script>'.
		'<div class="colorbox"><h4>'.lang('adv/'.$type, $advclass->name).'</h4>'.
		'<table cellspacing="0" cellpadding="3"><tr><td>'.
		(count($etype) > 1 && preg_match('/^[\w\_:]+$/', $type) ? (file_exists(DISCUZ_PLUGIN($etype[0]).'/adv/adv_'.$etype[1].'.gif') ? '<img src="source/plugin/'.$etype[0].'/adv/adv_'.$etype[1].'.gif" />' : '')
			: (file_exists(DISCUZ_ROOT.'./static/image/admincp/'.$type.'.gif') ? '<img src="static/image/admincp/'.$type.'.gif" />' : '')).
		'</td><td valign="top">'.lang('adv/'.$type, $advclass->description).'</td></tr></table>'.
		'<div style="width:95%" align="right">'.lang('adv/'.$type, $advclass->copyright).'</div></div>';
	if($operation == 'edit') {
		echo '<input type="button" class="btn" onclick="$(\'previewbtn\').click()" name="jspreview" value="'.$lang['preview'].'">';
		echo '<div class="jswizard" id="advpreview" style="display:none"><iframe id="preview" name="preview" frameborder="0" allowtransparency="true" onload="this.style.height = (this.contentWindow.document.body.scrollHeight + 50) + \'px\'" width="95%" height="0"></iframe></div>';
	}

	showformheader("adv&operation=$operation".($operation == 'add' ? '&type='.$type : '&advid='.$advid), 'enctype');
	if($type == 'custom') {
		showhiddenfields(['parameters[extra][customid]' => $_GET['customid']]);
	}
	showhiddenfields(['referer' => $returnurl]);
	showtableheader();
	showtableheader('', 'fixpadding');
	showsetting('adv_edit_title', 'advnew[title]', $adv['title'], 'text');
	if($type != 'custom') {
		showsetting('adv_edit_targets', ['advnew[targets]', $targets], explode("\t", $adv['targets']), 'mcheckbox');
	}

	if(is_array($advsetting)) {
		foreach($advsetting as $settingvar => $setting) {
			if(!empty($setting['value']) && is_array($setting['value'])) {
				foreach($setting['value'] as $k => $v) {
					$setting['value'][$k][1] = lang('adv/'.$type, $setting['value'][$k][1]);
				}
			}
			$varname = in_array($setting['type'], ['mradio', 'mcheckbox', 'select', 'mselect']) ?
				($setting['type'] == 'mselect' ? ['parameters[extra]['.$settingvar.'][]', $setting['value']] : ['parameters[extra]['.$settingvar.']', $setting['value']])
				: 'parameters['.$settingvar.']';
			$value = $adv['parameters']['extra'][$settingvar] != '' ? $adv['parameters']['extra'][$settingvar] : $setting['default'];
			$comment = lang('adv/'.$type, $setting['title'].'_comment');
			$comment = $comment != $setting['title'].'_comment' ? $comment : '';
			showsetting(lang('adv/'.$type, $setting['title']).':', $varname, $value, $setting['type'], '', 0, $comment);
		}
	}

	$adtypearray = [];
	$adtypes = ['code', 'text', 'image', 'flash'];
	foreach($adtypes as $adtype) {
		$displayary = [];
		foreach($adtypes as $adtype1) {
			$displayary['style_'.$adtype1] = $adtype1 == $adtype ? '' : 'none';
		}
		$adtypearray[] = [$adtype, $lang['adv_style_'.$adtype], $displayary];
	}
	showsetting('adv_edit_starttime', 'advnew[starttime]', $adv['starttime'], 'calendar');
	showsetting('adv_edit_endtime', 'advnew[endtime]', $adv['endtime'], 'calendar');
	showsetting('adv_edit_style', ['advnew[style]', $adtypearray], $adv['parameters']['style'], 'mradio');

	showtagheader('tbody', 'style_code', $adv['parameters']['style'] == 'code');
	showtitle('adv_edit_style_code');
	showsetting('adv_edit_style_code_html', 'advnew[code][html]', $adv['parameters']['html'], 'textarea');
	showtagfooter('tbody');

	showtagheader('tbody', 'style_text', $adv['parameters']['style'] == 'text');
	showtitle('adv_edit_style_text');
	showsetting('adv_edit_style_text_title', 'advnew[text][title]', $adv['parameters']['title'], 'htmltext');
	showsetting('adv_edit_style_text_link', 'advnew[text][link]', $adv['parameters']['link'], 'text');
	showsetting('adv_edit_style_text_size', 'advnew[text][size]', $adv['parameters']['size'], 'text');
	showtagfooter('tbody');

	showtagheader('tbody', 'style_image', $adv['parameters']['style'] == 'image');
	showtitle('adv_edit_style_image');
	showsetting('adv_edit_style_image_url', 'advnewimage', $adv['parameters']['url'], 'filetext');
	showsetting('adv_edit_style_image_link', 'advnew[image][link]', $adv['parameters']['link'], 'text');
	showsetting('adv_edit_style_image_alt', 'advnew[image][alt]', $adv['parameters']['alt'], 'text');
	if($imagesizes) {
		$v = $adv['parameters']['width'].'x'.$adv['parameters']['height'];
		showsetting('adv_edit_style_image_size', '', '', '<select onchange="setsize(this.value, \'image\')"><option value="x">'.cplang('adv_edit_style_custom').'</option>'.str_replace('"'.$v.'"', '"'.$v.'" selected="selected"', $imagesizes).'</select>');
	}
	showsetting('adv_edit_style_image_width', 'advnew[image][width]', $adv['parameters']['width'], 'text', '', 0, '', 'id="imagewidth" onchange="setpreview(\'image\')"');
	showsetting('adv_edit_style_image_height', 'advnew[image][height]', $adv['parameters']['height'], 'text', '', 0, '', 'id="imageheight" onchange="setpreview(\'image\')"');
	showtagfooter('tbody');

	showtagheader('tbody', 'style_flash', $adv['parameters']['style'] == 'flash');
	showtitle('adv_edit_style_flash');
	showsetting('adv_edit_style_flash_url', 'advnewflash', $adv['parameters']['url'], 'filetext');
	if($imagesizes) {
		$v = $adv['parameters']['flash'].'x'.$adv['parameters']['flash'];
		showsetting('adv_edit_style_flash_size', '', '', '<select onchange="setsize(this.value, \'flash\')"><option>'.cplang('adv_edit_style_custom').'</option>'.str_replace('"'.$v.'"', '"'.$v.'" selected="selected"', $imagesizes).'</select>');
	}
	showsetting('adv_edit_style_flash_width', 'advnew[flash][width]', $adv['parameters']['width'], 'text', '', 0, '', 'id="flashwidth" onchange="setpreview(\'flash\')"');
	showsetting('adv_edit_style_flash_height', 'advnew[flash][height]', $adv['parameters']['height'], 'text', '', 0, '', 'id="flashheight" onchange="setpreview(\'flash\')"');
	showtagfooter('tbody');

	echo '<tr><td colspan="2">';
	if($operation == 'edit') {
		echo '<input id="previewbtn" type="button" class="btn" onclick="$(\'advpreview\').style.display=\'\';this.form.preview.value=1;this.form.target=\'preview\';this.form.submit();" name="jspreview" value="'.$lang['preview'].'">&nbsp; &nbsp;';
	}
	echo '<input type="submit" class="btn" name="advsubmit" onclick="this.form.preview.value=0;this.form.target=\'\'" value="'.$lang['submit'].'"><input name="preview" type="hidden" value="0"></td></tr>';
	showtablefooter();
	showtableheader();
	echo '<tr><td colspan="2" id="imagesizepreviewtd" style="border:0"><div id="imagesizepreview" style="display:none;border:1px dotted gray"></div></td></tr>';
	echo '<tr><td colspan="2" id="flashsizepreviewtd" style="border:0"><div id="flashsizepreview" style="display:none;border:1px dotted gray"></div></td></tr>';
	showtablefooter();
	showformfooter();
	echo '<script type="text/JavaScript">
		function setsize(v, o) {
			if(v) {
				var size = v.split(\'x\');
				$(o + \'width\').value = size[0];
				$(o + \'height\').value = size[1];
			}
			setpreview(o);
		}
		function setpreview(o) {
			var w = $(o + \'width\').value > 0 ? $(o + \'width\').value : 0;
			var h = $(o + \'height\').value > 0 ? $(o + \'height\').value : 0;
			var obj = $(o + \'sizepreview\');
			var tdobj = $(o + \'sizepreviewtd\');
			obj.style.display = \'\';
			obj.style.width = w + \'px\';
			obj.style.height = h + \'px\';
			tdobj.style.height = (parseInt(h) + 10) + \'px\';
		}';
	if($operation == 'edit' && ($adv['parameters']['style'] == 'image' || $adv['parameters']['style'] == 'flash')) {
		echo 'setpreview(\''.$adv['parameters']['style'].'\');';
	}
	echo '</script>';

} else {

	if($operation == 'edit') {
		$advid = $_GET['advid'];
		$adv = table_common_advertisement::t()->fetch($advid);
		$type = $adv['type'];
		$adv['parameters'] = dunserialize($adv['parameters']);
	} else {
		$type = $_GET['type'];
	}

	$etype = explode(':', $type);
	if(count($etype) > 1 && preg_match('/^[\w\_:]+$/', $type)) {
		include_once DISCUZ_PLUGIN($etype[0]).'/adv/adv_'.$etype[1].'.php';
		$advclass = 'adv_'.$etype[1];
	} else {
		require_once libfile('adv/'.$type, 'class');
		$advclass = 'adv_'.$type;
	}
	$advclass = new $advclass;

	$advnew = $_GET['advnew'];

	$parameters = !empty($_GET['parameters']) ? $_GET['parameters'] : [];
	if(is_array($advnew['targets']) && in_array('custom', $advnew['targets'])) {
		$targetcustom = explode(',', $advnew['targetcustom']);
		$advnew['targets'] = array_merge($advnew['targets'], $targetcustom);
	}
	$advclass->setsetting($advnew, $parameters);

	$advnew['starttime'] = $advnew['starttime'] ? strtotime($advnew['starttime']) : 0;
	$advnew['endtime'] = $advnew['endtime'] ? strtotime($advnew['endtime']) : 0;

	if(!$advnew['title']) {
		cpmsg('adv_title_invalid', '', 'error');
	} elseif(strlen($advnew['title']) > 50) {
		cpmsg('adv_title_more', '', 'error');
	} elseif($advnew['endtime'] && ($advnew['endtime'] <= TIMESTAMP || $advnew['endtime'] <= $advnew['starttime'])) {
		cpmsg('adv_endtime_invalid', '', 'error');
	} elseif(($advnew['style'] == 'code' && !$advnew['code']['html'])
		|| ($advnew['style'] == 'text' && (!$advnew['text']['title'] || !$advnew['text']['link']))
		|| ($advnew['style'] == 'image' && (!$_FILES['advnewimage'] && !$_GET['advnewimage'] || !$advnew['image']['link']))
		|| ($advnew['style'] == 'flash' && (!$_FILES['advnewflash'] && !$_GET['advnewflash'] || !$advnew['flash']['width'] || !$advnew['flash']['height']))) {
		cpmsg('adv_parameter_invalid', '', 'error');
	}

	if($operation == 'add') {
		$advid = table_common_advertisement::t()->insert(['available' => 1, 'type' => $type], true);
	}

	if($advnew['style'] == 'image' || $advnew['style'] == 'flash') {
		if($_FILES['advnew'.$advnew['style']]) {
			$upload = new discuz_upload();
			if($upload->init($_FILES['advnew'.$advnew['style']], 'common') && $upload->save(1)) {
				$advnew[$advnew['style']]['url'] = (!str_contains($_G['setting']['attachurl'], '://') ? $_G['siteurl'] : '').$_G['setting']['attachurl'].'common/'.$upload->attach['attachment'];
			}
		} else {
			$advnew[$advnew['style']]['url'] = $_GET['advnew'.$advnew['style']];
		}
	}

	$advnew['displayorder'] = isset($advnew['displayorder']) ? implode("\t", $advnew['displayorder']) : '';
	$advnew['code'] = encodeadvcode($advnew);

	$extra = $type != 'custom' ? '' : '&customid='.$parameters['extra']['customid'];

	$advnew['parameters'] = serialize(array_merge(is_array($parameters) ? $parameters : [], ['style' => $advnew['style']], $advnew['style'] == 'code' ? [] : $advnew[$advnew['style']], ['html' => $advnew['code']], ['displayorder' => $advnew['displayorder']]));

	table_common_advertisement::t()->update($advid, [
		'title' => $advnew['title'],
		'targets' => $advnew['targets'],
		'parameters' => $advnew['parameters'],
		'code' => $advnew['code'],
		'starttime' => $advnew['starttime'],
		'endtime' => $advnew['endtime']
	]);

	updatecache('advs');
	updatecache('setting');

	cpmsg('adv_succeed', 'action=adv&operation=edit&advid='.$advid.$extra, 'succeed');

}
	