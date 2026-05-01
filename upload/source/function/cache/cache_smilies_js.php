<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_smilies_js() {
	global $_G;

	$fastsmiley = table_common_setting::t()->fetch_setting('fastsmiley', true);
	$return_type = 'var smilies_type = new Array();';
	$return_array = 'var smilies_array = new Array();var smilies_fast = new Array();';
	$spp = $_G['setting']['smcols'] * $_G['setting']['smrows'];
	$fpre = '';
	foreach(table_forum_imagetype::t()->fetch_all_by_type('smiley', 1) as $type) {
		$return_data = [];
		$return_datakey = '';
		$i = 0;
		$j = 1;
		$pre = '';
		$return_type .= 'smilies_type[\'_'.$type['typeid'].'\'] = [\''.str_replace('\'', '\\\'', $type['name']).'\', \''.str_replace('\'', '\\\'', $type['directory']).'\'];';
		$return_datakey .= 'smilies_array['.$type['typeid'].'] = new Array();';
		foreach(table_common_smiley::t()->fetch_all_by_type_code_typeid('smiley', $type['typeid']) as $smiley) {
			if($i >= $spp) {
				$return_data[$j] = 'smilies_array['.$type['typeid'].']['.$j.'] = ['.$return_data[$j].'];';
				$j++;
				$i = 0;
				$pre = '';
			}
			if($type['directory'] == ':emoji') {
				$smileyid = $smiley['id'];
				$return_data[$j] .= $pre.'[\''.$smileyid.'\', \''.$smiley['code'].'\']';
				if(is_array($fastsmiley[$type['typeid']]) && in_array($smileyid, $fastsmiley[$type['typeid']])) {
					$return_fast .= $fpre.'[\''.$type['typeid'].'\',\''.$j.'\',\''.$i.'\']';
					$fpre = ',';
				}
				$pre = ',';
			} elseif($size = @getimagesize(DISCUZ_ROOT.'./static/image/smiley/'.$type['directory'].'/'.$smiley['url'])) {
				$smiley['code'] = str_replace('\'', '\\\'', $smiley['code']);
				$smileyid = $smiley['id'];
				$s = smthumb($size, $_G['setting']['smthumb']);
				$smiley['w'] = $s['w'];
				$smiley['h'] = $s['h'];
				$l = smthumb($size);
				$smiley['lw'] = $l['w'];
				unset($smiley['id'], $smiley['directory']);
				$return_data[$j] .= $pre.'[\''.$smileyid.'\', \''.$smiley['code'].'\',\''.str_replace('\'', '\\\'', $smiley['url']).'\',\''.$smiley['w'].'\',\''.$smiley['h'].'\',\''.$smiley['lw'].'\']';
				if(is_array($fastsmiley[$type['typeid']]) && in_array($smileyid, $fastsmiley[$type['typeid']])) {
					$return_fast .= $fpre.'[\''.$type['typeid'].'\',\''.$j.'\',\''.$i.'\']';
					$fpre = ',';
				}
				$pre = ',';
			}
			$i++;
		}
		$return_data[$j] = 'smilies_array['.$type['typeid'].']['.$j.'] = ['.$return_data[$j].'];';
		$return_array .= $return_datakey.implode('', $return_data);
	}
	$cachedir = DISCUZ_DATA.'./cache/';
	if(!is_dir($cachedir)) {
		dmkdir($cachedir);
	}
	$common_smilies_var_js = 'var smthumb = \''.$_G['setting']['smthumb'].'\';'.$return_type.$return_array.'var smilies_fast=['.$return_fast.'];';
	if(file_put_contents($cachedir.'common_smilies_var.js', $common_smilies_var_js, LOCK_EX) === false) {
		exit('Can not write to cache files, please check directory ./data/ and ./data/cache/ .');
	}
	if(defined('IN_UPDATECACHE')) {
		oss::writeCache('common_smilies_var.js');
	}
	if(!empty($_G['config']['plugindeveloper'])) {
		file_put_contents(DISCUZ_ROOT.'./static/js/common_smilies_var.js', $common_smilies_var_js, LOCK_EX);
	}
}

