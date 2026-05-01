<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class secqaa_letter {

	var $version = '1.0';
	var $name = 'letter_name';
	var $description = 'letter_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $customname = '';

	function make(&$question) {
		$s = [];
		for($i = 0; $i < 4; $i++) {
			$s[] = chr(rand(1, 26) + 64);
		}
		shuffle($s);
		$question = $s;
		$s = [];
		for($i = 0; $i < 4; $i++) {
			$s[] = rand(0, 9);
		}
		for($i = 0; $i < 4; $i++) {
			$s[] = chr(rand(1, 8) + 39);
		}
		shuffle($s);
		$s = array_slice($s, 0, 6);
		$question = array_merge($question, $s);
		shuffle($question);

		$answer = [];

		for($i = 0; $i < count($question); $i++) {
			$a = ord($question[$i]);
			if($a >= 64 && $a <= 90) {
				$answer[] = $question[$i];
			}
		}
		$question = lang('secqaa/letter', 'letter_note').': '.implode(' ', array_map([$this, '_entityChar'], $question));
		return strtolower(implode('', $answer));
	}

	function _entityChar($char) {
		return '&#'.str_pad(ord($char), 3, '0', STR_PAD_LEFT).';';
	}

}

