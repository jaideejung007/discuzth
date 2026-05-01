<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace admin;

use table_common_card;
use DB;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class class_perm {

	private static function _checkFormat($formula) {
		$formula = explode(' ', $formula);
		foreach($formula as $k => $v) {
			$v = trim($v);
			if($v === '' || $v == '(' || $v == ')' || $v == 'or' || $v == 'and') {
				continue;
			}
			$formula[$k] = 'abc';
		}
		$formula = implode(' ', $formula);
		return checkformulasyntax(
			$formula,
			['and', 'or'],
			['abc'],
			''
		);
	}

	public static function formulaCheck($permformula) {
		if(!$permformula) {
			return true;
		}
		global $_G;

		$check = true;
		$p = 0;
		$permformula = str_replace(['(', ')'], [' ( ', ' ) '], $permformula);
		if(!self::_checkFormat($permformula)) {
			return false;
		}
		$s = '';
		foreach(explode(' ', $permformula) as $c) {
			if(!$c) {
				continue;
			} elseif(preg_match('/^(group|tag|verify|account|or|and)$/', $c)) {
				if(in_array($c, ['group', 'tag', 'verify', 'account', 'medal', 'magic', 'org'])) {
					$s .= '$_c[\''.$c.'\'] ';
				} else {
					$s .= $c.' ';
				}
			} elseif(preg_match('/^(g|t|v|a)-?\d+$/', $c)) {
				$s .= '$_c[\''.$c.'\'] ';
			} elseif(preg_match('/^O-?(\d+)\[.*?\]$/', $c)) {
				$s .= '$_c[\''.$c.'\'] ';
			} elseif(str_starts_with($c, 'p_') && isset($_G['setting']['plugins']['perm'][substr($c, 2)])) {
				$s .= '$_c[\''.$c.'\'] ';
			} elseif(str_starts_with($c, 'plugin_') && in_array(substr($c, 7), $_G['setting']['plugins']['available'])) {
				$s .= '$_c[\''.$c.'\'] ';
			} elseif($c == '(') {
				$s .= '( ';
				$p++;
			} elseif($c == ')') {
				$s .= ') ';
				$p--;
			} else {
				$check = false;
			}
		}

		if(!$check || $p != 0) {
			return false;
		}
		return true;
	}

}