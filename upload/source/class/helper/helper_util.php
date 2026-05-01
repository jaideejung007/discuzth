<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class helper_util {

	public static function compute($v1, $v2, $glue = '+') {
		return match ($glue) {
			'+' => $v1 + $v2,
			'-' => $v1 - $v2,
			'.' => $v1.$v2,
			'=', '==' => $v1 == $v2,
			'merge' => array_merge((array)$v1, (array)$v2),
			'===', '!==' => $v1 === $v2,
			'&&', '||' => $v1 && $v2,
			'and' => $v1 and $v2,
			'xor' => $v1 xor $v2,
			'|' => $v1 | $v2,
			'&' => $v1 & $v2,
			'^' => $v1 ^ $v2,
			'>' => $v1 > $v2,
			'<' => $v1 < $v2,
			'<>' => $v1 <> $v2,
			'!=' => $v1 != $v2,
			'<=' => $v1 <= $v2,
			'>=' => $v1 >= $v2,
			'*' => $v1 * $v2,
			'/' => $v1 / $v2,
			'%' => $v1 % $v2,
			'or' => $v1 or $v2,
			'<<' => $v1 << $v2,
			'>>' => $v1 >> $v2,
			default => null,
		};
	}

	public static function single_compute($v, $glue = '+') {
		return match ($glue) {
			'!' => !$v,
			'-' => -$v,
			'~' => ~$v,
			default => null,
		};
	}

	public static function check_glue($glue = '=') {
		return in_array($glue, ['=', '<', '<=', '>', '>=', '!=', '+', '-', '|', '&', '<>']) ? $glue : '=';
	}

}

