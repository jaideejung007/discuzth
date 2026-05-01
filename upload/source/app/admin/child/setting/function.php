<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}


function dateformat($string, $operation = 'formalise') {
	$string = dhtmlspecialchars(trim($string));
	$replace = $operation == 'formalise' ? [['n', 'j', 'y', 'Y'], ['mm', 'dd', 'yy', 'yyyy']] : [['mm', 'dd', 'yyyy', 'yy'], ['n', 'j', 'Y', 'y']];
	return str_replace($replace[0], $replace[1], $string);
}

function insertconfig($s, $find, $replace) {
	if(preg_match($find, $s)) {
		$s = preg_replace($find, $replace, $s);
	} else {
		$s .= "\r\n".$replace;
	}
	return $s;
}

function watermarkinit($type) {
	global $settingnew;
	$settingnew['watermarktext']['size'][$type] = intval($settingnew['watermarktext']['size'][$type]);
	$settingnew['watermarktext']['angle'][$type] = intval($settingnew['watermarktext']['angle'][$type]);
	$settingnew['watermarktext']['shadowx'][$type] = intval($settingnew['watermarktext']['shadowx']);
	$settingnew['watermarktext']['shadowy'][$type] = intval($settingnew['watermarktext']['shadowy'][$type]);
	$settingnew['watermarktext']['fontpath'][$type] = str_replace(['\\', '/'], '', $settingnew['watermarktext']['fontpath'][$type]);
	if($settingnew['watermarktype'][$type] == 'text' && $settingnew['watermarktext']['fontpath'][$type]) {
		$fontpath = $settingnew['watermarktext']['fontpath'][$type];
		$fontpathnew = 'ch/'.$fontpath;
		$settingnew['watermarktext']['fontpath'][$type] = file_exists('source/data/seccode/font/'.$fontpathnew) ? $fontpathnew : '';
		if(!$settingnew['watermarktext']['fontpath'][$type]) {
			$fontpathnew = 'en/'.$fontpath;
			$settingnew['watermarktext']['fontpath'][$type] = file_exists('source/data/seccode/font/'.$fontpathnew) ? $fontpathnew : '';
		}
		if(!$settingnew['watermarktext']['fontpath'][$type]) {
			cpmsg('watermarkpreview_fontpath_error', '', 'error');
		}
	}
}

function showlist($first, $seconds, $thirds, $subtype) {
	echo '<tbody id="'.$subtype.'_detail" style="display:none"><tr><td colspan="2"><table width="100%">';
	foreach($first as $id => $gsecond) {
		showdetial($gsecond, $subtype, 'group', '', 1);
		if(!empty($seconds[$id])) {
			foreach($seconds[$id] as $second) {
				showdetial($second, $subtype);
				if(!empty($thirds[$second['id']])) {
					foreach($thirds[$second['id']] as $third) {
						showdetial($third, $subtype);
					}
				}
			}
		}
		showdetial($gsecond, $subtype, '', 'last');
	}
	echo '</table></td></tr></tbody>';
}

function showdetial(&$forum, $varname, $type = '', $last = '', $toggle = false) {
	global $_G;

	if($last == '') {
		$tab1 = '&nbsp;&nbsp;';
		$tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;';
		if($type == 'group') {
			echo '<tr class="hover"><td colspan="2"'.($type == 'group' ? ' onclick="toggle_group(\'group_'.$varname.$forum['id'].'\', $(\'a_group_'.$varname.$forum['id'].'\'))"' : '').'>'.($type == 'group' ? '<a href="javascript:;" id="a_group_'.$varname.$forum['id'].'">'.($toggle ? '[+]' : '[-]').'</a>' : '').'&nbsp;&nbsp;'.$forum['name'].'</td></tr><tbody id="group_'.$varname.$forum['id'].'"'.($toggle ? ' style="display:none;"' : '').'>';
		}
		echo '<tr class="header"><td colspan="2">'.$tab1.$forum['name'].'</td></tr>';
		showtablerow('', ['width="12%"', ''], [
				$tab2.cplang('setting_seo_seotitle'),
				'<input type="text" id="t_'.$forum['id'].'_'.$varname.'" onfocus="getcodetext(this, \''.$varname.'\');" name="seo'.$varname.'['.$forum['id'].'][seotitle]" value="'.dhtmlspecialchars($forum['seotitle']).'" class="txt" style="width:280px;" />',
			]
		);
		showtablerow('', ['width="12%"', ''], [
				$tab2.cplang('setting_seo_seokeywords'),
				'<input type="text" id="k_'.$forum['id'].'_'.$varname.'" onfocus="getcodetext(this, \''.$varname.'\');" name="seo'.$varname.'['.$forum['id'].'][keywords]" value="'.dhtmlspecialchars($forum['keywords']).'" class="txt" style="width:280px;" />',
			]
		);
		showtablerow('', ['width="12%"', ''], [
				$tab2.cplang('setting_seo_seodescription'),
				'<input type="text" id="d_'.$forum['id'].'_'.$varname.'" onfocus="getcodetext(this, \''.$varname.'\');" name="seo'.$varname.'['.$forum['id'].'][description]" value="'.dhtmlspecialchars($forum['description']).'" class="txt" style="width:280px;" />',
			]
		);
	} else {
		if($last == 'lastboard') {
			$return = '</tbody>';
		} elseif($last == 'lastchildboard' && $type) {
			$return = '<script type="text/JavaScript">$(\'cb_'.$type.'\').className = \'lastchildboard\';</script>';
		} elseif($last == 'last') {
			$return = '</tbody>';
		}
	}
	echo $return = $return ?? '';
}

function getmemorycachekeys() {
	return ['common_member', 'forum_forum', 'forum_thread', 'forum_thread_forumdisplay', 'forum_postcache',
		'forum_collectionrelated', 'forum_collection', 'home_follow', 'forumindex', 'diyblock', 'diyblockoutput'];
}

function getseccodes() {
	global $_G;
	$checkdirs = array_merge([''], $_G['setting']['plugins']['available']);
	$seccodetypearray = [];
	foreach($checkdirs as $key) {
		if($key) {
			$dir = DISCUZ_PLUGIN($key).'/seccode';
		} else {
			$dir = DISCUZ_ROOT.'./source/class/seccode';
		}
		if(!file_exists($dir)) {
			continue;
		}
		$codedir = dir($dir);
		while($entry = $codedir->read()) {
			if(!in_array($entry, ['.', '..']) && preg_match('/^seccode\_[\w\.]+$/', $entry) && str_ends_with($entry, '.php') && strlen($entry) < 30 && is_file($dir.'/'.$entry)) {
				@include_once $dir.'/'.$entry;
				$codeclass = substr($entry, 0, -4);
				if(class_exists($codeclass)) {
					$code = new $codeclass();
					$script = substr($codeclass, 8);
					$script = ($key ? $key.':' : '').$script;
					if(!is_numeric($script)) {
						$seccodetypearray[] = [$script, lang('seccode/'.$script, $code->name), ['seccodeimageext' => 'none', 'seccodeimagewh' => 'none']];
					}
				}
			}
		}
	}
	return $seccodetypearray;
}

function getsecchecks() {
	global $_G;
	$sechecks = [];
	foreach($_G['setting']['plugins']['available'] as $key) {
		$dir = DISCUZ_PLUGIN($key).'/seccheck';
		if(!file_exists($dir)) {
			continue;
		}
		$qaadir = dir($dir);
		while($entry = $qaadir->read()) {
			if(!in_array($entry, ['.', '..']) && preg_match('/^seccheck\_[\w\.]+$/', $entry) && str_ends_with($entry, '.php') && strlen($entry) < 30 && is_file($dir.'/'.$entry)) {
				@include_once $dir.'/'.$entry;
				$checkclass = substr($entry, 0, -4);
				if(class_exists($checkclass)) {
					$check = new $checkclass();
					$script = substr($checkclass, 9);
					$new = @filemtime($dir.'/'.$entry) > TIMESTAMP - 86400 ? ' <font color="red">New!</font>' : '';
					$sechecks[$key.':'.$script] = [
						property_exists($check, 'settingurl'),
						(property_exists($check, 'name') ? lang('plugin/'.$key, $check->name, default: $check->name) : $key.':'.$script).$new,
						property_exists($check, 'settingurl') ? $check->settingurl : '',
						property_exists($check, 'copyright') ? lang('plugin/'.$key, $check->copyright, default: $check->copyright) : '',
					];
				}
			}
		}
	}
	return $sechecks;
}

function getsecqaas($qaaext) {
	global $_G;
	$checkdirs = array_merge([''], $_G['setting']['plugins']['available']);
	$secqaaext = '';
	foreach($checkdirs as $key) {
		if($key) {
			$dir = DISCUZ_PLUGIN($key).'/secqaa';
		} else {
			$dir = DISCUZ_ROOT.'./source/class/secqaa';
		}
		if(!file_exists($dir)) {
			continue;
		}
		$qaadir = dir($dir);
		while($entry = $qaadir->read()) {
			if(!in_array($entry, ['.', '..']) && preg_match('/^secqaa\_[\w\.]+$/', $entry) && str_ends_with($entry, '.php') && strlen($entry) < 30 && is_file($dir.'/'.$entry)) {
				@include_once $dir.'/'.$entry;
				$qaaclass = substr($entry, 0, -4);
				if(class_exists($qaaclass)) {
					$qaa = new $qaaclass();
					$script = substr($qaaclass, 7);
					$script = ($key ? $key.':' : '').$script;
					$setting = '';
					if(property_exists($qaa, 'settingurl')) {
						$setting = '<a style="float:right;margin: 8px 10px" href="'.ADMINSCRIPT.'?'.$qaa->settingurl.'" target="_blank">'.cplang('edit').'</a>';
					}
					$name = $qaa->name ? ($key ? lang('plugin/'.$key, $qaa->name, default: $qaa->name) : lang('secqaa/'.$script, $qaa->name)) : '';
					$desc = $qaa->description ? ($key ? lang('plugin/'.$key, $qaa->description, default: $qaa->description) : lang('secqaa/'.$script, $qaa->description)) : '';
					$copyright = $qaa->copyright ? ($key ? lang('plugin/'.$key, $qaa->copyright, default: $qaa->copyright) : lang('secqaa/'.$script, $qaa->copyright)) : '';
					$secqaaext .= showtablerow('class="hover"', [], [
						$setting.'<label><input class="checkbox" class="checkbox" type="checkbox" name="secqaaext[]" value="'.$script.'"'.(in_array($script, $qaaext) ? ' checked="checked"' : '').'> '.$name.(@filemtime($dir.'/'.$entry) > TIMESTAMP - 86400 ? ' <font color="red">New!</font>' : '').($qaa->description ? '<div class="lightfont" style="margin-left:30px">&nbsp;'.$desc.'</div>' : '').'</label>',
						$copyright
					], true);
				}
			}
		}
	}
	return $secqaaext;
}

function threadprofile_buttons($id, $authorinfoitems) {
	$buttons = '';
	$i = 0;
	foreach($authorinfoitems as $k => $name) {
		if(!is_numeric($k)) {
			if($i > 11) {
				$buttons .= '<br />';
				$i = 0;
			}
			if(str_starts_with($k, '{')) {
				$code = $k;
			} else {
				$code = '<dt>{baseinfo='.$k.',1}</dt><dd>{baseinfo='.$k.',0}</dd>\n';
			}
			$buttons .= '<a href="###" onclick="insertunit($(\''.$id.'\'), \''.$code.'\')">'.$name.'</a>';
			$i++;
		} else {
			$buttons .= $name ? '<a href="javascript:;" onclick="display(\''.$id.'more\')" class="light">'.cplang('more').'</a><div id="'.$id.'more" style="display:none">' : '<br />';
			$i = 0;
		}
	}
	$buttons .= '</div>';
	return $buttons;
}

function showsetting_threadprfile($authorinfoitems, $template = []) {
	$template_left = dhtmlspecialchars($template['left']);
	$buttons = threadprofile_buttons('tleft', $authorinfoitems);
	echo '<tr><td class="td27" colspan="2">'.cplang('setting_styles_threadprofile_leftinfoprofile').':</td></tr>
		<tr><td colspan="2" class="rowform"><div class="threadprofilenode">'.$buttons.'</div><textarea name="templatenew[left]" id="tleft" class="marginbot" style="width:80%" rows="10" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)">'.$template_left.'</textarea></td></tr>';
	$template_top = dhtmlspecialchars($template['top']);
	$buttons = threadprofile_buttons('ttop', $authorinfoitems);
	echo '<tr><td class="td27" colspan="2">'.cplang('setting_styles_threadprofile_avatarprofile').':</td></tr>
		<tr><td colspan="2" class="rowform"><div class="threadprofilenode">'.$buttons.'</div><textarea name="templatenew[top]" id="ttop" class="marginbot" style="width:80%" rows="10" onkeyup="textareasize(this)" onkeydown="textareakey(this, event)">'.$template_top.'</textarea></td></tr>';
}