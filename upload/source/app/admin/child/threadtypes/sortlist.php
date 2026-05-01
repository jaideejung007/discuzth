<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$optionid = $_GET['optionid'];
$option = table_forum_typeoption::t()->fetch($optionid);
include template('common/header');
$option['type'] = $lang['threadtype_edit_vars_type_'.$option['type']];
$option['available'] = 1;
showtablerow('', ['class="td25"', 'class="td28 td23"'], [
	"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"{$option['optionid']}\" ".($option['model'] ? 'disabled' : '').'>',
	"<input type=\"text\" class=\"txt\" size=\"2\" name=\"displayorder[{$option['optionid']}]\" value=\"{$option['displayorder']}\">",
	"<input class=\"checkbox\" type=\"checkbox\" name=\"available[{$option['optionid']}]\" value=\"1\" ".($option['available'] ? 'checked' : '').' '.($option['model'] ? 'disabled' : '').'>',
	dhtmlspecialchars($option['title']),
	$option['type'],
	"<input class=\"checkbox\" type=\"checkbox\" name=\"required[{$option['optionid']}]\" value=\"1\" ".($option['required'] ? 'checked' : '').' '.($option['model'] ? 'disabled' : '').'>',
	"<input class=\"checkbox\" type=\"checkbox\" name=\"unchangeable[{$option['optionid']}]\" value=\"1\" ".($option['unchangeable'] ? 'checked' : '').'>',
	"<input class=\"checkbox\" type=\"checkbox\" name=\"search[{$option['optionid']}][form]\" value=\"1\" ".(getstatus($option['search'], 1) == 1 ? 'checked' : '').'>',
	"<input class=\"checkbox\" type=\"checkbox\" name=\"search[{$option['optionid']}][font]\" value=\"1\" ".(getstatus($option['search'], 2) == 1 ? 'checked' : '').'>',
	"<input class=\"checkbox\" type=\"checkbox\" name=\"subjectshow[{$option['optionid']}]\" value=\"1\" ".($option['subjectshow'] ? 'checked' : '').'>',
	"<a href=\"".ADMINSCRIPT."?action=threadtypes&operation=optiondetail&optionid={$option['optionid']}\" class=\"act\">".$lang['edit'].'</a>'
]);
include template('common/footer');
exit;
	