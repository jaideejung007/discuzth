<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_styles() {
	global $_G;

	$stylevars = $styledata = $styleconsts = $stylesetting = [];
	$defaultstyleid = $_G['setting']['styleid'];
	foreach(table_common_stylevar::t()->range() as $var) {
		$stylevars[$var['styleid']][$var['variable']] = $var['substitute'];
		if(!in_array($var['variable'], ['imgdir', 'stypeid', 'available'])) {
			$var['substitute'] = str_replace(['{', '}'], '', $var['substitute']);
			$styleconsts[$var['styleid']]['{'.strtoupper($var['variable']).'}'] = $var['substitute'];
		}
	}
	savecache('styleconsts', $styleconsts);
	unset($styleconsts);

	foreach(table_common_style::t()->fetch_all_data(true) as $data) {
		$data['tpldir'] = $data['directory'];
		$data = array_merge($data, (array)$stylevars[$data['styleid']]);
		$datanew = [];
		$data['imgdir'] = $data['imgdir'] ? $data['imgdir'] : STATICURL.'image/common';
		$data['styleimgdir'] = $data['styleimgdir'] ? $data['styleimgdir'] : $data['imgdir'];
		foreach($data as $k => $v) {
			if(str_ends_with($k, 'bgcolor')) {
				$newkey = substr($k, 0, -7).'bgcode';
				$datanew[$newkey] = setcssbackground($data, $k);
			}
		}
		$data = array_merge($data, $datanew);
		if(str_contains($data['boardimg'], ',')) {
			$flash = explode(',', $data['boardimg']);
			$flash[0] = trim($flash[0]);
			$flash[0] = preg_match('/^(https?:)?\/\//i', $flash[0]) ? $flash[0] : $data['styleimgdir'].'/'.$flash[0];
			$data['boardlogo'] = "<embed src=\"".$flash[0]."\" width=\"".trim($flash[1])."\" height=\"".trim($flash[2])."\" type=\"application/x-shockwave-flash\" wmode=\"transparent\"></embed>";
		} else {
			$data['boardimg'] = empty($data['boardimg']) ? $data['imgdir'].'/logo.svg' : (preg_match('/^(https?:)?\/\//i', $data['boardimg']) || file_exists($data['boardimg']) ? '' : (file_exists($data['styleimgdir'].'/'.$data['boardimg']) ? $data['styleimgdir'].'/' : $data['imgdir'].'/')).$data['boardimg'];
			$data['boardlogo'] = "<img src=\"{$data['boardimg']}\" alt=\"".$_G['setting']['bbname']."\" class=\"boardlogo\" id=\"boardlogo\" border=\"0\" />";
		}
		$data['searchimg'] = empty($data['searchimg']) ? $data['imgdir'].'/logo_sc.svg' : (preg_match('/^(https?:)?\/\//i', $data['searchimg']) || file_exists($data['searchimg']) ? '' : (file_exists($data['styleimgdir'].'/'.$data['searchimg']) ? $data['styleimgdir'].'/' : $data['imgdir'].'/')).$data['searchimg'];
		$data['searchlogo'] = "<img src=\"{$data['searchimg']}\" alt=\"".$_G['setting']['bbname']."\" class=\"searchlogo\" id=\"searchlogo\" border=\"0\" />";
		$data['touchimg'] = empty($data['touchimg']) ? $data['imgdir'].'/logo_m.svg' : (preg_match('/^(https?:)?\/\//i', $data['touchimg']) || file_exists($data['touchimg']) ? '' : (file_exists($data['styleimgdir'].'/'.$data['touchimg']) ? $data['styleimgdir'].'/' : $data['imgdir'].'/')).$data['touchimg'];
		$data['touchlogo'] = "<img src=\"{$data['touchimg']}\" alt=\"".$_G['setting']['bbname']."\" class=\"touchlogo\" id=\"touchlogo\" border=\"0\" />";
		$data['bold'] = $data['nobold'] ? 'normal' : 'bold';
		$contentwidthint = intval($data['contentwidth']);
		$contentwidthint = $contentwidthint ? $contentwidthint : 600;
		if($data['extstyle']) {
			[$data['extstyle'], $data['defaultextstyle']] = explode('|', $data['extstyle']);
			$extstyle = explode("\t", $data['extstyle']);
			$data['extstyle'] = [];
			foreach($extstyle as $dir) {
				if(file_exists($extstylefile = DISCUZ_ROOT.$data['tpldir'].'/style/'.$dir.'/style.css')) {
					if($data['defaultextstyle'] == $dir) {
						$data['defaultextstyle'] = $data['tpldir'].'/style/'.$dir;
					}
					$content = file_get_contents($extstylefile);
					if(preg_match('/\[name\](.+?)\[\/name\]/i', $content, $r1) && preg_match('/\[iconbgcolor](.+?)\[\/iconbgcolor]/i', $content, $r2)) {
						$data['extstyle'][] = [$data['tpldir'].'/style/'.$dir, $r1[1], $r2[1]];
					}
				}
			}
		}
		$data['templatelang'] = file_exists(DISCUZ_TEMPLATE($data['tpldir']).'/i18n/'.currentlang().'/lang_template.php');

		foreach(table_common_stylevar_extra::t()->fetch_all_by_styleid($data['styleid']) as $var) {
			$_v = dunserialize($var['value']);
			$data[$var['variable']] = is_array($_v) ? $_v : $var['value'];
			if(strexists($var['type'], ':') || str_starts_with($var['type'], 'component_')) {
				admin\class_component::plugin_unserialize($var['type'], $data[$var['variable']]);
			}
			if($var['type'] == 'groupfids') {
				$data[$var['variable']] = explode(',', $data[$var['variable']]);
			}

			if(in_array(substr($var['type'], 0, 6), ['group_', 'forum_'])) {
				$stype = substr($var['type'], 0, 5).'s';
				$type = substr($var['type'], 6);
				if($type == 'select') {
					foreach(explode("\n", $var['extra']) as $key => $option) {
						$option = trim($option);
						if(!str_contains($option, '=')) {
							$key = $option;
						} else {
							$item = explode('=', $option);
							$key = trim($item[0]);
							$option = trim($item[1]);
						}
						$var['select'][] = [$key, $option];
					}
				}
				$stylesetting[$stype][$data['styleid']]['name'] = $data['name'];
				$stylesetting[$stype][$data['styleid']]['setting'][$var['stylevarid']] = ['title' => $var['title'], 'description' => $var['description'], 'type' => $type, 'select' => $var['select']];
			}
		}

		$data['verhash'] = random(3);
		$styledata[] = $data;
	}

	savecache('stylesetting', $stylesetting);
	foreach($styledata as $data) {
		savecache('style_'.$data['styleid'], $data);
		if($defaultstyleid == $data['styleid']) {
			savecache('style_default', $data);
		}
		writetocsscache($data);
	}

}

function setcssbackground(&$data, $code) {
	$codes = explode(' ', $data[$code]);
	$css = $codevalue = '';
	for($i = 0; $i < count($codes); $i++) {
		if($i < 2) {
			if($codes[$i] != '') {
				if($codes[$i][0] == '#') {
					$css .= strtoupper($codes[$i]).' ';
					$codevalue = strtoupper($codes[$i]);
				} elseif(preg_match('/^(https?:)?\/\//i', $codes[$i])) {
					$css .= 'url("'.$codes[$i].'") ';
				} else {
					$css .= 'url("'.$data['styleimgdir'].'/'.$codes[$i].'") ';
				}
			}
		} else {
			$css .= $codes[$i].' ';
		}
	}
	$data[$code] = $codevalue;
	$css = trim($css);
	return $css ? 'background: '.$css : '';
}

function writetocsscache($data) {
	global $_G;
	$dir = DISCUZ_TEMPLATE('./template/default/common/');
	$dh = opendir($dir);
	$data['staticurl'] = STATICURL;
	while(($entry = readdir($dh)) !== false) {
		if(fileext($entry) == 'css') {
			$cssfile = DISCUZ_TEMPLATE('./'.$data['tpldir'].'/common/'.$entry);
			!tplfile::file_exists($cssfile) && $cssfile = $dir.$entry;
			$cssdata = tplfile::file_get_contents($cssfile);
			if(tplfile::file_exists($cssfile = DISCUZ_TEMPLATE('./'.$data['tpldir'].'/common/extend_'.$entry))) {
				$cssdata .= tplfile::file_get_contents($cssfile);
			}
			if(is_array($_G['setting']['plugins']['available']) && $_G['setting']['plugins']['available']) {
				foreach($_G['setting']['plugins']['available'] as $plugin) {
					if(file_exists($cssfile = DISCUZ_PLUGIN($plugin).'/template/extend_'.$entry)) {
						$cssdata .= @implode('', file($cssfile));
					}
				}
			}

			writetocsscache_callback_1($data, 1);

			$cssdata = preg_replace_callback('/\{([A-Z0-9]+)\}/', 'writetocsscache_callback_1', $cssdata);
			$cssdata = preg_replace('/<\?.+?\?>\s*/', '', $cssdata);
			$cssdata = !preg_match('/^(https?:)?\/\//i', $data['styleimgdir']) ? preg_replace("/url\(([\"'])?".preg_quote($data['styleimgdir'], '/').'/i', "url(\\1{$_G['siteurl']}{$data['styleimgdir']}", $cssdata) : $cssdata;
			$cssdata = !preg_match('/^(https?:)?\/\//i', $data['imgdir']) ? preg_replace("/url\(([\"'])?".preg_quote($data['imgdir'], '/').'/i', "url(\\1{$_G['siteurl']}{$data['imgdir']}", $cssdata) : $cssdata;
			$cssdata = !preg_match('/^(https?:)?\/\//i', $data['staticurl']) ? preg_replace("/url\(([\"'])?".preg_quote($data['staticurl'], '/').'/i', "url(\\1{$_G['siteurl']}{$data['staticurl']}", $cssdata) : $cssdata;
			if($entry == 'module.css') {
				$cssdata = preg_replace('/\/\*\*\s*(.+?)\s*\*\*\//', '[\\1]', $cssdata);
			}
			$cssdata = preg_replace(['/\s*([,;:\{\}])\s*/', '/[\t\n\r]/', '/\/\*.+?\*\//'], ['\\1', '', ''], $cssdata);
			$cachedir = DISCUZ_DATA.'./cache/';
			if(!is_dir($cachedir)) {
				dmkdir($cachedir);
			}
			if(file_put_contents($cachedir.'style_'.$data['styleid'].'_'.$entry, $cssdata, LOCK_EX) === false) {
				exit('Can not write to cache files, please check directory ./data/ and ./data/cache/ .');
			}
			if(defined('IN_UPDATECACHE')) {
				oss::writeCache('style_'.$data['styleid'].'_'.$entry);
			}
		}
	}
}

function writetocsscache_callback_1($matches, $action = 0) {
	static $data = [];

	if($action == 1) {
		$data = $matches;
	} else {
		return $data[strtolower($matches[1])];
	}
}

