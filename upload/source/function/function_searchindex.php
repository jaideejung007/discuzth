<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function searchindex_cache() {
	global $_G;
	include_once DISCUZ_ROOT.'./source/discuz_version.php';

	$siteurl = $_G['siteurl'];
	$_G['siteurl'] = '';
	require DISCUZ_ROOT.'./source/i18n/'.currentlang().'/lang_admincp_menu.php';
	$menulang = $lang;
	require DISCUZ_ROOT.'./source/i18n/'.currentlang().'/lang_admincp.php';
	$_G['genlang'] = $lang + $menulang;
	$_G['genlangi'] = '|'.implode('|', array_keys($_G['genlang'])).'|';
	$_G['siteurl'] = $siteurl;
	$indexdata = [];

	require appfile('module/menu', 'admin');
	foreach($menu as $topmenu => $leftmenu) {
		foreach($leftmenu as $item) {
			if(!isset($item[2]) && isset($menulang[$item[0]])) {
				list($action, $operation, $do) = explode('_', $item[1]);
				$indexdata[] = ['index' => [
					$menulang[$item[0]] => 'action='.$action.($operation ? '&operation='.$operation.($do ? '&do='.$do : '') : '')
				], 'text' => [$menulang[$item[0]]]];
			}
		}
	}

	get_searchindex_dir(DISCUZ_ROOT.'./source/app/admin/module/', $indexdata);
	get_searchindex_dir(DISCUZ_ROOT.'./source/app/admin/child/', $indexdata);
	savecache('adminsearchindex', $indexdata);
	$_G['cache']['adminsearchindex'] = $indexdata;
}

function get_searchindex_dir($root, &$indexdata) {
	$flag = false;
	$dir = opendir($root);
	if($dir) {
		while($entry = readdir($dir)) {
			if($entry != '.' && $entry != '..') {
				$adminfile = $root.$entry;
				if(is_dir($adminfile)) {
					$_flag = get_searchindex_dir($adminfile.'/', $indexdata);
					$flag = $_flag || $flag;
				} elseif(fileext($entry) == 'php') {
					$_flag = get_searchindex($adminfile, $indexdata);
					$flag = $_flag || $flag;
				}
			}
		}
	}
	return $flag;
}

function get_searchindex($adminfile, &$indexdata) {
	global $_G;

	$flag = false;

	$data = file_get_contents($adminfile);
	$data = preg_replace('/\/\/.+?\r/', '', $data);
	$data = preg_replace_callback(
		'/\/\*(.+?)\*\//s',
		function($matches) {
			if(!preg_match('/^search/i', $matches[1])) {
				return '';
			} else {
				return '/*'.$matches[1].'*/';
			}
		},
		$data
	);
	$isfullindex = preg_match_all('#/\*search=\s*(\{.+?\})\s*\*/(.+?)/\*search\*/#is', $data, $search);
	if($isfullindex) {
		foreach($search[0] as $k => $item) {
			$search[1][$k] = stripslashes($search[1][$k]);
			$titles = json_decode($search[1][$k], 1);
			$titlesnew = $titletext = [];
			foreach($titles as $title => $url) {
				$titlekey = strip_tags($_G['genlang'][$title] ?? $title);
				$titlesnew[$titlekey] = $url;
				if($titlekey[0] != '_') {
					$titletext[] = $titlekey;
				}
			}
			$data = $search[2][$k];
			$l = $tm = [];
			preg_match_all("/(showsetting|showtitle|showtableheader|showtips|showcomponent)\('(\w+)'/", $data, $r);
			if($r[2]) {
				if($titletext) {
					$l[] = implode(' &raquo; ', $titletext);
				}
				foreach($r[2] as $i) {
					if(in_array($i, $tm)) {
						continue;
					}
					$tm[] = $i;
					$l[] = strip_tags($i);
					$l[] = strip_tags($_G['genlang'][$i]);
					$preg = '/\|('.preg_quote($i).'_comment)\|/';
					preg_match_all($preg, $_G['genlangi'], $lr);
					if($lr[1]) {
						foreach($lr[1] as $li) {
							$l[] = strip_tags($_G['genlang'][$li]);
						}
					}
				}
			}

			preg_match_all("/\\\$lang\['(\w+)'\]/", $data, $r);
			if($r[1]) {
				if(empty($l) && $titletext) {
					$l[] = implode(' &raquo; ', $titletext);
				}
				foreach($r[1] as $i) {
					if(in_array($i, $tm)) {
						continue;
					}
					$tm[] = $i;
					$l[] = strip_tags($i);
					$l[] = strip_tags($_G['genlang'][$i]);
					$preg = '/\|('.preg_quote($i).'_comment)\|/';
					preg_match_all($preg, $_G['genlangi'], $lr);
					if($lr[1]) {
						foreach($lr[1] as $li) {
							$l[] = strip_tags($_G['genlang'][$li]);
						}
					}
				}
			}
			if(!empty($l)) {
				$indexdata[] = ['index' => $titlesnew, 'text' => $l];
				$flag = true;
			}
		}
	}

	return $flag;
}