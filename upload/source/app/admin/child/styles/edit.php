<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('editsubmit')) {

	if(empty($id)) {
		$stylelist = "<select name=\"id\" style=\"width: 150px\">\n";
		foreach(table_common_style::t()->fetch_all_data() as $style) {
			$stylelist .= "<option value=\"{$style['styleid']}\">{$style['name']}</option>\n";
		}
		$stylelist .= '</select>';
		$highlight = getgpc('highlight');
		$highlight = !empty($highlight) ? dhtmlspecialchars($highlight, ENT_QUOTES) : '';
		cpmsg('styles_nonexistence', 'action=styles&operation=edit'.(!empty($highlight) ? "&highlight=$highlight" : ''), 'form', [], $stylelist);
	}

	$style = table_common_style::t()->fetch_by_styleid($id);
	if(!$style) {
		cpmsg('style_not_found', '', 'error');
	}
	list($style['extstyle'], $style['defaultextstyle']) = explode('|', $style['extstyle']);
	$style['extstyle'] = explode("\t", $style['extstyle']);

	$extstyle = $defaultextstyle = [];
	if(file_exists($extstyledir = DISCUZ_ROOT.$style['directory'].'/style')) {
		$defaultextstyle[] = ['', $lang['default']];
		$tpl = dir($extstyledir);
		while($entry = $tpl->read()) {
			if($entry != '.' && $entry != '..' && file_exists($extstylefile = $extstyledir.'/'.$entry.'/style.css')) {
				$content = file_get_contents($extstylefile);
				if(preg_match('/\[name\](.+?)\[\/name\]/i', $content, $r1) && preg_match('/\[iconbgcolor](.+?)\[\/iconbgcolor]/i', $content, $r2)) {
					$extstyle[] = [$entry, '<em style="background:'.$r2[1].'">&nbsp;&nbsp;&nbsp;&nbsp;</em> '.$r1[1]];
					$defaultextstyle[] = [$entry, $r1[1]];
				}
			}
		}
		$tpl->close();
	}

	$stylecustom = '';
	$stylestuff = $existvars = $stylecustomvars = [];
	foreach(table_common_stylevar::t()->fetch_all_by_styleid($id) as $stylevar) {
		if(array_key_exists($stylevar['variable'], $predefinedvars)) {
			$stylestuff[$stylevar['variable']] = ['id' => $stylevar['stylevarid'], 'subst' => $stylevar['substitute']];
			$existvars[] = $stylevar['variable'];
		} else {
			if($stylevar['variable'] == 'version') {
				continue;
			}
			$key = strtoupper($stylevar['variable']);
			if(str_ends_with($key, 'IMG')) {
				$varname = "stylevar[{$stylevar['stylevarid']}]";
				$value = $stylevar['substitute'];
				$defaulttype = $value ? 1 : 0;
				$_id = 'file'.random(2);
				$valueHtml = '<div style="max-width:600px;width:450px"><input id="'.$_id.'_0" style="display:'.($defaulttype ? 'none' : '').'" name="'.($defaulttype ? 'TMP' : '').$varname.'" value="" type="file" class="txt uploadbtn marginbot" />'.
					'<input id="'.$_id.'_1" style="display:'.(!$defaulttype ? 'none' : '').'" name="'.(!$defaulttype ? 'TMP' : '').$varname.'" value="'.dhtmlspecialchars($value).'" type="text" class="txt marginbot" /><br />'.
					'<a id="'.$_id.'_0a" style="'.(!$defaulttype ? 'font-weight:bold' : '').'" href="javascript:;" onclick="$(\''.$_id.'_1a\').style.fontWeight = \'\';this.style.fontWeight = \'bold\';$(\''.$_id.'_1\').name = \'TMP'.$varname.'\';$(\''.$_id.'_0\').name = \''.$varname.'\';$(\''.$_id.'_0\').style.display = \'\';$(\''.$_id.'_1\').style.display = \'none\'">'.cplang('switch_upload').'</a>&nbsp;'.
					'<a id="'.$_id.'_1a" style="'.($defaulttype ? 'font-weight:bold' : '').'" href="javascript:;" onclick="$(\''.$_id.'_0a\').style.fontWeight = \'\';this.style.fontWeight = \'bold\';$(\''.$_id.'_0\').name = \'TMP'.$varname.'\';$(\''.$_id.'_1\').name = \''.$varname.'\';$(\''.$_id.'_1\').style.display = \'\';$(\''.$_id.'_0\').style.display = \'none\'">'.cplang('switch_url').'</a>';
				if($value) {
					$valueHtml .= '<br /><img src="'.$value.'" />';
				}
				$valueHtml .= '</div>';
			} else {
				$valueHtml = "<textarea name=\"stylevar[{$stylevar['stylevarid']}]\" style=\"height: 45px\" cols=\"50\" rows=\"2\">{$stylevar['substitute']}</textarea>";
			}
			$stylecustom .= showtablerow('', ['class="td25"', 'class="td24 bold"', 'class="td26"'], [
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$stylevar['stylevarid']}\">",
				'{'.$key.'}',
				$valueHtml,
			], TRUE);
			$stylecustomvars[$stylevar['stylevarid']] = $stylevar;
		}
	}
	if($diffvars = array_diff(array_keys($predefinedvars), $existvars)) {
		foreach($diffvars as $variable) {
			$stylestuff[$variable] = [
				'id' => table_common_stylevar::t()->insert(['styleid' => $id, 'variable' => $variable, 'substitute' => ''], true),
				'subst' => ''
			];
		}
	}

	$tplselect = [];
	foreach(table_common_template::t()->fetch_all_data() as $template) {
		$tplselect[] = [$template['templateid'], $template['name']];
	}

	$smileytypes = [];
	foreach(table_forum_imagetype::t()->fetch_all_available() as $type) {
		$smileytypes[] = [$type['typeid'], $type['name']];
	}

	$adv = !empty($_GET['adv']) ? 1 : 0;

	$stylevars = $submenuitem = [];
	$submenuitem[] = ['styles_setting_basic', '_default', empty($_GET['anchor'])];
	$custom = $customMenus = false;
	$anchorCount = 0;
	foreach(table_common_stylevar_extra::t()->fetch_all_visible_by_styleid($id) as $var) {
		$stylevars[$var['variable']] = $var;
		if(str_starts_with($var['type'], 'style')) {
			$custom = true;
		}
		if($var['type'] == 'stylePage') {
			$customMenus = true;
			$submenuitem[] = [$lang[$var['title']] ?? dhtmlspecialchars($var['title']),
				$var['variable'], $_GET['anchor'] == $var['variable']];
			$anchorCount++;
		}
	}

	if(!$customMenus && $stylevars) {
		$submenuitem[] = ['plugins_config', 'config', !empty($_GET['anchor'])];
	}

	shownav('template', 'styles_edit');

	showchildmenu([['styles_admin', 'styles']], $style['name'], $submenuitem, '', true);

	?>
	<script type="text/JavaScript">
		function imgpre_onload(obj) {
			if (!obj.complete) {
				setTimeout(function () {
					imgpre_resize(obj)
				}, 100);
			}
			imgpre_resize(obj);
		}

		function imgpre_resize(obj) {
			if (obj.width > 350) {
				obj.style.width = '350px';
			}
		}

		function imgpre_update(id, obj) {
			url = obj.value;
			if (url) {
				re = /^(https?:)?\/\//i;
				var matches = re.exec(url);
				if (matches == null) {
					url = ($('styleimgdir').value ? $('styleimgdir').value : ($('imgdir').value ? $('imgdir').value : '<?php echo STATICURL; ?>image/common')) + '/' + url;
				}
				$('bgpre_' + id).style.backgroundImage = 'url(' + url + ')';
			} else {
				$('bgpre_' + id).style.backgroundImage = 'url(<?php echo STATICURL; ?>image/common/none.gif)';
			}
		}

		function imgpre_switch(id) {
			if ($('bgpre_' + id).innerHTML == '') {
				url = $('bgpre_' + id).style.backgroundImage.substring(4, $('bgpre_' + id).style.backgroundImage.length - 1);
				$('bgpre_' + id).innerHTML = '<img onload="imgpre_onload(this)" src="' + url + '" />';
				$('bgpre_' + id).backgroundImage = $('bgpre_' + id).style.backgroundImage;
				$('bgpre_' + id).style.backgroundImage = '';
			} else {
				$('bgpre_' + id).style.backgroundImage = $('bgpre_' + id).backgroundImage;
				$('bgpre_' + id).innerHTML = '';
			}
		}
	</script>
	<?php

	//是否有自定义配置文件
	$configflag = false;
	if(preg_match('/^.?\/template\/([a-z]+[a-z0-9_]*)$/', $style['directory'], $a)) {
		$configfile = DISCUZ_TEMPLATE($a[1]).'/config.inc.php';
		if(file_exists($configfile)) {
			$configflag = true;
			include $configfile;
		}
	}

	if(!$configflag) {

		/*search={"styles_admin":"action=styles&operation=edit"}*/
		showformheader("styles&operation=edit&id=$id", 'enctype');

		echo '<div id="_default"'.(empty($_GET['anchor']) ? '' : ' style="display:none"').'>';

		//echo '<iframe class="preview" frameborder="0" src="'.ADMINSCRIPT.'?action=styles&preview=yes&styleid='.$id.'"></iframe>';
		//showtips('styles_tips');

		showtableheader($lang['styles_edit'], 'nobottom');
		showsetting('styles_edit_name', 'namenew', $style['name'], 'text');
		showsetting('styles_edit_tpl', ['templateidnew', $tplselect], $style['templateid'], 'select');
		if($extstyle) {
			showsetting('styles_edit_extstyle', ['extstylenew', $extstyle], $style['extstyle'], 'mcheckbox');
			showsetting('styles_edit_defaultextstyle', ['defaultextstylenew', $defaultextstyle], $style['defaultextstyle'], 'select');
		}
		showsetting('styles_edit_smileytype', ["stylevar[{$stylestuff['stypeid']['id']}]", $smileytypes], $stylestuff['stypeid']['subst'], 'select');
		showsetting('styles_edit_imgdir', '', '', '<input type="text" class="txt" name="stylevar['.$stylestuff['imgdir']['id'].']" id="imgdir" value="'.$stylestuff['imgdir']['subst'].'" />');
		showsetting('styles_edit_styleimgdir', '', '', '<input type="text" class="txt" name="stylevar['.$stylestuff['styleimgdir']['id'].']" id="styleimgdir" value="'.$stylestuff['styleimgdir']['subst'].'" />');
		empty($stylestuff['imgdir']['subst']) && $stylestuff['imgdir']['subst'] = 'static/image/common';
		empty($stylestuff['styleimgdir']['subst']) && $stylestuff['styleimgdir']['subst'] = $stylestuff['imgdir']['subst'];
		$boardimghtml = '<br /><img src="'.(empty($stylestuff['boardimg']['subst']) ? $stylestuff['imgdir']['subst'].'/logo.svg' : (preg_match('/^(https?:)?\/\//i', $stylestuff['boardimg']['subst']) || file_exists($stylestuff['boardimg']['subst']) ? '' : (file_exists($stylestuff['styleimgdir']['subst'].'/'.$stylestuff['boardimg']['subst']) ? $stylestuff['styleimgdir']['subst'].'/' : $stylestuff['imgdir']['subst'].'/')).$stylestuff['boardimg']['subst']).'" style="max-height: 70px;" />';
		$searchimghtml = '<img src="'.(empty($stylestuff['searchimg']['subst']) ? $stylestuff['imgdir']['subst'].'/logo_sc.svg' : (preg_match('/^(https?:)?\/\//i', $stylestuff['searchimg']['subst']) || file_exists($stylestuff['searchimg']['subst']) ? '' : (file_exists($stylestuff['styleimgdir']['subst'].'/'.$stylestuff['searchimg']['subst']) ? $stylestuff['styleimgdir']['subst'].'/' : $stylestuff['imgdir']['subst'].'/')).$stylestuff['searchimg']['subst']).'" style="max-height: 70px;" />';
		$touchimghtml = '<img src="'.(empty($stylestuff['touchimg']['subst']) ? $stylestuff['imgdir']['subst'].'/logo_m.svg' : (preg_match('/^(https?:)?\/\//i', $stylestuff['touchimg']['subst']) || file_exists($stylestuff['touchimg']['subst']) ? '' : (file_exists($stylestuff['styleimgdir']['subst'].'/'.$stylestuff['touchimg']['subst']) ? $stylestuff['styleimgdir']['subst'].'/' : $stylestuff['imgdir']['subst'].'/')).$stylestuff['touchimg']['subst']).'" style="max-height: 70px;" />';
		showsetting('styles_edit_logo', "stylevar[{$stylestuff['boardimg']['id']}]", empty($stylestuff['boardimg']['subst']) ? 'logo.svg' : $stylestuff['boardimg']['subst'], 'filetext', '', 0, cplang('styles_edit_logo_comment').$boardimghtml);
		showsetting('styles_edit_searchlogo', "stylevar[{$stylestuff['searchimg']['id']}]", empty($stylestuff['searchimg']['subst']) ? 'logo_sc.svg' : $stylestuff['searchimg']['subst'], 'filetext', '', 0, $searchimghtml);
		showsetting('styles_edit_touchlogo', "stylevar[{$stylestuff['touchimg']['id']}]", empty($stylestuff['touchimg']['subst']) ? 'logo_m.svg' : $stylestuff['touchimg']['subst'], 'filetext', '', 0, $touchimghtml);

		foreach($predefinedvars as $predefinedvar => $v) {
			if($v !== []) {
				if(!empty($v[1])) {
					showtitle($v[1]);
				}
				$type = $v[0] == 1 ? 'text' : 'color';
				$extra = '';
				$comment = ($type == 'text' ? $lang['styles_edit_'.$predefinedvar.'_comment'] : $lang['styles_edit_hexcolor']).$lang['styles_edit_'.$predefinedvar.'_comment'];
				if(str_ends_with($predefinedvar, 'bgcolor')) {
					$stylestuff[$predefinedvar]['subst'] = explode(' ', $stylestuff[$predefinedvar]['subst']);
					$bgimg = $stylestuff[$predefinedvar]['subst'][1];
					$bgextra = implode(' ', array_slice($stylestuff[$predefinedvar]['subst'], 2));
					$stylestuff[$predefinedvar]['subst'] = $stylestuff[$predefinedvar]['subst'][0];
					$bgimgpre = $bgimg ? (preg_match('/^(https?:)?\/\//i', $bgimg) ? $bgimg : ($stylestuff['styleimgdir']['subst'] ? $stylestuff['styleimgdir']['subst'] : ($stylestuff['imgdir']['subst'] ? $stylestuff['imgdir']['subst'] : (STATICURL.'image/common'))).'/'.$bgimg) : (STATICURL.'image/common/none.gif');
					$comment .= '<div id="bgpre_'.$stylestuff[$predefinedvar]['id'].'" onclick="imgpre_switch('.$stylestuff[$predefinedvar]['id'].')" style="background-image:url('.$bgimgpre.');cursor:pointer;float:right;width:350px;height:40px;overflow:hidden;border: 1px solid #ccc"></div>'.$lang['styles_edit_'.$predefinedvar.'_comment'].$lang['styles_edit_bg'];
					$extra = '<br /><input name="stylevarbgimg['.$stylestuff[$predefinedvar]['id'].']" value="'.$bgimg.'" onchange="imgpre_update('.$stylestuff[$predefinedvar]['id'].', this)" type="text" class="txt" style="margin:5px 0;" />'.
						'<br /><input name="stylevarbgextra['.$stylestuff[$predefinedvar]['id'].']" value="'.$bgextra.'" type="text" class="txt" />';
					$varcomment = ' {'.strtoupper($predefinedvar).'},{'.strtoupper(substr($predefinedvar, 0, -7)).'BGCODE}:';
				} else {
					$varcomment = ' {'.strtoupper($predefinedvar).'}:';
				}
				showsetting(cplang('styles_edit_'.$predefinedvar).$varcomment, 'stylevar['.$stylestuff[$predefinedvar]['id'].']', $stylestuff[$predefinedvar]['subst'], $type, '', 0, $comment, $extra);
			}
		}
		showtablefooter();

		showtableheader('styles_edit_customvariable', 'notop');
		showsubtitle(['', 'styles_edit_variable', 'styles_edit_subst']);
		echo $stylecustom;
		showtablerow('', ['class="td25"', 'class="td24 bold"', 'class="td26"'], [
			cplang('add_new'),
			'<input type="text" class="txt" name="newcvar">',
			'<textarea name="newcsubst" class="tarea" style="height: 45px" cols="50" rows="2"></textarea>'

		]);

		showsubmit('editsubmit', 'submit', 'del');
		showtablefooter();

		echo '</div>';

		if($stylevars) {
			if(!$customMenus) {
				echo '<div id="config"'.(empty($_GET['anchor']) ? ' style="display: none"' : '').'>';
				showtableheader();
				showtitle($lang['plugins_config']);
			}

			foreach($stylevars as $var) {
				$extra = [];
				if(strexists($var['type'], ':') || str_starts_with($var['type'], 'component_')) {
					$var['variable'] = 'varsnew['.$var['variable'].']';
					$_G['showcomponent'][$var['type']][] = $var['variable'];
					admin\class_component::type_plugin($var, $extra);
				} else {
					if(strexists($var['type'], '_')) {
						continue;
					}
					$var['var'] = $var['variable'];
					$var['variable'] = 'varsnew['.$var['variable'].']';
					if($var['type'] == 'styleTitle') {
						showtitle($lang[$var['title']] ?? dhtmlspecialchars($var['title']));
						continue;
					} elseif($var['type'] == 'stylePage') {
						if($boxHeader) {
							showsubmit('editsubmit');
							showtablefooter();
							echo '</div>';
							$boxHeader = false;
						}
						$boxHeader = true;
						echo '<div id="'.$var['var'].'"'.($_GET['anchor'] != $var['var'] ? ' style="display: none"' : '').'>';
						showtableheader();
						continue;
					} elseif(method_exists('admin\class_component', $_method = 'type_'.$var['type'])) {
						admin\class_component::$_method($var, $extra);
					}
				}

				showsetting($lang[$var['title']] ?? dhtmlspecialchars($var['title']), $var['variable'], $var['value'], $var['type'], '', 0, $lang[$var['description']] ?? nl2br(dhtmlspecialchars($var['description'])), dhtmlspecialchars($var['extra']), '', true, 0, !empty($var['widemode']));
				echo implode('', $extra);
			}
			if(!$customMenus || $boxHeader) {
				showsubmit('editsubmit');
				showtablefooter();
				echo '</div>';
			}
		}
		/*search*/

		showformfooter();
	}
} else {
	$style = table_common_style::t()->fetch_by_styleid($id);
	if(!$style) {
		cpmsg('style_not_found', '', 'error');
	}

	//是否有自定义配置文件
	$configflag = false;
	if(preg_match('/^.?\/template\/([a-z]+[a-z0-9_]*)$/', $style['directory'], $a)) {
		$configfile = DISCUZ_TEMPLATE($a[1]).'/config.inc.php';
		if(file_exists($configfile)) {
			$configflag = true;
			include $configfile;
		}
	}

	if($_GET['newcvar'] && $_GET['newcsubst']) {
		if(table_common_stylevar::t()->check_duplicate($id, $_GET['newcvar'])) {
			cpmsg('styles_edit_variable_duplicate', '', 'error');
		} elseif(!preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/", $_GET['newcvar'])) {
			cpmsg('styles_edit_variable_illegal', '', 'error');
		}
		$newcvar = strtolower($_GET['newcvar']);
		table_common_stylevar::t()->insert(['styleid' => $id, 'variable' => $newcvar, 'substitute' => $_GET['newcsubst']]);
	}

	if(!$configflag) {

		$data = [];
		if(isset($_GET['namenew'])) {
			$data['name'] = $_GET['namenew'];
		}
		if(isset($_GET['templateidnew'])) {
			$data['templateid'] = $_GET['templateidnew'];
		}
		if(isset($_GET['defaultextstylenew'])) {
			if(!isset($_GET['extstylenew']) || !is_array($_GET['extstylenew'])) {
				$_GET['extstylenew'] = [];
			}
			if(!in_array($_GET['defaultextstylenew'], $_GET['extstylenew'])) {
				$_GET['extstylenew'][] = $_GET['defaultextstylenew'];
			}
			$data['extstyle'] = implode("\t", $_GET['extstylenew']).'|'.$_GET['defaultextstylenew'];
		}
		if(!empty($data)) {
			table_common_style::t()->update($id, $data);
		}
		if(isset($_GET['stylevar'])) {
			$stylevar = $_GET['stylevar'];
			$stylevarbgimg = $_GET['stylevarbgimg'];
			$stylevarbgextra = $_GET['stylevarbgextra'];
			foreach($stylevar as $varid => $substitute) {
				if(!empty($stylevarbgimg[$varid])) {
					$substitute .= ' '.$stylevarbgimg[$varid];
					if(!empty($stylevarbgextra[$varid])) {
						$substitute .= ' '.$stylevarbgextra[$varid];
					}
				}
				$substitute = @dhtmlspecialchars($substitute);
				$stylevarids = [$varid];
				table_common_stylevar::t()->update_substitute_by_styleid($substitute, $id, $stylevarids);
			}

			if(isset($_FILES['stylevar']['name'])) {
				foreach(table_common_stylevar::t()->fetch_all_by_styleid($id) as $stylevar) {
					$stylesvar[$stylevar['stylevarid']] = $stylevar['variable'];
				}
				$upload = new discuz_upload();
				foreach($_FILES['stylevar']['name'] as $varid => $value) {
					if($stylesvar[$varid]) {
						$file = [
							'name' => $_FILES['stylevar']['name'][$varid],
							'type' => $_FILES['stylevar']['type'][$varid],
							'tmp_name' => $_FILES['stylevar']['tmp_name'][$varid],
							'error' => $_FILES['stylevar']['error'][$varid],
							'size' => $_FILES['stylevar']['size'][$varid],
						];
						$logonew = admin\class_attach::upload($file, 'common', 'template', 0, $stylesvar[$varid].'_'.date('Ymd').strtolower(random(8)));
						if($logonew) {
							$stylevarids = [$varid];
							table_common_stylevar::t()->update_substitute_by_styleid($logonew, $id, $stylevarids);
						}
					}
				}
			}
		}
	}

	if($_GET['delete']) {
		table_common_stylevar::t()->delete_by_styleid($id, $_GET['delete']);
	}

	if(isset($_FILES['varsnew']['name'])) {
		$upload = new discuz_upload();
		foreach($_FILES['varsnew']['name'] as $varid => $value) {
			if(!$value) {
				continue;
			}
			$file = [
				'name' => $_FILES['varsnew']['name'][$varid],
				'type' => $_FILES['varsnew']['type'][$varid],
				'tmp_name' => $_FILES['varsnew']['tmp_name'][$varid],
				'error' => $_FILES['varsnew']['error'][$varid],
				'size' => $_FILES['varsnew']['size'][$varid],
			];
			$_GET['varsnew'][$varid] = admin\class_attach::upload($file, 'common', 'style', 0, $value);
		}
	}
	if(!empty($_GET['deleteUploadimage'])) {
		foreach($_GET['deleteUploadimage'] as $key) {
			if(!isset($_GET['varsnew'][$key])) {
				continue;
			}
			admin\class_attach::delete($_GET['varsnew'][$key]);
			$_GET['varsnew'][$key] = '';
		}
	}

	if(is_array($_GET['varsnew'])) {

		$stylevars = [];
		foreach(table_common_stylevar_extra::t()->fetch_all_by_styleid($id) as $var) {
			$stylevars[$var['variable']] = $var;
		}

		foreach($_GET['varsnew'] as $variable => $value) {
			if(isset($stylevars[$variable])) {
				if($stylevars[$variable]['type'] == 'number') {
					$value = (float)$value;
				} elseif(in_array($stylevars[$variable]['type'], ['forums', 'groups', 'selects', 'portalcats'])) {
					$value = serialize($value);
				}
				$value = (string)$value;
				table_common_stylevar_extra::t()->update_by_variable($id, $variable, ['value' => $value]);
			}
		}
	}

	updatecache(['setting', 'styles']);

	$tpl = dir(DISCUZ_DATA.'./template');
	while($entry = $tpl->read()) {
		if(preg_match('/\.tpl\.php$/', $entry)) {
			@unlink(DISCUZ_DATA.'./template/'.$entry);
		}
	}
	$tpl->close();
	$anchor == '_default' && $anchor = '';
	cpmsg('styles_edit_succeed', 'action=styles&operation=edit&id='.$id.'&anchor='.$anchor, 'succeed');

}
	