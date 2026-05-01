<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function profile_setting($fieldid, $space = [], $showstatus = false, $ignoreunchangable = false, $ignoreshowerror = false) {
	global $_G;

	if(empty($_G['cache']['profilesetting'])) {
		loadcache('profilesetting');
	}
	$field = getglobal('cache/profilesetting/'.$fieldid);
	if(empty($field) || !$field['available'] || in_array($fieldid, ['uid', 'constellation', 'zodiac', 'birthmonth', 'birthyear', 'birthcountry', 'birthprovince', 'birthdist', 'birthcommunity', 'residecountry', 'resideprovince', 'residedist', 'residecommunity', 'fields'])) {
		return '';
	}

	if($showstatus) {
		$uid = intval($space['uid']);
		if($uid && !isset($_G['profile_verifys'][$uid])) {
			$_G['profile_verifys'][$uid] = [];
			if($value = table_common_member_verify_info::t()->fetch_by_uid_verifytype($uid, 0)) {
				$fields = dunserialize($value['field']);
				foreach($fields as $key => $fvalue) {
					if($_G['cache']['profilesetting'][$key]['needverify']) {
						$_G['profile_verifys'][$uid][$key] = $fvalue;
					}
				}
			}
		}
		$verifyvalue = NULL;
		if(isset($_G['profile_verifys'][$uid][$fieldid])) {
			if($fieldid == 'gender') {
				$verifyvalue = lang('space', 'gender_'.intval($_G['profile_verifys'][$uid][$fieldid]));
			} elseif($fieldid == 'birthday') {
				$verifyvalue = $_G['profile_verifys'][$uid]['birthyear'].'-'.$_G['profile_verifys'][$uid]['birthmonth'].'-'.$_G['profile_verifys'][$uid]['birthday'];
			} else {
				$verifyvalue = $_G['profile_verifys'][$uid][$fieldid];
			}
		}
	}

	if(!empty($field['encrypt'])) {
		$space[$fieldid] = authcode_field($field['encrypt'], $space[$fieldid], 'DECODE');
	}

	$html = '';
	$field['unchangeable'] = !$ignoreunchangable && $field['unchangeable'] ? 1 : 0;
	if($fieldid == 'birthday') {
		if($field['unchangeable'] && !empty($space[$fieldid])) {
			return '<span>'.$space['birthyear'].'-'.$space['birthmonth'].'-'.$space['birthday'].'</span>';
		}
		$birthyeayhtml = '';
		$nowy = dgmdate($_G['timestamp'], 'Y');
		for($i = 0; $i < 100; $i++) {
			$they = $nowy - $i;
			$selectstr = $they == $space['birthyear'] ? ' selected' : '';
			$birthyeayhtml .= "<option value=\"$they\"$selectstr>$they</option>";
		}
		$birthmonthhtml = '';
		for($i = 1; $i < 13; $i++) {
			$selectstr = $i == $space['birthmonth'] ? ' selected' : '';
			$birthmonthhtml .= "<option value=\"$i\"$selectstr>$i</option>";
		}
		$birthdayhtml = '';
		if(empty($space['birthmonth']) || in_array($space['birthmonth'], [1, 3, 5, 7, 8, 10, 12])) {
			$days = 31;
		} elseif(in_array($space['birthmonth'], [4, 6, 9, 11])) {
			$days = 30;
		} elseif($space['birthyear'] && (($space['birthyear'] % 400 == 0) || ($space['birthyear'] % 4 == 0 && $space['birthyear'] % 100 != 0))) {
			$days = 29;
		} else {
			$days = 28;
		}
		for($i = 1; $i <= $days; $i++) {
			$selectstr = $i == $space['birthday'] ? ' selected' : '';
			$birthdayhtml .= "<option value=\"$i\"$selectstr>$i</option>";
		}
		$html = '<select name="birthyear" id="birthyear" class="ps'.(defined('IN_MOBILE') ? ' sort_sel' : '').'" onchange="showbirthday();">'
			.'<option value="">'.lang('space', 'year').'</option>'
			.$birthyeayhtml
			.'</select>'
			.'&nbsp;&nbsp;'
			.'<select name="birthmonth" id="birthmonth" class="ps'.(defined('IN_MOBILE') ? ' sort_sel' : '').'" onchange="showbirthday();">'
			.'<option value="">'.lang('space', 'month').'</option>'
			.$birthmonthhtml
			.'</select>'
			.'&nbsp;&nbsp;'
			.'<select name="birthday" id="birthday" class="ps'.(defined('IN_MOBILE') ? ' sort_sel' : '').'">'
			.'<option value="">'.lang('space', 'day').'</option>'
			.$birthdayhtml
			.'</select>';

	} elseif($fieldid == 'gender') {
		if($field['unchangeable'] && $space[$fieldid] > 0) {
			return '<span>'.lang('space', 'gender_'.intval($space[$fieldid])).'</span>';
		}
		$selected = [$space[$fieldid] => ' selected="selected"'];
		$html = '<select name="gender" id="gender" class="ps'.(defined('IN_MOBILE') ? ' sort_sel' : '').'">';
		if($field['unchangeable']) {
			$html .= '<option value="">'.lang('space', 'gender').'</option>';
		} else {
			$html .= '<option value="0"'.($space[$fieldid] == '0' ? ' selected="selected"' : '').'>'.lang('space', 'gender_0').'</option>';
		}
		$html .= '<option value="1"'.($space[$fieldid] == '1' ? ' selected="selected"' : '').'>'.lang('space', 'gender_1').'</option>'
			.'<option value="2"'.($space[$fieldid] == '2' ? ' selected="selected"' : '').'>'.lang('space', 'gender_2').'</option>'
			.'</select>';

	} elseif($fieldid == 'birthcity') {
		if($field['unchangeable'] && !empty($space[$fieldid])) {
			return '<span>'.$space['birthcountry'].'-'.$space['birthprovince'].'-'.$space['birthcity'].'</span>';
		}
		$values = [0, 0, 0, 0, 0];
		$elems = ['birthcountry', 'birthprovince', 'birthcity', 'birthdist', 'birthcommunity'];
		if(!empty($space['birthcountry'])) {
			$html = profile_show('birthcity', $space);
			$html .= '&nbsp;(<a href="javascript:;" onclick="showdistrict(\'birthdistrictbox\', [\'birthcountry\', \'birthprovince\', \'birthcity\', \'birthdist\', \'birthcommunity\'], 4, \'\', \'birth\'); return false;">'.lang('spacecp', 'profile_edit').'</a>)';
			$html .= '<p id="birthdistrictbox"></p>';
		} else {
			$html = '<p id="birthdistrictbox">'.showdistrict($values, $elems, 'birthdistrictbox', 1, 'birth').'</p>';
		}

	} elseif($fieldid == 'residecity') {
		if($field['unchangeable'] && !empty($space[$fieldid])) {
			return '<span>'.$space['resideprovince'].'-'.$space['residecity'].'</span>';
		}
		$values = [0, 0, 0, 0, 0];
		$elems = ['residecountry', 'resideprovince', 'residecity', 'residedist', 'residecommunity'];
		if(!empty($space['residecountry'])) {
			$html = profile_show('residecity', $space);
			$html .= '&nbsp;(<a href="javascript:;" onclick="showdistrict(\'residedistrictbox\', [\'residecountry\', \'resideprovince\', \'residecity\', \'residedist\', \'residecommunity\'], 4, \'\', \'reside\'); return false;">'.lang('spacecp', 'profile_edit').'</a>)';
			$html .= '<p id="residedistrictbox"></p>';
		} else {
			$html = '<p id="residedistrictbox">'.showdistrict($values, $elems, 'residedistrictbox', 1, 'reside').'</p>';
		}
	} elseif($fieldid == 'qq') {
		$html = "<input type=\"text\" name=\"$fieldid\" id=\"$fieldid\" class=\"px\" value=\"$space[$fieldid]\" placeholder=\"\" /><p><a href=\"\" class=\"xi2\" onclick=\"this.href='//wp.qq.com/set.html?from=discuz&uin='+$('$fieldid').value\" target=\"_blank\">".lang('spacecp', 'qq_set_status').'</a></p>';
	} else {
		if($field['unchangeable'] && $space[$fieldid] != '') {
			if($field['formtype'] == 'file') {
				$imgurl = getglobal('setting/attachurl').'./profile/'.$space[$fieldid];
				return '<span><a href="'.$imgurl.'" target="_blank"><img src="'.$imgurl.'"  style="max-width: 500px;" /></a></span>';
			} else {
				return '<span>'.nl2br($space[$fieldid]).'</span>';
			}
		}
		if($field['formtype'] == 'textarea') {
			$html = "<textarea name=\"$fieldid\" id=\"$fieldid\" class=\"pt\" rows=\"3\" cols=\"40\">$space[$fieldid]</textarea>";
		} elseif($field['formtype'] == 'select') {
			$field['choices'] = explode("\n", $field['choices']);
			$html = "<select name=\"$fieldid\" id=\"$fieldid\" class=\"ps\">";
			foreach($field['choices'] as $op) {
				$html .= "<option value=\"$op\"".($op == $space[$fieldid] ? 'selected="selected"' : '').">$op</option>";
			}
			$html .= '</select>';
		} elseif($field['formtype'] == 'list') {
			$field['choices'] = explode("\n", $field['choices']);
			$html = "<select name=\"{$fieldid}[]\" id=\"$fieldid\" class=\"ps\" multiple=\"multiplue\">";
			$space[$fieldid] = explode("\n", $space[$fieldid]);
			foreach($field['choices'] as $op) {
				$html .= "<option value=\"$op\"".(in_array($op, $space[$fieldid]) ? 'selected="selected"' : '').">$op</option>";
			}
			$html .= '</select>';
		} elseif($field['formtype'] == 'checkbox') {
			$field['choices'] = explode("\n", $field['choices']);
			$space[$fieldid] = explode("\n", $space[$fieldid]);
			foreach($field['choices'] as $op) {
				$html .= ''
					."<label class=\"lb\"><input type=\"checkbox\" name=\"{$fieldid}[]\" id=\"$fieldid\" class=\"pc\" value=\"$op\"".(in_array($op, $space[$fieldid]) ? ' checked="checked"' : '').' />'
					."$op</label>";
			}
		} elseif($field['formtype'] == 'radio') {
			$field['choices'] = explode("\n", $field['choices']);
			foreach($field['choices'] as $op) {
				$html .= ''
					."<label class=\"lb\"><input type=\"radio\" name=\"{$fieldid}\" class=\"pr\" value=\"$op\"".($op == $space[$fieldid] ? ' checked="checked"' : '').' />'
					."$op</label>";
			}
		} elseif($field['formtype'] == 'file') {
			$html = "<input type=\"file\" value=\"\" name=\"$fieldid\" id=\"$fieldid\" class=\"pf\" style=\"height:26px;\" /><input type=\"hidden\" name=\"$fieldid\" value=\"$space[$fieldid]\" />";
			if(!empty($space[$fieldid])) {
				$url = getglobal('setting/attachurl').'./profile/'.$space[$fieldid];
				$html .= "&nbsp;<label><input type=\"checkbox\" class=\"checkbox\" name=\"deletefile[$fieldid]\" id=\"$fieldid\" value=\"yes\" />".lang('spacecp', 'delete')."</label><br /><a href=\"$url\" target=\"_blank\"><img src=\"$url\" width=\"200\" class=\"mtm\" /></a>";
			}
		} else {
			$html = "<input type=\"text\" name=\"$fieldid\" id=\"$fieldid\" class=\"px\" value=\"$space[$fieldid]\" placeholder=\"".$field['title']."\" />";
		}
	}
	$html .= !$ignoreshowerror ? "<div class=\"rq mtn\" id=\"showerror_$fieldid\"></div>" : '';
	if($showstatus) {
		$tips = $field['description'] ?? '';
		if($space[$fieldid] == '' && !empty($field['unchangeable'])) {
			$tips .= (empty($tips) ? '' : ' ').lang('spacecp', 'profile_unchangeable');
		}
		if($verifyvalue !== null) {
			if($field['formtype'] == 'file') {
				$imgurl = getglobal('setting/attachurl').'./profile/'.$verifyvalue;
				$verifyvalue = "<img src='$imgurl' alt='$imgurl' style='max-width: 500px;'/>";
			}
			$tips .= (empty($tips) ? '' : ' ').'<strong>'.lang('spacecp', 'profile_is_verifying')." (<a href=\"#\" onclick=\"display('newvalue_$fieldid');return false;\">".lang('spacecp', 'profile_mypost').'</a>)</strong>'
				."<p id=\"newvalue_$fieldid\" style=\"display:none\">".$verifyvalue.'</p>';
		} elseif($field['needverify']) {
			$tips .= (empty($tips) ? '' : ' ').lang('spacecp', 'profile_need_verifying');
		}
		$html .= '<p class="d">'.$tips.'</p>';
	}

	return $html;
}

function profile_check($fieldid, &$value, $space = []) {
	global $_G;

	if(empty($_G['cache']['profilesetting'])) {
		loadcache('profilesetting');
	}
	if(empty($_G['profilevalidate'])) {
		include childfile('profilevalidate', 'home/spacecp');;
		$_G['profilevalidate'] = $profilevalidate;
	}

	$field = $_G['cache']['profilesetting'][$fieldid];
	if(empty($field) || !$field['available']) {
		return false;
	}

	if($value == '') {
		if($field['required']) {
			if(in_array($fieldid, ['birthcountry', 'birthprovince', 'birthcity', 'birthdist', 'birthcommunity', 'residecountry', 'resideprovince', 'residecity', 'residedist', 'residecommunity'])) {
				if(str_starts_with($fieldid, 'birth')) {
					if(!empty($_GET['birthcountry']) || !empty($_GET['birthprovince']) || !empty($_GET['birthcity']) || !empty($_GET['birthdist']) || !empty($_GET['birthcommunity'])) {
						return true;
					}
				} elseif(!empty($_GET['residecountry']) || !empty($_GET['resideprovince']) || !empty($_GET['residecity']) || !empty($_GET['residedist']) || !empty($_GET['residecommunity'])) {
					return true;
				}
			}
			return false;
		} else {
			return true;
		}
	}
	if($field['unchangeable'] && !empty($space[$fieldid])) {
		return false;
	}

	include_once libfile('function/home');
	if(in_array($fieldid, ['birthday', 'birthmonth', 'birthyear', 'gender'])) {
		$value = intval($value);
		return true;
	} elseif(in_array($fieldid, ['birthcountry', 'birthprovince', 'birthcity', 'birthdist', 'birthcommunity', 'residecountry', 'resideprovince', 'residecity', 'residedist', 'residecommunity'])) {
		$value = getstr($value);
		return true;
	}

	if($field['choices']) {
		$field['choices'] = explode("\n", $field['choices']);
	}
	if($field['formtype'] == 'text' || $field['formtype'] == 'textarea') {
		$value = getstr($value);
		if($field['size'] && strlen($value) > $field['size']) {
			return false;
		} else {
			$field['validate'] = !empty($field['validate']) ? $field['validate'] : ($_G['profilevalidate'][$fieldid] ? $_G['profilevalidate'][$fieldid] : '');
			if($field['validate'] && !preg_match($field['validate'], $value)) {
				return false;
			}
		}
	} elseif($field['formtype'] == 'checkbox' || $field['formtype'] == 'list') {
		$arr = [];
		foreach($value as $op) {
			if(in_array($op, $field['choices'])) {
				$arr[] = $op;
			}
		}
		$value = implode("\n", $arr);
		if($field['size'] && count($arr) > $field['size']) {
			return false;
		}
	} elseif($field['formtype'] == 'radio' || $field['formtype'] == 'select') {
		if(!in_array($value, $field['choices'])) {
			return false;
		}
	}
	return true;
}

function profile_show($fieldid, $space = [], $getalone = false) {
	global $_G;

	if(empty($_G['cache']['profilesetting'])) {
		loadcache('profilesetting');
	}
	if($fieldid == 'qqnumber') {
		$_G['cache']['profilesetting'][$fieldid] = $_G['cache']['profilesetting']['qq'];
	}
	$field = $_G['cache']['profilesetting'][$fieldid];
	if(empty($field) || !$field['available'] || (!$getalone && in_array($fieldid, ['uid', 'birthmonth', 'birthyear', 'birthcountry', 'residecountry']))) {
		return false;
	}

	if(!empty($_G['cache']['profilesetting'][$fieldid]['encrypt'])) {
		$space[$fieldid] = authcode_field($_G['cache']['profilesetting'][$fieldid]['encrypt'], $space[$fieldid], 'DECODE');
	}

	if($fieldid == 'gender') {
		return lang('space', 'gender_'.intval($space['gender']));
	} elseif($fieldid == 'birthday' && !$getalone) {
		$return = $space['birthyear'] ? $space['birthyear'].' '.lang('space', 'year').' ' : '';
		if($space['birthmonth'] && $space['birthday']) {
			$return .= $space['birthmonth'].' '.lang('space', 'month').' '.$space['birthday'].' '.lang('space', 'day');
		}
		return $return;
	} elseif($fieldid == 'birthcity' && !$getalone) {
		return $space['birthcountry']
			.(!empty($space['birthprovince']) ? ' '.$space['birthprovince'] : '')
			.(!empty($space['birthcity']) ? ' '.$space['birthcity'] : '')
			.(!empty($space['birthdist']) ? ' '.$space['birthdist'] : '')
			.(!empty($space['birthcommunity']) ? ' '.$space['birthcommunity'] : '');
	} elseif($fieldid == 'residecity' && !$getalone) {
		return $space['residecountry']
			.(!empty($space['resideprovince']) ? ' '.$space['resideprovince'] : '')
			.(!empty($space['residecity']) ? ' '.$space['residecity'] : '')
			.(!empty($space['residedist']) ? ' '.$space['residedist'] : '')
			.(!empty($space['residecommunity']) ? ' '.$space['residecommunity'] : '');
	} elseif($fieldid == 'site') {
		$url = str_replace('"', '\\"', $space[$fieldid]);
		return "<a href=\"$url\" target=\"_blank\">$url</a>";
	} elseif($fieldid == 'position') {
		return nl2br($space['office'] ?: ($space['field_position'] ?: $space['position']));
	} elseif($fieldid == 'qq') {
		return '<a href="//wpa.qq.com/msgrd?v=3&uin='.$space[$fieldid].'&site='.$_G['setting']['bbname'].'&menu=yes&from=discuz" target="_blank" title="'.lang('spacecp', 'qq_dialog').'"><img src="'.STATICURL.'image/common/qq.gif" alt="QQ" style="margin:0px;"/></a>';
	} elseif($fieldid == 'qqnumber') {
		return $space['qq'];
	} else {
		return nl2br($space[$fieldid]);
	}
}


function showdistrict($values, $elems = [], $container = 'districtbox', $showlevel = null, $containertype = 'birth') {
	$html = '';
	if(!preg_match('/^[A-Za-z0-9_]+$/', $container)) {
		return $html;
	}
	$showlevel = !empty($showlevel) ? intval($showlevel) : count($values);
	$showlevel = $showlevel <= 5 ? $showlevel : 5;
	$upids = [0];
	for($i = 0; $i < $showlevel; $i++) {
		if(!empty($values[$i])) {
			$upids[] = intval($values[$i]);
		} else {
			for($j = $i; $j < $showlevel; $j++) {
				$values[$j] = '';
			}
			break;
		}
	}
	$options = [0 => [], 1 => [], 2 => [], 3 => [], 4 => []];
	if($upids && is_array($upids)) {
		foreach(table_common_district::t()->fetch_all_by_upid($upids, 'displayorder', 'ASC') as $value) {
			if($value['level'] == 0 && ($value['id'] != $values[0] && ($value['usetype'] == 0 || !(($containertype == 'birth' && in_array($value['usetype'], [1, 3])) || ($containertype != 'birth' && in_array($value['usetype'], [2, 3])))))) {
				continue;
			}
			$options[$value['level']][] = [$value['id'], $value['name']];
		}
	}
	$names = ['country', 'province', 'city', 'district', 'community'];
	for($i = 0; $i < 4; $i++) {
		if(!empty($elems[$i])) {
			$elems[$i] = dhtmlspecialchars(preg_replace('/[^\[A-Za-z0-9_\]]/', '', $elems[$i]));
		} else {
			$elems[$i] = ($containertype == 'birth' ? 'birth' : 'reside').$names[$i];
		}
	}

	for($i = 0; $i < $showlevel; $i++) {
		$level = $i;
		if(!empty($options[$level])) {
			$jscall = "showdistrict('$container', ['$elems[0]', '$elems[1]', '$elems[2]', '$elems[3]', '$elems[4]'], $showlevel, $level, '$containertype')";
			$html .= '<select name="'.$elems[$i].'" id="'.$elems[$i].'" class="ps" onchange="'.$jscall.'">';
			$html .= '<option value="">'.lang('spacecp', 'district_level_'.$level).'</option>';
			foreach($options[$level] as $option) {
				$selected = $option[0] == $values[$i] ? ' selected="selected"' : '';
				$html .= '<option did="'.$option[0].'" value="'.$option[1].'"'.$selected.'>'.$option[1].'</option>';
			}
			$html .= '</select>';
			$html .= '&nbsp;&nbsp;';
		}
	}
	return $html;
}

function countprofileprogress($uid = 0) {
	global $_G;

	$uid = intval(!$uid ? $_G['uid'] : $uid);
	if(($profilegroup = table_common_setting::t()->fetch_setting('profilegroup', true))) {
		$fields = [];
		foreach($profilegroup as $type => $value) {
			foreach($value['field'] as $key => $field) {
				$fields[$key] = $field;
			}
		}
		if(isset($fields['sightml']) && empty($_G['group']['maxsigsize'])) {
			unset($fields['sightml']);
		}
		if(isset($fields['customstatus']) && empty($_G['group']['allowcstatus'])) {
			unset($fields['customstatus']);
		}
		loadcache('profilesetting');
		$allowcstatus = !empty($_G['group']['allowcstatus']);
		$complete = 0;
		$profile = array_merge(table_common_member_profile::t()->fetch($uid), table_common_member_field_forum::t()->fetch($uid));
		foreach($fields as $key) {
			if((!isset($_G['cache']['profilesetting'][$key]) || !$_G['cache']['profilesetting'][$key]['available']) && !in_array($key, ['sightml', 'customstatus'])) {
				unset($fields[$key]);
				continue;
			}
			if(in_array($key, ['birthday', 'birthyear', 'birthcountry', 'birthprovince', 'birthcity', 'birthdist', 'birthcommunity', 'residecountry', 'resideprovince', 'residecity', 'residedist', 'residecommunity'])) {
				if($key == 'birthday') {
					if(!empty($profile['birthyear']) || !empty($profile[$key])) {
						$complete++;
					}
					unset($fields['birthyear']);
				} elseif($key == 'birthcity') {
					if(!empty($profile['birthcountry']) || !empty($profile['birthprovince']) || !empty($profile[$key]) || !empty($profile['birthdist']) || !empty($profile['birthcommunity'])) {
						$complete++;
					}
					unset($fields['birthcountry']);
					unset($fields['birthprovince']);
					unset($fields['birthdist']);
					unset($fields['birthcommunity']);
				} elseif($key == 'residecity') {
					if(!empty($profile['residecountry']) || !empty($profile['resideprovince']) || !empty($profile[$key]) || !empty($profile['residedist']) || !empty($profile['residecommunity'])) {
						$complete++;
					}
					unset($fields['residecountry']);
					unset($fields['resideprovince']);
					unset($fields['residedist']);
					unset($fields['residecommunity']);
				}
			} else if($profile[$key] != '') {
				$complete++;
			}
		}
		$progress = empty($fields) ? 0 : floor($complete / count($fields) * 100);
		table_common_member_status::t()->update($uid, ['profileprogress' => $progress > 100 ? 100 : $progress], 'UNBUFFERED');
		return $progress;
	}
}

function get_constellation($birthmonth, $birthday) {
	$birthmonth = intval($birthmonth);
	$birthday = intval($birthday);
	$idx = $birthmonth;
	if($birthday <= 22) {
		if(1 == $birthmonth) {
			$idx = 12;
		} else {
			$idx = $birthmonth - 1;
		}
	}
	return $idx > 0 && $idx <= 12 ? lang('space', 'constellation_'.$idx) : '';
}

function get_zodiac($birthyear) {
	$birthyear = intval($birthyear);
	$idx = (($birthyear - 1900) % 12) + 1;
	return $idx > 0 && $idx <= 12 ? lang('space', 'zodiac_'.$idx) : '';
}

function isprofileimage($file) {
	return is_file(getglobal('setting/attachdir').'./profile/'.$file) && str_starts_with(realpath(getglobal('setting/attachdir').'./profile/'.$file), realpath(getglobal('setting/attachdir').'./profile/').DIRECTORY_SEPARATOR) && in_array(fileext($file), ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp']);
}

