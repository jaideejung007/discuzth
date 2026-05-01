<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

function import_smilies() {
	$smileyarray = getimportdata('Discuz! Smilies');

	$renamed = 0;
	if(table_forum_imagetype::t()->count_by_name('smiley', $smileyarray['name'])) {
		$smileyarray['name'] .= '_'.random(4);
		$renamed = 1;
	}
	$data = [
		'name' => $smileyarray['name'],
		'type' => 'smiley',
		'directory' => $smileyarray['directory'],
	];
	$typeid = table_forum_imagetype::t()->insert($data, true);


	foreach($smileyarray['smilies'] as $key => $smiley) {
		table_common_smiley::t()->insert(['type' => 'smiley', 'typeid' => $typeid, 'displayorder' => $smiley['displayorder'], 'code' => '', 'url' => $smiley['url']]);
	}
	table_common_smiley::t()->update_code_by_typeid($typeid);

	updatecache(['smileytypes', 'smilies', 'smileycodes', 'smilies_js']);
	return $renamed;
}

function import_styles($ignoreversion = 1, $dir = '', $restoreid = 0, $updatecache = 1, $validate = 1) {
	global $_G, $importtxt, $stylearray;
	if(empty($dir)) {
		$stylearrays = [getimportdata('Discuz! Style')];
	} else {
		require_once libfile('function/cloudaddons');
		if(!$restoreid) {
			$dir = str_replace(['/', '\\'], '', $dir);
			$templatedir = DISCUZ_TEMPLATE('./template/'.$dir);
			if($validate) {
				cloudaddons_validator($dir.'.template');
			}
		} else {
			$templatedir = DISCUZ_TEMPLATE($dir);
			$dir = basename($dir);
			if($validate) {
				cloudaddons_validator($dir.'.template');
			}
		}
		$searchdir = dir($templatedir);
		$stylearrays = [];
		while($searchentry = $searchdir->read()) {
			if(str_starts_with($searchentry, 'discuz_style_') && (fileext($searchentry) == 'xml' || fileext($searchentry) == 'json')) {
				$importfile = $templatedir.'/'.$searchentry;
				$importtxt = implode('', file($importfile));
				$stylearrays[] = getimportdata('Discuz! Style');
			}
		}
	}

	foreach($stylearrays as $stylearray) {
		if(empty($ignoreversion) && !versioncompatible($stylearray['version'])) {
			cpmsg('styles_import_version_invalid', 'action=styles', 'error', ['cur_version' => $stylearray['version'], 'set_version' => $_G['setting']['version']]);
		}
		$styleidnew = 0;
		if(!$restoreid) {
			$renamed = 0;
			if($stylearray['templateid'] != 1) {
				$templatedir = DISCUZ_TEMPLATE($stylearray['directory']);
				if(!is_dir($templatedir)) {
					if(!@mkdir($templatedir, 0777)) {
						$basedir = dirname($stylearray['directory']);
						cpmsg('styles_import_directory_invalid', 'action=styles', 'error', ['basedir' => $basedir, 'directory' => $stylearray['directory']]);
					}
				}
				$templateid = table_common_template::t()->get_templateid_by_directory($stylearray['directory']);
				if(!$templateid) {
					$templateid = table_common_template::t()->get_templateid($stylearray['tplname']);
				}
				if(!$templateid) {
					$templateid = table_common_template::t()->insert([
						'name' => $stylearray['tplname'],
						'directory' => $stylearray['directory'],
						'copyright' => $stylearray['copyright']
					], true);
				}
			} else {
				$templateid = 1;
			}

			if(table_common_style::t()->check_stylename($stylearray['name'])) {
				$renamed = 1;
				$styleinfo = table_common_style::t()->fetch_by_stylename_templateid($stylearray['name']);
				if(!empty($styleinfo['styleid'])) {
					if($styleinfo['templateid'] != $templateid) {
						$template = table_common_template::t()->fetch_by_templateid($styleinfo['templateid']);
						if(empty($template)) {
							table_common_style::t()->update($styleinfo['styleid'], ['templateid' => $templateid], true);
							$styleidnew = $styleinfo['styleid'];
						} else {
							$styleinfo = table_common_style::t()->fetch_by_stylename_templateid($stylearray['name'], $templateid);
							if(!empty($styleinfo['styleid'])) {
								$styleidnew = $styleinfo['styleid'];
							} else {
								$styleidnew = table_common_style::t()->insert(['name' => $stylearray['name'], 'templateid' => $templateid], true);
							}
						}
					} else {
						$styleidnew = $styleinfo['styleid'];
					}
				}
			} else {
				$styleidnew = table_common_style::t()->insert(['name' => $stylearray['name'], 'templateid' => $templateid, 'version' => $stylearray['style']['version']], true);
			}
		} else {
			$styleidnew = $restoreid;
			table_common_stylevar::t()->delete_by_styleid($styleidnew);
			table_common_stylevar_extra::t()->delete_by_styleid($styleidnew);
		}

		if($styleidnew) {
			$stylevars = [];
			$result = table_common_stylevar::t()->fetch_all_by_styleid($styleidnew);
			if(is_array($result) && !empty($result)) {
				foreach($result as $style) {
					$stylevars[$style['variable']] = $style['substitute'];
				}
			}
			foreach($stylearray['style'] as $variable => $substitute) {
				if(!isset($stylevars[$variable])) {
					$substitute = @dhtmlspecialchars($substitute);
					table_common_stylevar::t()->insert(['styleid' => $styleidnew, 'variable' => $variable, 'substitute' => $substitute]);
				}
			}

			$stylevarextras = [];
			$result = table_common_stylevar_extra::t()->fetch_all_by_styleid($styleidnew);
			if(is_array($result) && !empty($result)) {
				foreach($result as $style) {
					$stylevarextras[$style['variable']] = $style;
				}
			}

			foreach($stylearray['var'] as $variable => $data) {
				if(!isset($stylevarextras[$variable])) {
					table_common_stylevar_extra::t()->insert(
						[
							'styleid' => $styleidnew,
							'displayorder' => $data['displayorder'],
							'title' => @dhtmlspecialchars($data['title']),
							'description' => @dhtmlspecialchars($data['description']),
							'variable' => $data['variable'],
							'type' => $data['type'],
							'value' => is_array($data['value']) ? serialize($data['value']) : $data['value'],
							'extra' => $data['extra'],
						]
					);
				}
			}

			foreach(table_common_stylevar_extra::t()->fetch_all_by_styleid($styleidnew) as $var) {
				unset($var['stylevarid']);
				unset($var['styleid']);
				$stylearray['var'][$var['variable']] = $var;
			}
		}
	}

	if($dir) {
		cloudaddons_installlog($dir.'.template');
		cloudaddons_clear('template', $dir);
	}

	if($updatecache) {
		updatecache('styles');
		updatecache('setting');
	}
	return $renamed;
}

function import_block($xmlurl, $clientid, $xmlkey = '', $signtype = '', $ignoreversion = 1, $update = 0) {
	global $_G, $importtxt;
	$_GET['importtype'] = $_GET['importtxt'] = '';
	$xmlurl = strip_tags($xmlurl);
	$clientid = strip_tags($clientid);
	$xmlkey = strip_tags($xmlkey);
	$parse = parse_url($xmlurl);
	if(!empty($parse['host'])) {
		$queryarr = explode('&', $parse['query']);
		$para = [];
		foreach($queryarr as $value) {
			$k = $v = '';
			list($k, $v) = explode('=', $value);
			if(!empty($k) && !empty($v)) {
				$para[$k] = $v;
			}
		}
		$para['clientid'] = $clientid;
		$para['op'] = 'getconfig';
		$para['charset'] = CHARSET;
		$signurl = create_sign_url($para, $xmlkey, $signtype);
		$pos = strpos($xmlurl, '?');
		$pos = $pos === false ? strlen($xmlurl) : $pos;
		$signurl = substr($xmlurl, 0, $pos).'?'.$signurl;
		$importtxt = @dfsockopen($signurl);
	} else {
		$importtxt = @implode('', file($xmlurl));
	}
	$blockarrays = getimportdata('Discuz! Block', 0);
	if(empty($blockarrays['name']) || empty($blockarrays['fields']) || empty($blockarrays['getsetting'])) {
		cpmsg(cplang('import_data_typeinvalid').cplang($importtxt), '', 'error');
	}
	require_once libfile('function/cloudaddons');
	if(empty($ignoreversion) && !versioncompatible($blockarrays['version'])) {
		cpmsg(cplang('blockxml_import_version_invalid'), '', 'error', ['cur_version' => $blockarrays['version'], 'set_version' => $_G['setting']['version']]);
	}
	$data = [
		'name' => dhtmlspecialchars($blockarrays['name']),
		'version' => dhtmlspecialchars($blockarrays['version']),
		'url' => $xmlurl,
		'clientid' => $clientid,
		'key' => $xmlkey,
		'signtype' => !empty($signtype) ? 'MD5' : '',
		'data' => serialize($blockarrays)
	];
	if(!$update) {
		table_common_block_xml::t()->insert($data);
	} else {
		table_common_block_xml::t()->update($update, $data);
	}
}

function create_sign_url($para, $key = '', $signtype = '') {
	ksort($para);
	$url = http_build_query($para);
	if(!empty($signtype) && strtoupper($signtype) == 'MD5') {
		$sign = md5(urldecode($url).$key);
		$url = $url.'&sign='.$sign;
	} else {
		$url = $url.'&sign='.$key;
	}
	return $url;
}

