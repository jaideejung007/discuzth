<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class secqaa_calcmad {

	var $version = '1.0';
	var $name = 'calcmad_name';
	var $description = 'calcmad_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $customname = '';

	function make(&$question) {
		$a = rand(1, 9);
		$b = rand(1, 9);
		if(rand(0, 1)) {
			$question = $a.' * '.$b.' = ?';
			$answer = $a * $b;
		} else {
			$question = ($a * $b).' / '.$a.' = ?';
			$answer = $b;
		}
		return $answer;
	}

}