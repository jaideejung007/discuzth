<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


function domessageformat(array $value, array $recommend_status): array {
	global $_G;
	$value['fields'] = json_decode($value['fields'], true);
	if(!empty($value['ip'])) {
		$value['ip'] = ip::to_display($value['ip']);
		$value['iplocation'] = $_G['setting']['showiplocation'] ? ip::convert($value['ip'], true) : '';
	}

	
	require_once libfile('function/discuzcode');
	$value['message'] = preg_replace_callback('/http[s]?:\/\/[a-zA-Z0-9\-\._~:\/?#[\]@!\$&\'\(\)\*\+,;=.]+/', function($matches) {
		$url = $matches[0];
		$url = preg_replace('/(&nbsp;)+$/', '', $url);
		$url = trim($url);
		if(isVideoUrl($url) || parseflv($url)) {
			$media = parsemedia('x,500,373', $url);
			return '<br />'.$media.'<br />';
		} elseif(isAudioUrl($url)) {
			$audio = parseaudio($url);
			return '<br />'.$audio.'<br />';
		} else {
			return '<a href="'.dhtmlspecialchars($url).'" target="_blank" rel="nofollow">'.dhtmlspecialchars($url).'</a>';
		}
	}, $value['message']);
	
	if(!empty($value['fields']['tags']) && is_array($value['fields']['tags'])) {
		foreach($value['fields']['tags'] as $tagid => $tag) {
			if(!empty($tag) && !empty($tagid)) {
				
				$tag_url = "home.php?mod=space&do=doing&tagid={$tagid}";
				$tag_link = "<a href=\"{$tag_url}\" class=\"tag doing_tag\" target=\"_blank\">#{$tag}#</a>";
				
				$value['message'] = preg_replace("/#{$tag}#/i", $tag_link, $value['message']);
				$value['message'] = preg_replace("/#{$tag}(?=\s|$)/i", $tag_link, $value['message'].' ');
			}
		}
	}
	$value['body_data'] = dunserialize($value['body_data']);
	$searchs = $replaces = [];
	if($value['body_data']) {
		foreach(array_keys($value['body_data']) as $key) {
			$searchs[] = '{'.$key.'}';
			$replaces[] = $value['body_data'][$key];
		}
		if($value['body_data']['image']) $value['image'] = $value['body_data']['image'];
		if($value['body_data']['image_link']) $value['image_link'] = $value['body_data']['image_link'];
	}
	$value['body_template'] = str_replace($searchs, $replaces, $value['body_template']);
	
	$value['recomends'] = $value['recomends'] ? $value['recomends'] : 0;
	$value['recommendstatus'] = isset($recommend_status[$value['doid']]) ? $recommend_status[$value['doid']] : 0;

	return $value;
}