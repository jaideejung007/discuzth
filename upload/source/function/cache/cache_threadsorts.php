<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_threadsorts() {
	$sortlist = $templatedata = $stemplatedata = $ptemplatedata = $btemplatedata = $template = $super = [];
	$query = table_forum_threadtype::t()->fetch_all_for_cache();
	foreach($query as $data) {
		$data['rules'] = dunserialize($data['rules']);
		$sortid = $data['sortid'];
		$optionid = $data['optionid'];
		$sortlist[$sortid][$optionid] = [
			'title' => dhtmlspecialchars($data['title']),
			'type' => dhtmlspecialchars($data['type']),
			'unit' => dhtmlspecialchars($data['unit']),
			'identifier' => dhtmlspecialchars($data['identifier']),
			'description' => dhtmlspecialchars($data['description']),
			'permprompt' => $data['permprompt'],
			'required' => intval($data['required']),
			'unchangeable' => intval($data['unchangeable']),
			'search' => intval($data['search']),
			'subjectshow' => intval($data['subjectshow']),
			'expiration' => intval($data['expiration']),
			'protect' => dunserialize($data['protect']),
		];

		if(in_array($data['type'], ['select', 'checkbox', 'radio'])) {
			if($data['rules']['choices']) {
				$choices = [];
				foreach(explode("\n", $data['rules']['choices']) as $item) {
					list($index, $choice) = explode('=', $item);
					$choices[trim($index)] = trim($choice);
				}
				$sortlist[$sortid][$optionid]['choices'] = $choices;
			} else {
				$sortlist[$sortid][$optionid]['choices'] = [];
			}
			if($data['type'] == 'select') {
				$sortlist[$sortid][$optionid]['inputsize'] = $data['rules']['inputsize'] ? intval($data['rules']['inputsize']) : 108;
			}
		} elseif(in_array($data['type'], ['text', 'textarea', 'calendar'])) {
			$sortlist[$sortid][$optionid]['maxlength'] = intval($data['rules']['maxlength']);
			if($data['type'] == 'textarea') {
				$sortlist[$sortid][$optionid]['rowsize'] = $data['rules']['rowsize'] ? intval($data['rules']['rowsize']) : 5;
				$sortlist[$sortid][$optionid]['colsize'] = $data['rules']['colsize'] ? intval($data['rules']['colsize']) : 50;
			} else {
				$sortlist[$sortid][$optionid]['inputsize'] = $data['rules']['inputsize'] ? intval($data['rules']['inputsize']) : '';
			}
			if(in_array($data['type'], ['text', 'textarea'])) {
				$sortlist[$sortid][$optionid]['defaultvalue'] = $data['rules']['defaultvalue'];
			}
			if($data['type'] == 'text') {
				$sortlist[$sortid][$optionid]['profile'] = $data['rules']['profile'];
			}
		} elseif($data['type'] == 'image') {
			$sortlist[$sortid][$optionid]['maxwidth'] = intval($data['rules']['maxwidth']);
			$sortlist[$sortid][$optionid]['maxheight'] = intval($data['rules']['maxheight']);
			$sortlist[$sortid][$optionid]['inputsize'] = $data['rules']['inputsize'] ? intval($data['rules']['inputsize']) : '';
		} elseif(in_array($data['type'], ['number', 'range'])) {
			$sortlist[$sortid][$optionid]['inputsize'] = $data['rules']['inputsize'] ? intval($data['rules']['inputsize']) : '';
			$sortlist[$sortid][$optionid]['maxnum'] = intval($data['rules']['maxnum']);
			$sortlist[$sortid][$optionid]['minnum'] = intval($data['rules']['minnum']);
			if($data['rules']['searchtxt']) {
				$sortlist[$sortid][$optionid]['searchtxt'] = explode(',', $data['rules']['searchtxt']);
			}
			if($data['type'] == 'number') {
				$sortlist[$sortid][$optionid]['defaultvalue'] = $data['rules']['defaultvalue'];
			}
		} elseif($data['type'] == 'plugin') {
			$sortlist[$sortid][$optionid]['pluginthreadtype'] = $data['rules']['pluginthreadtype'];
			$sortlist[$sortid][$optionid]['pluginthreadtype_param'] = $data['rules']['pluginthreadtype_param'];
		}
		$super[$sortid] = dunserialize($data['super']);
	}
	$query = table_forum_threadtype::t()->range();
	foreach($query as $data) {
		$stemplatedata[$data['typeid']] = loadThreadtypeTemplate($data['stemplate']);
		$ptemplatedata[$data['typeid']] = loadThreadtypeTemplate($data['ptemplate']);
		$btemplatedata[$data['typeid']] = loadThreadtypeTemplate($data['btemplate']);
		$templatedata[$data['typeid']] = loadThreadtypeTemplate($data['template']);
	}

	$data['sortoption'] = $data['template'] = [];

	foreach($sortlist as $sortid => $option) {
		$template['viewthread'] = $templatedata[$sortid];
		$template['subject'] = $stemplatedata[$sortid];
		$template['post'] = $ptemplatedata[$sortid];
		$template['block'] = $btemplatedata[$sortid];
		$template['super'] = $super[$sortid];

		savecache('threadsort_option_'.$sortid, $option);
		savecache('threadsort_template_'.$sortid, $template);
	}

}

function loadThreadtypeTemplate($data) {
	if(!preg_match('/\[file\](.+?)\[\/file\]/', trim($data), $r)) {
		return addcslashes($data, '",\\');
	}
	[$pluginid, $tpl] = explode(':', $r[1]);
	if(!$tpl) {
		if(!preg_match('/^[\\/\w+$/', $pluginid)) {
			return addcslashes($data, '",\\');
		}
		$tpls = [
			DISCUZ_ROOT.'./template/default/'.$pluginid.'.php',
			DISCUZ_ROOT.'./template/default/touch/'.$pluginid.'.php',
		];
	} else {
		if(!preg_match('/^[\\/\w]+$/', $tpl)) {
			return addcslashes($data, '",\\');
		}
		$tpls = [
			DISCUZ_PLUGIN($pluginid).'/template/'.$tpl.'.php',
			DISCUZ_PLUGIN($pluginid).'/template/touch/'.$tpl.'.php',
		];
	}

	if(!file_exists($tpls[0])) {
		return addcslashes($data, '",\\');
	}
	$content = implode("\n", array_slice(file($tpls[0]), 1));

	if(!file_exists($tpls[1])) {
		return [addcslashes($content, '",\\'), addcslashes($content, '",\\')];
	}
	$h5content = implode("\n", array_slice(file($tpls[1]), 1));
	return [addcslashes($content, '",\\'), addcslashes($h5content, '",\\')];
}
