<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

loadcache('adminsearchindex');

if(empty($_G['cache']['adminsearchindex'])) {
	require_once libfile('function/searchindex');
	searchindex_cache();
}

if(empty($_G['cache']['adminsearchindex'])) {
	cpmsg('searchindex_not_found', '', 'error');
}

$searchindex = &$_G['cache']['adminsearchindex'];

$keywords = trim($_GET['keywords']);
if(str_starts_with($keywords, '(') && str_ends_with($keywords, ')')) {
	$kws = [substr($keywords, 1, -1)];
} else {
	$kws = explode(' ', $keywords);
}
$kws = array_map('trim', $kws);
$kws = array_filter($kws);
$keywords = implode(' ', $kws);

$result = $html = [];

if($_GET['searchsubmit'] && $keywords) {
	foreach($searchindex as $skey => $items) {
		foreach($kws as $kw) {
			foreach($items['text'] as $k => $text) {
				if(str_contains(strtolower($text), strtolower($kw))) {
					$result[$skey][] = $k;
				}
			}
		}
	}
	if($result) {
		$totalcount = 0;
		foreach($result as $skey => $tkeys) {
			$tmp = [];
			foreach($searchindex[$skey]['index'] as $title => $url) {
				if($title[0] != '_') {
					$tmp[] = '<a href="'.ADMINSCRIPT.'?'.$url.'&highlight='.rawurlencode($keywords).'"  target="_blank">'.$title.'</a>';
				}
			}
			$texts = [];
			$tkeys = array_unique($tkeys);
			foreach($tkeys as $tkey) {
				if(isset($lang[$searchindex[$skey]['text'][$tkey]])) {
					$texts[] = '<li><span s="1">'.strip_tags($lang[$searchindex[$skey]['text'][$tkey]]).'</span><span s="1" class="lightfont">('.$searchindex[$skey]['text'][$tkey].')</span></li>';
				} else {
					$texts[] = '<li><span s="1">'.$searchindex[$skey]['text'][$tkey].'</span></li>';
				}
			}
			$texts = array_unique($texts);
			$texts = implode('', $texts);
			$totalcount += $count = count($tkeys);
			$html[] = '<div class="news"><span class="right">'.cplang('search_result_item', ['number' => $count]).'</span><b>'.implode(' &raquo; ', $tmp).'</b></div><ul class="tipsblock">'.$texts.'</ul>';
		}
		if($totalcount) {
			showsubmenu('search_result', [], '<span class="right">'.cplang('search_result_find', ['number' => $totalcount]).'</span>');
			showboxheader();
			echo implode('<br />', $html);
			hlkws($kws);
			showboxfooter();
		} else {
			cpmsg('search_result_noexists', '', 'error');
		}
	} else {
		cpmsg('search_result_noexists', '', 'error');
	}
} else {
	cpmsg('search_keyword_noexists', '', 'error');
}

function hlkws($kws) {
	echo <<<EOF
<script type="text/JavaScript">
_attachEvent(window, 'load', function () {
EOF;
	foreach($kws as $kw) {
		echo 'parsetag(\''.$kw.'\');';
	}
	echo '}, document)</script>';
}

