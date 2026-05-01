<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */



if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_misctag extends discuz_block {

	function __construct(){
	}

	function name() {
		return lang('block/misctag', 'blockclass_misctag_script_misctag');
	}

	function blockclass() {
		return ['misctag', lang('block/misctag', 'blockclass_misctag_misctag')];
	}

	function fields() {
		return [
			'id' => ['name' => lang('block/misctag', 'blockclass_misctag_field_id'), 'formtype' => 'text', 'datatype' => 'int'],
			'url' => ['name' => lang('block/misctag', 'blockclass_misctag_field_url'), 'formtype' => 'text', 'datatype' => 'string'],
			'title' => ['name' => lang('block/misctag', 'blockclass_misctag_field_name'), 'formtype' => 'title', 'datatype' => 'title'],
			'related_count' => ['name' => lang('block/misctag', 'blockclass_misctag_field_related_count'), 'formtype' => 'text', 'datatype' => 'int'],
			'hot_score' => ['name' => lang('block/misctag', 'blockclass_misctag_field_hot_score'), 'formtype' => 'text', 'datatype' => 'string'],
			'size_level' => ['name' => lang('block/misctag', 'blockclass_misctag_field_size_level'), 'formtype' => 'text', 'datatype' => 'int'],
			'color_level' => ['name' => lang('block/misctag', 'blockclass_misctag_field_color_level'), 'formtype' => 'text', 'datatype' => 'int'],
		];
	}

	function getsetting() {
		global $_G;
		$settings = [
			'ids' => [
				'title' => 'misctag_ids',
				'type' => 'text'
			],
			'orderby' => [
				'title' => 'misctag_orderby',
				'type'=> 'mradio',
				'value' => [
					['tagid', 'misctag_orderby_tagid'],
					['hot_score', 'misctag_orderby_hot_score'],
					['related_count', 'misctag_orderby_related_count'],
					['rand', 'misctag_orderby_rand'],
				],
				'default' => 'rand'
			],
			'titlelength' => [
				'title' => 'misctag_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'startrow' => [
				'title' => 'misctag_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
		return $settings;
	}

	function getdata($style, $parameter) {
		global $_G;

		$parameter = $this->cookparameter($parameter);

		$tagids		= !empty($parameter['ids']) ? explode(',', $parameter['ids']) : [];
		$startrow	= !empty($parameter['startrow']) ? intval($parameter['startrow']) : 0;
		$items		= !empty($parameter['items']) ? intval($parameter['items']) : 10;
		$orderby	= isset($parameter['orderby']) ? (in_array($parameter['orderby'], ['tagid','hot_score', 'related_count','rand']) ? $parameter['orderby'] : 'tagid') : 'tagid';
		$titlelength	= !empty($parameter['titlelength']) ? intval($parameter['titlelength']) : 40;

		$list = [];
		$params = [];
		$order = '';

		$orderby = ($orderby == 'rand') ? 'rand' : $orderby;
		if($orderby != 'rand'){
			$order = 'DESC';
		}
		if($tagids) {
			$tags = table_common_tag::t()->get_byids($tagids);
		}else{
			$tags = table_common_tag::t()->fetch_all_by_hot(NULL, $startrow, $items, $order, $orderby);
		}
		$tags = $this->process_tags_by_hot_score($tags);
		foreach($tags as $data) {
			$list[] = [
				'id' => $data['tagid'],
				'title' => cutstr($data['tagname'], $titlelength, ''),
				'url' => 'misc.php?mod=tag&id='.$data['tagid'],
				'fields' => [
					'related_count' => intval($data['related_count']),
					'hot_score' => sprintf("%.2f", $data['hot_score']),
					'size_level' => intval($data['size_level']),
					'color_level' => intval($data['color_level']),
				],
			];
		}
		return ['html' => '', 'data' => $list];
	}
	private function process_tags_by_hot_score($tags) {
		
		if (empty($tags)) {
			return [];
		}

		
		$maxHotScore = PHP_INT_MIN;
		$minHotScore = PHP_INT_MAX;

		foreach ($tags as $tag) {
			$hotScore = (int)$tag['hot_score'];
			$maxHotScore = max($maxHotScore, $hotScore);
			$minHotScore = min($minHotScore, $hotScore);
		}

		
		if ($maxHotScore == $minHotScore) {
			foreach ($tags as &$tag) {
				$tag['size_level'] = 3;
				$tag['color_level'] = 3;
			}
			return $tags;
		}

		
		$range = $maxHotScore - $minHotScore;
		$interval = $range / 5;

		
		foreach ($tags as &$tag) {
			$hotScore = (int)$tag['hot_score'];

			
			if ($hotScore >= $minHotScore && $hotScore < $minHotScore + $interval) {
				$level = 1;
			} elseif ($hotScore < $minHotScore + $interval * 2) {
				$level = 2;
			} elseif ($hotScore < $minHotScore + $interval * 3) {
				$level = 3;
			} elseif ($hotScore < $minHotScore + $interval * 4) {
				$level = 4;
			} else {
				$level = 5;
			}

			
			$tag['size_level'] = $level;
			$tag['color_level'] = $level;
		}

		return $tags;
	}

}