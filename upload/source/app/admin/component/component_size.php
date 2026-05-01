<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace admin;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class component_size {

	var $name = '容量值';

	var $desc = 'KB,MB,GB';

	const unit = [
		'KB' => 1,
		'MB' => 1024,
		'GB' => 1048576,
	];

	function show(&$var, &$extra) {
		$var['type'] = $this->__show_sizeunits($var);
	}


	function __show_sizeunits($var) {
		$valueNew = $value = $var['value'];
		$extra = $var['extra'];
		if(empty($extra)) {
			$extra = ['KB', 'MB'];
		} else {
			$extra = explode(',', $extra);
		}
		$return = '<select style="width: 90px" name="'.$var['variable'].'[unit]">';
		$selected = false;
		foreach($extra as $unit) {
			if(!isset(self::unit[$unit])) {
				continue;
			}
			if($value && $value / self::unit[$unit] == intval($value / self::unit[$unit])) {
				$selected = $unit;
				$valueNew = $value / self::unit[$unit];
			}
		}
		foreach($extra as $unit) {
			if(!isset(self::unit[$unit])) {
				continue;
			}
			$return .= '<option value="'.$unit.'"'.($selected == $unit ? ' selected="selected"' : '').'>'.$unit.'</option>';
		}
		$return .= '</select>';
		return '<input name="'.$var['variable'].'[value]" type="text" style="width: 120px !important" class="txt" value="'.$valueNew.'" />'.$return;
	}

	function serialize(&$value) {
		if(!isset(self::unit[$value['unit']])) {
			$value = $value['value'];
		}
		$value = (int)$value['value'] * self::unit[$value['unit']];
	}

	function unserialize(&$value) {
	}

}